<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

$message = '';

// --- Traitement des actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'une prime
    if (isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare("INSERT INTO primes (nom_prime, description, formule_calcul) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['nom_prime'],
            $_POST['description'],
            $_POST['formule_calcul']
        ]);
        $message = "Prime ajoutée avec succès.";
    }
    
    // Attribution d'une prime à un employé
    if (isset($_POST['attribuer'])) {
        // Vérifier si déjà attribuée
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM personnel_primes WHERE id_personnel = ? AND id_prime = ?");
        $stmt->execute([$_POST['id_personnel'], $_POST['id_prime']]);
        
        if ($stmt->fetchColumn() > 0) {
            // Mise à jour
            $stmt = $pdo->prepare("UPDATE personnel_primes SET valeur = ? WHERE id_personnel = ? AND id_prime = ?");
            $stmt->execute([
                $_POST['valeur'],
                $_POST['id_personnel'],
                $_POST['id_prime']
            ]);
        } else {
            // Nouvelle attribution
            $stmt = $pdo->prepare("INSERT INTO personnel_primes (id_personnel, id_prime, valeur) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['id_personnel'],
                $_POST['id_prime'],
                $_POST['valeur']
            ]);
        }
        $message = "Prime attribuée avec succès.";
    }

    // Suppression d'une prime
    if (isset($_POST['supprimer'])) {
        $stmt = $pdo->prepare("DELETE FROM primes WHERE id_prime = ?");
        $stmt->execute([$_POST['id_prime']]);
        $message = "Prime supprimée avec succès.";
    }
}

// --- Récupération des données ---
$primes = $pdo->query("SELECT * FROM primes ORDER BY nom_prime")->fetchAll();
$personnel = $pdo->query("SELECT id_personnel, matricule, nom, prenom FROM personnel ORDER BY nom, prenom")->fetchAll();

// Récupération des attributions de primes
$sql = "SELECT pp.*, p.matricule, p.nom, p.prenom, pr.nom_prime 
        FROM personnel_primes pp
        JOIN personnel p ON p.id_personnel = pp.id_personnel
        JOIN primes pr ON pr.id_prime = pp.id_prime
        ORDER BY p.nom, p.prenom, pr.nom_prime";
$attributions = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Primes - Gestion du Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Gestion des Primes</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulaire d'ajout de prime -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Ajouter une prime</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Nom de la prime</label>
                                <input type="text" name="nom_prime" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Formule de calcul</label>
                                <input type="text" name="formule_calcul" class="form-control" 
                                       placeholder="Ex: salaire_base * 0.1">
                            </div>
                            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Attribution de prime -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Attribuer une prime</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label class="form-label">Prime</label>
                                <select name="id_prime" class="form-select" required>
                                    <option value="">Sélectionner une prime</option>
                                    <?php foreach($primes as $prime): ?>
                                        <option value="<?= $prime['id_prime'] ?>">
                                            <?= $prime['nom_prime'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Montant/Valeur</label>
                                <input type="number" step="0.01" name="valeur" class="form-control" required>
                            </div>
                            <button type="submit" name="attribuer" class="btn btn-primary">Attribuer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des primes -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Liste des primes</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Formule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($primes as $prime): ?>
                            <tr>
                                <td><?= $prime['nom_prime'] ?></td>
                                <td><?= $prime['description'] ?></td>
                                <td><?= $prime['formule_calcul'] ?></td>
                                <td>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="id_prime" value="<?= $prime['id_prime'] ?>">
                                        <button type="submit" name="supprimer" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Êtes-vous sûr ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Liste des attributions -->
        <div class="card">
            <div class="card-header">
                <h5>Primes attribuées</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Prime</th>
                            <th>Valeur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($attributions as $attr): ?>
                            <tr>
                                <td><?= $attr['matricule'] ?> - <?= $attr['nom'] ?> <?= $attr['prenom'] ?></td>
                                <td><?= $attr['nom_prime'] ?></td>
                                <td><?= number_format($attr['valeur'], 2, ',', ' ') ?> DA</td>
                                <td>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="id_personnel" value="<?= $attr['id_personnel'] ?>">
                                        <input type="hidden" name="id_prime" value="<?= $attr['id_prime'] ?>">
                                        <button type="submit" name="supprimer_attribution" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Êtes-vous sûr ?')">
                                            Supprimer
                                        </button>
                                    </form>
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