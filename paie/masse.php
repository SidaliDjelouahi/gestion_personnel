<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

// --- Calcul de la masse salariale par mois/année ---
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');

$sql = "SELECT 
            SUM(p.salaire_base) as total_base,
            SUM(p.indemnites) as total_indemnites,
            SUM(p.retenues) as total_retenues,
            SUM(p.net_a_payer) as total_net,
            COUNT(*) as nb_employes
        FROM paie p 
        WHERE p.annee = ? AND p.mois = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$annee, $mois]);
$totaux = $stmt->fetch(PDO::FETCH_ASSOC);

// --- Détail par service ---
$sql_services = "SELECT 
                    s.nom_service,
                    COUNT(DISTINCT p.id_personnel) as nb_employes,
                    SUM(p.salaire_base) as total_service
                FROM paie p
                JOIN personnel pe ON pe.id_personnel = p.id_personnel
                JOIN services s ON s.id_service = pe.id_service
                WHERE p.annee = ? AND p.mois = ?
                GROUP BY s.id_service";

$stmt = $pdo->prepare($sql_services);
$stmt->execute([$annee, $mois]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Masse Salariale - Gestion du Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Masse Salariale</h1>

        <!-- Sélection période -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-auto">
                        <select name="mois" class="form-select">
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == $mois ? 'selected' : '' ?>>
                                    <?= strftime('%B', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="annee" class="form-select">
                            <?php for($a=2020; $a<=2030; $a++): ?>
                                <option value="<?= $a ?>" <?= $a == $annee ? 'selected' : '' ?>>
                                    <?= $a ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Afficher</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Récapitulatif global -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Brut</h5>
                        <p class="display-6"><?= number_format($totaux['total_base'], 2, ',', ' ') ?> DA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Net</h5>
                        <p class="display-6"><?= number_format($totaux['total_net'], 2, ',', ' ') ?> DA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Indemnités</h5>
                        <p class="display-6"><?= number_format($totaux['total_indemnites'], 2, ',', ' ') ?> DA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Retenues</h5>
                        <p class="display-6"><?= number_format($totaux['total_retenues'], 2, ',', ' ') ?> DA</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détail par service -->
        <div class="card">
            <div class="card-header">
                <h5>Répartition par service</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Nombre d'employés</th>
                            <th>Masse salariale</th>
                            <th>Moyenne/employé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($services as $service): ?>
                            <tr>
                                <td><?= $service['nom_service'] ?></td>
                                <td><?= $service['nb_employes'] ?></td>
                                <td><?= number_format($service['total_service'], 2, ',', ' ') ?> DA</td>
                                <td>
                                    <?= number_format($service['total_service'] / $service['nb_employes'], 2, ',', ' ') ?> DA
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