<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

// Initialisation
$historique = [];
$employe = null;

// Charger historique si id_personnel fourni
if (!empty($_GET['id_personnel'])) {
    $id_personnel = intval($_GET['id_personnel']);

    // Récup info employé
    $stmt = $pdo->prepare("SELECT * FROM personnel WHERE id_personnel = ?");
    $stmt->execute([$id_personnel]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    // Charger historique carrière
    $sql = "SELECT c.*, g.libelle_grade, s.nom_service, p.nom, p.prenom
            FROM carriere c
            LEFT JOIN grades g ON c.id_grade = g.id_grade
            LEFT JOIN services s ON c.id_service = s.id_service
            INNER JOIN personnel p ON c.id_personnel = p.id_personnel
            WHERE c.id_personnel = ?
            ORDER BY c.date_debut DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_personnel]);
    $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <h2 class="mb-3"><i class="fa fa-history"></i> Historique de carrière</h2>

    <!-- 🔎 Recherche employé -->
    <form method="get" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6 position-relative">
                <input type="text" id="searchEmploye" class="form-control" placeholder="Rechercher employé (nom ou matricule)">
                <div id="resultsEmploye" class="list-group position-absolute w-100" style="z-index:1000;"></div>
                <input type="hidden" name="id_personnel" id="id_personnel" value="<?= htmlspecialchars($_GET['id_personnel'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Voir l’historique
                </button>
            </div>
        </div>
    </form>

    <?php if ($employe): ?>
        <div class="alert alert-info">
            <strong>Employé :</strong> <?= htmlspecialchars($employe['nom'] . " " . $employe['prenom']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($historique)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Type de mouvement</th>
                        <th>Grade</th>
                        <th>Service / Affectation</th>
                        <th>Observation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date_debut']) ?></td>
                            <td><?= $row['date_fin'] ? htmlspecialchars($row['date_fin']) : "<span class='badge bg-success'>En cours</span>" ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($row['type_mouvement']) ?></span></td>
                            <td><?= htmlspecialchars($row['libelle_grade'] ?? "—") ?></td>
                            <td><?= htmlspecialchars($row['nom_service'] ?? "—") ?></td>
                            <td><?= htmlspecialchars($row['observation']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (!empty($_GET['id_personnel'])): ?>
        <div class="alert alert-warning">Aucun historique trouvé pour cet employé.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function(){
    $("#searchEmploye").keyup(function(){
        let query = $(this).val();
        if(query.length > 1){
            $.ajax({
                url: "../carriere/recherche_employe.php",
                method: "GET",
                data: {q: query},
                success: function(data) {
                    let results = JSON.parse(data);
                    let html = "";
                    results.forEach(item => {
                        let label = item.matricule + " - " + item.nom + " " + item.prenom;
                        html += `<div class="list-group-item list-group-item-action employe-item" 
                                     data-id="${item.id_personnel}" 
                                     data-label="${label}">
                                     ${label}
                                 </div>`;
                    });
                    $("#resultsEmploye").html(html).show();
                }

            });
        } else {
            $("#resultsEmploye").hide();
        }
    });

    // Quand on clique sur un résultat
    $(document).on("click", ".employe-item", function(){
        let id = $(this).data("id");
        let label = $(this).data("label"); // <-- le texte bien formé
        $("#id_personnel").val(id);        // hidden field
        $("#searchEmploye").val(label);    // champ texte visible
        $("#resultsEmploye").hide();
    });

});
</script>
</body>
</html>
