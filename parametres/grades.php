<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/header.php";

// --- Suppression ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id_grade = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: grades.php");
    exit();
}

// --- Ajout ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO grades (libelle_grade, categorie, indice_salarial) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['libelle'], $_POST['categorie'], $_POST['indice']]);
    header("Location: grades.php");
    exit();
}

// --- Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE grades SET libelle_grade=?, categorie=?, indice_salarial=? WHERE id_grade=?");
    $stmt->execute([$_POST['libelle'], $_POST['categorie'], $_POST['indice'], $_POST['id']]);
    header("Location: grades.php");
    exit();
}

// --- Liste ---
$grades = $pdo->query("SELECT * FROM grades ORDER BY id_grade DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Gestion des grades</h2>

    <!-- Bouton ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Ajouter un grade</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Libellé</th>
                <th>Catégorie</th>
                <th>Indice salarial</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $grd): ?>
                <tr>
                    <td><?= $grd['id_grade'] ?></td>
                    <td><?= htmlspecialchars($grd['libelle_grade']) ?></td>
                    <td><?= htmlspecialchars($grd['categorie']) ?></td>
                    <td><?= htmlspecialchars($grd['indice_salarial']) ?></td>
                    <td>
                        <!-- bouton modifier -->
                        <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal<?= $grd['id_grade'] ?>">
                            Modifier
                        </button>
                        <!-- bouton supprimer -->
                        <a href="?delete=<?= $grd['id_grade'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer ce grade ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $grd['id_grade'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier grade</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $grd['id_grade'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Libellé</label>
                            <input type="text" name="libelle" class="form-control" value="<?= htmlspecialchars($grd['libelle_grade']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars($grd['categorie']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Indice salarial</label>
                            <input type="number" name="indice" class="form-control" value="<?= htmlspecialchars($grd['indice_salarial']) ?>" required>
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
          <h5 class="modal-title">Ajouter un grade</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Libellé</label>
            <input type="text" name="libelle" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Catégorie</label>
            <input type="text" name="categorie" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Indice salarial</label>
            <input type="number" name="indice" class="form-control" required>
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