<?php
session_start();
require_once '../includes/database.php';

// Vérifier si l’usager est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../config/login.php");
    exit;
}

// Vérifier si un id d’emprunt est passé
if (!isset($_GET['id'])) {
    echo "Aucun emprunt sélectionné.";
    exit;
}

$idEmprunt = (int) $_GET['id'];
$idUsager = $_SESSION['user_id'];

try {
    $pdo = dbConnexion();

    // Vérifier que l’emprunt existe et appartient bien à l’utilisateur connecté
    $sql = "SELECT * FROM emprunts WHERE id = ? AND id_usager = ? AND statut = 'en_cours'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idEmprunt, $idUsager]);
    $emprunt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$emprunt) {
        echo "Aucun emprunt trouvé ou déjà rendu.";
        exit;
    }

    // Mettre à jour le statut de l’emprunt
    $sql = "UPDATE emprunts 
            SET statut = 'rendu', date_retour = CURDATE() 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idEmprunt]);

    // Remettre le livre en disponible
    $sql = "UPDATE livres SET disponible = 'oui' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$emprunt['id_livre']]);

    // Redirection vers mes emprunts
    header("Location: mes_emprunts.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>