<?php
// index.php (dashboard principal)
session_start();
require_once "includes/db.php";    // connexion DB
require_once "includes/header.php";
require_once "includes/sidebar.php";


// --- Statistiques globales ---
$total_personnel = $pdo->query("SELECT COUNT(*) FROM personnel")->fetchColumn();
$total_services  = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$total_absences  = $pdo->query("SELECT COUNT(*) FROM absences")->fetchColumn();
$total_conges    = $pdo->query("SELECT COUNT(*) FROM conges")->fetchColumn();

// --- Masse salariale (exemple simple : somme salaires bruts) ---
$total_salaire = $pdo->query("SELECT SUM(salaire_base) FROM paie")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Gestion du Personnel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
  <h1 class="mb-4">Tableau de bord</h1>

  <div class="row">
    <!-- Carte : Effectif -->
    <div class="col-md-3">
      <div class="card text-bg-primary shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Effectif</h5>
          <p class="display-6"><?= $total_personnel ?></p>
          <a href="personnel/liste.php" class="btn btn-light btn-sm">Voir détails</a>
        </div>
      </div>
    </div>

    <!-- Carte : Services -->
    <div class="col-md-3">
      <div class="card text-bg-success shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Services</h5>
          <p class="display-6"><?= $total_services ?></p>
          <a href="parametres/services.php" class="btn btn-light btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <!-- Carte : Absences -->
    <div class="col-md-3">
      <div class="card text-bg-warning shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Absences</h5>
          <p class="display-6"><?= $total_absences ?></p>
          <a href="absences/index.php" class="btn btn-light btn-sm">Voir</a>
        </div>
      </div>
    </div>

    <!-- Carte : Congés -->
    <div class="col-md-3">
      <div class="card text-bg-danger shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Congés</h5>
          <p class="display-6"><?= $total_conges ?></p>
          <a href="conges/index.php" class="btn btn-light btn-sm">Voir</a>
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
          <a href="paie/masse.php" class="btn btn-outline-primary btn-sm">Détails</a>
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

</div>

<?php require_once "includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
