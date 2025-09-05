<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

// --- Suppression ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: categories.php");
    exit();
}

// --- Ajout ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO categories (nom_categorie, description) VALUES (?, ?)");
    $stmt->execute([$_POST['nom'], $_POST['description']]);
    header("Location: categories.php");
    exit();
}

// --- Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE categories SET nom_categorie=?, description=? WHERE id_categorie=?");
    $stmt->execute([$_POST['nom'], $_POST['description'], $_POST['id']]);
    header("Location: categories.php");
    exit();
}

// --- Liste ---
$categories = $pdo->query("SELECT * FROM categories ORDER BY id_categorie DESC")->fetchAll();

// Inclure header et sidebar seulement après traitements
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Gestion des catégories</h2>

    <!-- Bouton ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
        + Ajouter une catégorie
    </button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id_categorie'] ?></td>
                    <td><?= htmlspecialchars($cat['nom_categorie']) ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td>
                        <!-- bouton modifier -->
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $cat['id_categorie'] ?>">
                            Modifier
                        </button>
                        <!-- bouton supprimer -->
                        <a href="?delete=<?= $cat['id_categorie'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cette catégorie ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $cat['id_categorie'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier catégorie</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $cat['id_categorie'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" 
                                   value="<?= htmlspecialchars($cat['nom_categorie']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"><?= htmlspecialchars($cat['description']) ?></textarea>
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
          <h5 class="modal-title">Ajouter une catégorie</h5>
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
