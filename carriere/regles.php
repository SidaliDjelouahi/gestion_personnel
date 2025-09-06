<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

// --- Supprimer ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM regle_carriere WHERE id_regle = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: regles.php");
    exit();
}

// --- Ajouter ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO regle_carriere (intitule, description, duree_annee, condition_grade) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['intitule'],
        $_POST['description'],
        $_POST['duree_annee'],
        $_POST['condition_grade']
    ]);
    header("Location: regles.php");
    exit();
}

// --- Modifier ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE regle_carriere SET intitule=?, description=?, duree_annee=?, condition_grade=? WHERE id_regle=?");
    $stmt->execute([
        $_POST['intitule'],
        $_POST['description'],
        $_POST['duree_annee'],
        $_POST['condition_grade'],
        $_POST['id']
    ]);
    header("Location: regles.php");
    exit();
}

// --- Liste ---
$regles = $pdo->query("SELECT * FROM regles_carriere ORDER BY id_regle DESC")->fetchAll(PDO::FETCH_ASSOC);

// Charger les grades pour les sélectionner dans le formulaire
$grades = $pdo->query("SELECT * FROM grades ORDER BY id_grade ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";
?>

<div class="container mt-4">
    <h2 class="mb-3">Gestion des règles d’avancement</h2>

    <!-- Bouton Ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Ajouter une règle</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Intitulé</th>
                <th>Description</th>
                <th>Durée (années)</th>
                <th>Grade concerné</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($regles as $regle): ?>
                <tr>
                    <td><?= $regle['id_regle'] ?></td>
                    <td><?= htmlspecialchars($regle['intitule']) ?></td>
                    <td><?= htmlspecialchars($regle['description']) ?></td>
                    <td><?= htmlspecialchars($regle['duree_annee']) ?></td>
                    <td>
                        <?php
                        $gradeNom = "N/A";
                        foreach ($grades as $g) {
                            if ($g['id_grade'] == $regle['condition_grade']) {
                                $gradeNom = htmlspecialchars($g['nom_grade']);
                                break;
                            }
                        }
                        echo $gradeNom;
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $regle['id_regle'] ?>">Modifier</button>
                        <a href="?delete=<?= $regle['id_regle'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette règle ?');">Supprimer</a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $regle['id_regle'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier la règle</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $regle['id_regle'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Intitulé</label>
                            <input type="text" name="intitule" class="form-control" value="<?= htmlspecialchars($regle['intitule']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"><?= htmlspecialchars($regle['description']) ?></textarea>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Durée (années)</label>
                            <input type="number" name="duree_annee" class="form-control" value="<?= htmlspecialchars($regle['duree_annee']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Grade concerné</label>
                            <select name="condition_grade" class="form-select" required>
                              <option value="">-- Choisir grade --</option>
                              <?php foreach ($grades as $g): ?>
                                <option value="<?= $g['id_grade'] ?>" <?= $g['id_grade']==$regle['condition_grade']?"selected":"" ?>><?= htmlspecialchars($g['nom_grade']) ?></option>
                              <?php endforeach; ?>
                            </select>
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
          <h5 class="modal-title">Ajouter une règle d’avancement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Intitulé</label>
            <input type="text" name="intitule" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Durée (années)</label>
            <input type="number" name="duree_annee" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade concerné</label>
            <select name="condition_grade" class="form-select" required>
              <option value="">-- Choisir grade --</option>
              <?php foreach ($grades as $g): ?>
                <option value="<?= $g['id_grade'] ?>"><?= htmlspecialchars($g['libelle_grade']) ?></option>
              <?php endforeach; ?>
            </select>
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

