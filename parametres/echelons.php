<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

// --- Suppression ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM echelons WHERE id_echelon = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: echelons.php");
    exit();
}

// --- Ajout ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO echelons (nom_echelon, indice, description) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nom'], $_POST['indice'], $_POST['description']]);
    header("Location: echelons.php");
    exit();
}

// --- Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE echelons SET nom_echelon=?, indice=?, description=? WHERE id_echelon=?");
    $stmt->execute([$_POST['nom'], $_POST['indice'], $_POST['description'], $_POST['id']]);
    header("Location: echelons.php");
    exit();
}

// --- Liste ---
$echelons = $pdo->query("SELECT * FROM echelons ORDER BY id_echelon DESC")->fetchAll();

// ⚡ Design seulement après traitements
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Échelons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Gestion des échelons</h2>

    <!-- Bouton ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Ajouter un échelon</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Indice</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($echelons as $ech): ?>
                <tr>
                    <td><?= $ech['id_echelon'] ?></td>
                    <td><?= htmlspecialchars($ech['nom_echelon']) ?></td>
                    <td><?= htmlspecialchars($ech['indice']) ?></td>
                    <td><?= htmlspecialchars($ech['description']) ?></td>
                    <td>
                        <!-- bouton modifier -->
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $ech['id_echelon'] ?>">
                            Modifier
                        </button>
                        <!-- bouton supprimer -->
                        <a href="?delete=<?= $ech['id_echelon'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cet échelon ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $ech['id_echelon'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier échelon</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $ech['id_echelon'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($ech['nom_echelon']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Indice</label>
                            <input type="number" name="indice" class="form-control" value="<?= htmlspecialchars($ech['indice']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"><?= htmlspecialchars($ech['description']) ?></textarea>
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
          <h5 class="modal-title">Ajouter un échelon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Indice</label>
            <input type="number" name="indice" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
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
