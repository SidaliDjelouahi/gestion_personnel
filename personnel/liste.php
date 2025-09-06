<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

// --- Suppression ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM personnel WHERE id_personnel = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: liste.php");
    exit();
}

// --- Ajout ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO personnel 
        (matricule, nom, prenom, date_naissance, lieu_naissance, adresse, telephone, email, etat_civil, sexe, date_recrutement, type_contrat, id_service) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['matricule'], $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'],
        $_POST['lieu_naissance'], $_POST['adresse'], $_POST['telephone'], $_POST['email'],
        $_POST['etat_civil'], $_POST['sexe'], $_POST['date_recrutement'],
        $_POST['type_contrat'], $_POST['id_service']
    ]);
    header("Location: liste.php");
    exit();
}

// --- Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare("UPDATE personnel SET 
        matricule=?, nom=?, prenom=?, date_naissance=?, lieu_naissance=?, adresse=?, telephone=?, email=?, etat_civil=?, sexe=?, date_recrutement=?, type_contrat=?, id_service=? 
        WHERE id_personnel=?");
    $stmt->execute([
        $_POST['matricule'], $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'],
        $_POST['lieu_naissance'], $_POST['adresse'], $_POST['telephone'], $_POST['email'],
        $_POST['etat_civil'], $_POST['sexe'], $_POST['date_recrutement'],
        $_POST['type_contrat'], $_POST['id_service'], $_POST['id']
    ]);
    header("Location: liste.php");
    exit();
}

// --- Liste ---
$personnels = $pdo->query("SELECT * FROM personnel ORDER BY id_personnel DESC")->fetchAll();

// Charger les services pour affichage du nom dans la liste
$services = $pdo->query("SELECT * FROM services ORDER BY id_service ASC")->fetchAll(PDO::FETCH_ASSOC);
$servicesMap = [];
foreach ($services as $srv) {
    $servicesMap[$srv['id_service']] = $srv['id_service'];
}

// ⚡ Design
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";
?>

<div class="container mt-4">
    <h2 class="mb-3">Gestion du personnel</h2>

    <!-- Bouton ajouter -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Ajouter un employé</button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>Lieu</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>État civil</th>
                <th>Sexe</th>
                <th>Date recrutement</th>
                <th>Contrat</th>
                <th>Service</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personnels as $pers): ?>
                <tr>
                    <td><?= $pers['id_personnel'] ?></td>
                    <td><?= htmlspecialchars($pers['matricule']) ?></td>
                    <td><?= htmlspecialchars($pers['nom']) ?></td>
                    <td><?= htmlspecialchars($pers['prenom']) ?></td>
                    <td><?= htmlspecialchars($pers['date_naissance']) ?></td>
                    <td><?= htmlspecialchars($pers['lieu_naissance']) ?></td>
                    <td><?= htmlspecialchars($pers['telephone']) ?></td>
                    <td><?= htmlspecialchars($pers['email']) ?></td>
                    <td><?= htmlspecialchars($pers['etat_civil']) ?></td>
                    <td><?= htmlspecialchars($pers['sexe']) ?></td>
                    <td><?= htmlspecialchars($pers['date_recrutement']) ?></td>
                    <td><?= htmlspecialchars($pers['type_contrat']) ?></td>
                    <td><?= $servicesMap[$pers['id_service']] ?? "N/A" ?></td>
                    <td>
                        <!-- bouton modifier -->
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $pers['id_personnel'] ?>">
                            Modifier
                        </button>
                        <!-- bouton supprimer -->
                        <a href="?delete=<?= $pers['id_personnel'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cet employé ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal<?= $pers['id_personnel'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Modifier employé</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row g-3">
                          <input type="hidden" name="id" value="<?= $pers['id_personnel'] ?>">
                          <div class="col-md-6">
                            <label class="form-label">Matricule</label>
                            <input type="text" name="matricule" class="form-control" value="<?= htmlspecialchars($pers['matricule']) ?>" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($pers['nom']) ?>" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($pers['prenom']) ?>" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($pers['date_naissance']) ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="form-control" value="<?= htmlspecialchars($pers['lieu_naissance']) ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($pers['adresse']) ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($pers['telephone']) ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($pers['email']) ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">État civil</label>
                            <input type="text" name="etat_civil" class="form-control" value="<?= htmlspecialchars($pers['etat_civil']) ?>">
                          </div>
                          <div class="col-md-6">
                              <label class="form-label">Sexe</label>
                              <select name="sexe" class="form-select" required>
                                  <option value="">-- Choisir --</option>
                                  <option value="Homme" <?= $pers['sexe']=="Homme"?"selected":"" ?>>Homme</option>
                                  <option value="Femme" <?= $pers['sexe']=="Femme"?"selected":"" ?>>Femme</option>
                              </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Date recrutement</label>
                            <input type="date" name="date_recrutement" class="form-control" value="<?= htmlspecialchars($pers['date_recrutement']) ?>">
                          </div>
                          <div class="col-md-6">
                              <label class="form-label">Type contrat</label>
                              <select name="type_contrat" class="form-select" required>
                                  <option value="">-- Choisir --</option>
                                  <option value="Titulaire" <?= $pers['type_contrat']=="Titulaire"?"selected":"" ?>>Titulaire</option>
                                  <option value="Contractuel" <?= $pers['type_contrat']=="Contractuel"?"selected":"" ?>>Contractuel</option>
                                  <option value="Vacataire" <?= $pers['type_contrat']=="Vacataire"?"selected":"" ?>>Vacataire</option>
                              </select>
                          </div>
                          <div class="col-md-12">
                            <label class="form-label">Service</label>
                            <select name="id_service" class="form-select" required>
                              <option value="">-- Choisir service --</option>
                              <?php foreach ($services as $srv): ?>
                                <option value="<?= $srv['id_service'] ?>" <?= $srv['id_service']==$pers['id_service']?"selected":"" ?>>
                                    <?= htmlspecialchars($srv['nom_service']) ?>
                                </option>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un employé</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Matricule</label>
            <input type="text" name="matricule" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Lieu de naissance</label>
            <input type="text" name="lieu_naissance" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">État civil</label>
            <input type="text" name="etat_civil" class="form-control">
          </div>
          <div class="col-md-6">
              <label class="form-label">Sexe</label>
              <select name="sexe" class="form-select" required>
                  <option value="">-- Choisir --</option>
                  <option value="Homme">Homme</option>
                  <option value="Femme">Femme</option>
              </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Date recrutement</label>
            <input type="date" name="date_recrutement" class="form-control">
          </div>
          <div class="col-md-6">
              <label class="form-label">Type contrat</label>
              <select name="type_contrat" class="form-select" required>
                  <option value="">-- Choisir --</option>
                  <option value="Titulaire">Titulaire</option>
                  <option value="Contractuel">Contractuel</option>
                  <option value="Vacataire">Vacataire</option>
              </select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Service</label>
            <select name="id_service" class="form-select" required>
              <option value="">-- Choisir service --</option>
              <?php foreach ($services as $srv): ?>
                <option value="<?= $srv['id_service'] ?>"><?= htmlspecialchars($srv['nom_service']) ?></option>
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
