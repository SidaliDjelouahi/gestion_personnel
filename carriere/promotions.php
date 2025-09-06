<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";
require_once ROOT_PATH . "/includes/header.php";
require_once ROOT_PATH . "/includes/sidebar.php";

// --- Liste des employés ---
$employes = $pdo->query("SELECT id_personnel, matricule, nom, prenom 
                         FROM personnel 
                         ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);

// --- Liste des grades ---
$grades = $pdo->query("SELECT id_grade, libelle_grade 
                       FROM grades 
                       ORDER BY libelle_grade")->fetchAll(PDO::FETCH_ASSOC);

// --- Traitement du formulaire ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_personnel = $_POST['id_personnel'];
    $id_grade     = $_POST['id_grade'];
    $date_debut   = $_POST['date_debut'];
    $observation  = $_POST['observation'] ?? '';

    // Clôturer la carrière en cours (s’il existe)
    $pdo->prepare("UPDATE carriere 
                   SET date_fin = ? 
                   WHERE id_personnel = ? AND date_fin IS NULL")
        ->execute([$date_debut, $id_personnel]);

    // Ajouter un nouvel enregistrement carrière
    $stmt = $pdo->prepare("INSERT INTO carriere 
        (id_personnel, id_grade, date_debut, type_mouvement, observation) 
        VALUES (?, ?, ?, 'Promotion', ?)");
    $stmt->execute([$id_personnel, $id_grade, $date_debut, $observation]);

    $_SESSION['message'] = ["type" => "success", "text" => "Promotion enregistrée avec succès"];
    header("Location: promotions.php");
    exit;
}
?>

<div class="container mt-4">
    <h2 class="mb-3"><i class="fa fa-arrow-up"></i> Nouvelle Promotion</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Formulaire ajout promotion -->
    <form method="post" class="card p-3 shadow-sm">
        <div class="mb-3 position-relative">
            <label class="form-label">Recherche Employé</label>
            <input type="text" id="rechercheEmploye" class="form-control" placeholder="Matricule , Nom ou prénom..." autocomplete="off" autofocus>
            <input type="hidden" name="id_personnel" id="id_personnel" required>
            <div id="resultatsEmploye" class="list-group position-absolute w-100 shadow-sm" style="z-index:1000; max-height:200px; overflow-y:auto;"></div>
        </div>


        <div class="mb-3">
            <label class="form-label">Nouveau Grade</label>
            <select name="id_grade" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?= $grade['id_grade'] ?>">
                        <?= htmlspecialchars($grade['libelle_grade']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date d'effet</label>
            <input type="date" name="date_debut" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observation</label>
            <textarea name="observation" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Enregistrer
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const input = document.getElementById("rechercheEmploye");
    const hiddenId = document.getElementById("id_personnel");
    const resultsDiv = document.getElementById("resultatsEmploye");

    input.addEventListener("keyup", function() {
        let query = input.value.trim();
        if (query.length < 2) {
            resultsDiv.innerHTML = "";
            return;
        }

        fetch("recherche_employe.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = "";
                data.forEach(emp => {
                    let item = document.createElement("button");
                    item.type = "button";
                    item.className = "list-group-item list-group-item-action";
                    item.textContent = emp.nom + " " + emp.prenom;
                    item.onclick = function() {
                        input.value = emp.nom + " " + emp.prenom;
                        hiddenId.value = emp.id_personnel;
                        resultsDiv.innerHTML = "";
                    };
                    resultsDiv.appendChild(item);
                });
            });
    });

    // Si on clique ailleurs, fermer la liste
    document.addEventListener("click", function(e) {
        if (!resultsDiv.contains(e.target) && e.target !== input) {
            resultsDiv.innerHTML = "";
        }
    });
});
</script>

</body>
</html>
