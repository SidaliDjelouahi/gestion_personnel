<?php
session_start();
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

$message = '';

// Chargement des paramètres actuels (à implémenter dans une table parameters)
$parametres = [
    'valeur_point_indice' => 45.00,  // Valeur du point d'indice
    'taux_irg' => 15,                // Taux IRG (%)
    'taux_ss' => 9,                  // Taux Sécurité Sociale (%)
    'plafond_heures_sup' => 20,      // Nombre max d'heures sup par mois
    'majoration_heure_sup' => 25     // Majoration heures sup (%)
];

// Traitement de la mise à jour des paramètres
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // TODO: Sauvegarder dans une table parameters
    $message = "Paramètres mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres de Paie - Gestion du Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Paramètres de Paie</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Paramètres généraux -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Paramètres généraux</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Valeur du point d'indice</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="valeur_point_indice" 
                                           class="form-control" required
                                           value="<?= $parametres['valeur_point_indice'] ?>">
                                    <span class="input-group-text">DA</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Taux IRG</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="taux_irg" 
                                           class="form-control" required
                                           value="<?= $parametres['taux_irg'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Taux Sécurité Sociale</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="taux_ss" 
                                           class="form-control" required
                                           value="<?= $parametres['taux_ss'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <button type="submit" name="update" class="btn btn-primary">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Paramètres heures supplémentaires -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Heures supplémentaires</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Plafond mensuel</label>
                                <div class="input-group">
                                    <input type="number" name="plafond_heures_sup" 
                                           class="form-control" required
                                           value="<?= $parametres['plafond_heures_sup'] ?>">
                                    <span class="input-group-text">heures</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Taux de majoration</label>
                                <div class="input-group">
                                    <input type="number" name="majoration_heure_sup" 
                                           class="form-control" required
                                           value="<?= $parametres['majoration_heure_sup'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <button type="submit" name="update" class="btn btn-primary">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barème IRG -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Barème IRG</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tranche de</th>
                            <th>à</th>
                            <th>Taux</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>0 DA</td>
                            <td>30 000 DA</td>
                            <td>0%</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Modifier</button>
                            </td>
                        </tr>
                        <tr>
                            <td>30 001 DA</td>
                            <td>120 000 DA</td>
                            <td>20%</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Modifier</button>
                            </td>
                        </tr>
                        <tr>
                            <td>120 001 DA</td>
                            <td>∞</td>
                            <td>35%</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Modifier</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-success">Ajouter une tranche</button>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>