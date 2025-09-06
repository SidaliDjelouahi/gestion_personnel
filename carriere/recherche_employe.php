<?php
require_once __DIR__ . "/../includes/config.php";
require_once ROOT_PATH . "/includes/db.php";

$q = trim($_GET['q'] ?? '');

if ($q !== "") {
    $stmt = $pdo->prepare("SELECT id_personnel, matricule, nom, prenom 
                           FROM personnel 
                           WHERE nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?
                           ORDER BY nom, prenom , matricule
                           LIMIT 10");
    $stmt->execute(["%$q%", "%$q%" , "%$q%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
