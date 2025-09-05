<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

// --- TRAITEMENTS ---
// Suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM primes WHERE id_prime = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: primes.php");
    exit();
}

// Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO primes (nom_prime, description, formule_calcul) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nom'], $_POST['description'], $_POST['formule']]);
    header("Location: primes.php");
    exit();
}

// Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE primes SET nom_prime=?, description=?, formule_calcul=? WHERE id_prime=?");
    $stmt->execute([$_POST['nom'], $_POST['description'], $_POST['formule'], $_POST['id']]);
    header("Location: primes.php");
    exit();
}

// --- Liste ---
$primes = $pdo->query("SELECT * FROM primes ORDER BY id_prime DESC")->fetchAll();

// --- AFFICHAGE ---
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Primes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Gestion des primes</h2>

    <!-- Bouton ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Ajouter une prime</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Formule de calcul</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($primes as $prm): ?>
                <tr>
                    <td><?= $prm['id_prime'] ?></td>
                    <td><?= htmlspecialchars($prm['nom_prime']) ?></td>
                    <td><?= htmlspecialchars($prm['description']) ?></td>
                    <td><?= htmlspecialchars($prm['formule_calcul']) ?></td>
                    <td>
                        <!-- bouton modifier -->
                        <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal<?= $prm['id_prime'] ?>">
                            Modifier
                        </button>
                        <!-- bouton supprimer -->
                        <a href="?delete=<?= $prm['id_prime'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cette prime ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $prm['id_prime'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier prime</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $prm['id_prime'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($prm['nom_prime']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"><?= htmlspecialchars($prm['description']) ?></textarea>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Formule de calcul</label>
                            <input type="text" name="formule" class="form-control" value="<?= htmlspecialchars($prm['formule_calcul']) ?>">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="edit" class="btn btn-success">Enregistrer</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal ajout -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter une prime</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Formule de calcul</label>
            <input type="text" name="formule" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
