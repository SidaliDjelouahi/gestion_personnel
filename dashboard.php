<?php
// index.php (dashboard principal)
session_start();
require_once __DIR__ . "/includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

// --- Statistiques globales ---
// use centralized safe_count helper (returns 0 if table missing)
$total_personnel = function_exists('safe_count') ? safe_count($pdo, 'personnel') : @($pdo->query("SELECT COUNT(*) FROM personnel")->fetchColumn() ?: 0);
$total_services  = function_exists('safe_count') ? safe_count($pdo, 'services') : @($pdo->query("SELECT COUNT(*) FROM services")->fetchColumn() ?: 0);
$total_absences  = function_exists('safe_count') ? safe_count($pdo, 'absences') : @($pdo->query("SELECT COUNT(*) FROM absences")->fetchColumn() ?: 0);
$total_conges    = function_exists('safe_count') ? safe_count($pdo, 'conges') : @($pdo->query("SELECT COUNT(*) FROM conges")->fetchColumn() ?: 0);

// --- Masse salariale ---
$total_salaire = $pdo->query("SELECT SUM(salaire_base) FROM paie")->fetchColumn();
?>
  <h1 class="mb-4">Tableau de bord</h1>

  <div class="row">
    <!-- Carte : Effectif -->
    <div class="col-md-3">
      <div class="card text-bg-primary shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Effectif</h5>
          <p class="display-6"><?= $total_personnel ?></p>
          <a href="<?= ROOT_URL ?>/personnel/liste.php" class="btn btn-light btn-sm">Voir détails</a>
        </div>
      </div>
    </div>

    <!-- Carte : Services -->
    <div class="col-md-3">
      <div class="card text-bg-success shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Services</h5>
          <p class="display-6"><?= $total_services ?></p>
          <a href="<?= ROOT_URL ?>/parametres/services.php" class="btn btn-light btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <!-- Carte : Absences -->
    <div class="col-md-3">
      <div class="card text-bg-warning shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Absences</h5>
          <p class="display-6"><?= $total_absences ?></p>
          <a href="<?= ROOT_URL ?>/absences/index.php" class="btn btn-light btn-sm">Voir</a>
        </div>
      </div>
    </div>

    <!-- Carte : Congés -->
    <div class="col-md-3">
      <div class="card text-bg-danger shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Congés</h5>
          <p class="display-6"><?= $total_conges ?></p>
          <a href="<?= ROOT_URL ?>/conges/index.php" class="btn btn-light btn-sm">Voir</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bloc masse salariale -->
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Masse salariale</h5>
          <p class="fs-3 text-primary"><?= number_format($total_salaire, 2, ',', ' ') ?> DA</p>
          <a href="<?= ROOT_URL ?>/paie/masse.php" class="btn btn-outline-primary btn-sm">Détails</a>
        </div>
      </div>
    </div>

    <!-- Bloc alertes -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title text-danger">Alertes</h5>
          <ul class="list-group">
            <li class="list-group-item">🔔 3 congés en attente de validation</li>
            <li class="list-group-item">⚠️ 2 contrats expirent ce mois</li>
            <li class="list-group-item">❌ 1 absence non justifiée</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

<?php require_once ROOT_PATH . "/includes/footer.php"; ?>
