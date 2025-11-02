<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

$message = '';

// --- Récupération des employés pour le formulaire ---
$personnel = $pdo->query("SELECT id_personnel, matricule, nom, prenom FROM personnel ORDER BY nom, prenom")->fetchAll();

// --- Traitement du formulaire de création/modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generer'])) {
        // Vérifier si une fiche existe déjà pour ce mois
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM paie WHERE id_personnel = ? AND mois = ? AND annee = ?");
        $stmt->execute([$_POST['id_personnel'], $_POST['mois'], $_POST['annee']]);
        
        if ($stmt->fetchColumn() > 0) {
            $message = "Une fiche de paie existe déjà pour cet employé ce mois-ci.";
        } else {
            // Récupérer le grade et l'échelon actuel
            $sql = "SELECT g.indice_salarial, e.indice 
                    FROM personnel p 
                    JOIN carriere c ON c.id_personnel = p.id_personnel 
                    JOIN grades g ON g.id_grade = c.id_grade
                    JOIN echelons e ON e.id_echelon = p.id_echelon
                    WHERE p.id_personnel = ? 
                    AND c.date_debut <= CURRENT_DATE 
                    AND (c.date_fin IS NULL OR c.date_fin >= CURRENT_DATE)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['id_personnel']]);
            $indices = $stmt->fetch();
            
            // Calcul du salaire de base
            $salaire_base = $indices['indice_salarial'] * $indices['indice'];
            
            // Récupération des primes
            $sql_primes = "SELECT pp.valeur, p.formule_calcul 
                          FROM personnel_primes pp
                          JOIN primes p ON p.id_prime = pp.id_prime
                          WHERE pp.id_personnel = ?";
            
            $stmt = $pdo->prepare($sql_primes);
            $stmt->execute([$_POST['id_personnel']]);
            $primes = $stmt->fetchAll();
            
            $total_primes = 0;
            foreach ($primes as $prime) {
                // TODO: Évaluer la formule de calcul
                $total_primes += $prime['valeur'];
            }
            
            // Calcul des retenues (à adapter selon vos règles)
            $retenues = ($salaire_base + $total_primes) * 0.15; // 15% de retenues
            
            // Insertion de la fiche de paie
            $stmt = $pdo->prepare("INSERT INTO paie (id_personnel, mois, annee, salaire_base, indemnites, retenues, net_a_payer) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $net = $salaire_base + $total_primes - $retenues;
            
            $stmt->execute([
                $_POST['id_personnel'],
                $_POST['mois'],
                $_POST['annee'],
                $salaire_base,
                $total_primes,
                $retenues,
                $net
            ]);
            
            $message = "Fiche de paie générée avec succès.";
        }
    }
}

// --- Liste des fiches de paie du mois en cours ---
$mois_courant = date('m');
$annee_courante = date('Y');

$sql = "SELECT p.*, pe.matricule, pe.nom, pe.prenom 
        FROM paie p
        JOIN personnel pe ON pe.id_personnel = p.id_personnel
        WHERE p.mois = ? AND p.annee = ?
        ORDER BY pe.nom, pe.prenom";

$stmt = $pdo->prepare($sql);
$stmt->execute([$mois_courant, $annee_courante]);
$fiches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Paies - Gestion du Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Gestion des Paies</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulaire de génération -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Générer une fiche de paie</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Employé</label>
                        <select name="id_personnel" class="form-select" required>
                            <option value="">Sélectionner un employé</option>
                            <?php foreach($personnel as $emp): ?>
                                <option value="<?= $emp['id_personnel'] ?>">
                                    <?= $emp['matricule'] ?> - <?= $emp['nom'] ?> <?= $emp['prenom'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mois</label>
                        <select name="mois" class="form-select" required>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == $mois_courant ? 'selected' : '' ?>>
                                    <?= strftime('%B', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Année</label>
                        <select name="annee" class="form-select" required>
                            <?php for($a=2020; $a<=2030; $a++): ?>
                                <option value="<?= $a ?>" <?= $a == $annee_courante ? 'selected' : '' ?>>
                                    <?= $a ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" name="generer" class="btn btn-primary d-block">Générer la fiche</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des fiches du mois -->
        <div class="card">
            <div class="card-header">
                <h5>Fiches de paie du mois en cours</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Salaire Base</th>
                            <th>Indemnités</th>
                            <th>Retenues</th>
                            <th>Net à payer</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($fiches as $fiche): ?>
                            <tr>
                                <td><?= $fiche['matricule'] ?></td>
                                <td><?= $fiche['nom'] ?></td>
                                <td><?= $fiche['prenom'] ?></td>
                                <td><?= number_format($fiche['salaire_base'], 2, ',', ' ') ?> DA</td>
                                <td><?= number_format($fiche['indemnites'], 2, ',', ' ') ?> DA</td>
                                <td><?= number_format($fiche['retenues'], 2, ',', ' ') ?> DA</td>
                                <td><?= number_format($fiche['net_a_payer'], 2, ',', ' ') ?> DA</td>
                                <td>
                                    <a href="fiche_paie.php?id=<?= $fiche['id_paie'] ?>" 
                                       class="btn btn-sm btn-primary">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>