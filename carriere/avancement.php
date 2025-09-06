<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

// --- Traitement validation d’un avancement ---
if (isset($_POST['valider_avancement'])) {
    $id_personnel = $_POST['id_personnel'];
    $id_grade     = $_POST['id_grade']; // nouveau grade
    $date_debut   = date("Y-m-d");      // début aujourd’hui
    $type         = "Avancement automatique";
    $obs          = "Avancement validé par RH (semi-auto)";

    // On clôture l’ancien mouvement (date_fin)
    $pdo->prepare("UPDATE carriere 
                   SET date_fin = ? 
                   WHERE id_personnel = ? AND date_fin IS NULL")
        ->execute([$date_debut, $id_personnel]);

    // On ajoute un nouvel enregistrement carrière
    $stmt = $pdo->prepare("INSERT INTO carriere 
        (id_personnel, id_grade, date_debut, type_mouvement, observation) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_personnel, $id_grade, $date_debut, $type, $obs]);

    $_SESSION['message'] = ["type" => "success", "text" => "Avancement validé avec succès"];
    header("Location: avancement.php");
    exit();
}

// --- Récupérer les dernières carrières ---
$sql = "SELECT c.*, p.nom, p.prenom, g.libelle_grade
        FROM carriere c
        JOIN personnel p ON p.id_personnel = c.id_personnel
        JOIN grades g ON g.id_grade = c.id_grade
        WHERE c.date_fin IS NULL
        ORDER BY c.date_debut ASC";
$carrieres = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// --- Récupérer les règles ---
$regles = $pdo->query("SELECT * FROM regles_carriere")->fetchAll(PDO::FETCH_ASSOC);
$regles_by_grade = [];
foreach ($regles as $r) {
    $regles_by_grade[$r['condition_grade']][] = $r;
}
?>

<div class="container mt-4">
    <h2 class="mb-3">Avancements – Mode semi-automatique</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Employé</th>
                <th>Grade actuel</th>
                <th>Date début</th>
                <th>Durée (années)</th>
                <th>Éligibilité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($carrieres as $c): 
            $eligible = false;
            $regle_ok = null;

            if (isset($regles_by_grade[$c['id_grade']])) {
                foreach ($regles_by_grade[$c['id_grade']] as $r) {
                    $date_debut = new DateTime($c['date_debut']);
                    $now = new DateTime();
                    $interval = $date_debut->diff($now)->y; // années passées

                    if ($interval >= $r['duree_annee']) {
                        $eligible = true;
                        $regle_ok = $r;
                        break;
                    }
                }
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($c['nom'] . " " . $c['prenom']) ?></td>
                <td><?= htmlspecialchars($c['nom_grade']) ?></td>
                <td><?= htmlspecialchars($c['date_debut']) ?></td>
                <td>
                    <?= isset($regle_ok) ? $regle_ok['duree_annee']." ans" : "-" ?>
                </td>
                <td>
                    <?php if ($eligible): ?>
                        <span class="badge bg-success">Éligible</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Non</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($eligible && $regle_ok): ?>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id_personnel" value="<?= $c['id_personnel'] ?>">
                            <input type="hidden" name="id_grade" value="<?= $regle_ok['condition_grade']+1 ?>"> 
                            <!-- Ici j’ai mis +1 pour simuler le grade suivant -->
                            <button type="submit" name="valider_avancement" class="btn btn-sm btn-primary">
                                Valider
                            </button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>

