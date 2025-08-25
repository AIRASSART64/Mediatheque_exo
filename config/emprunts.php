<?php
session_start();
require_once '../includes/database.php';
if (!isset($_SESSION['user_id'])) {
    // Si pas connecté → on redirige vers la page de login
    header("Location: ../public/login.php");
    exit;
}

// 2. Vérifier que l'id du livre est présent dans l'URL
if (!isset($_GET['id'])) {
    echo "Aucun livre sélectionné.";
    exit;
}

$idLivre = (int) $_GET['id'];
$idUsager = $_SESSION['user_id'];

try {
$pdo = dbConnexion();

$sql = "SELECT disponible
        FROM livres 
        WHERE id = ?" ;
$stmt = $pdo->prepare($sql);
$stmt->execute([$idLivre]);
$livre = $stmt->fetch(PDO::FETCH_ASSOC);

 if (!$livre) {
     echo "Livre introuvable.";
        exit;
    }
      if ($livre['disponible'] === 'non') {
        echo "Ce livre est déjà emprunté.";
        exit;
    }
    

    // 4. Créer un emprunt
    $sql = "INSERT INTO emprunts (id_usager, id_livre, date_emprunt, statut)
            VALUES (?, ?, CURDATE(), 'en_cours')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsager, $idLivre]);

    // 5. Mettre à jour la dispo du livre
    $sql = "UPDATE livres SET disponible = 'non' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idLivre]);

    // Redirection vers la liste des emprunts
    header("Location: mes_emprunts.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}


?>

<h2>Mes emprunts</h2>
<ul>
<?php foreach ($emprunts as $emprunt): ?>
    <li>
        <?= htmlspecialchars($emprunt['titre']) ?> - <?= htmlspecialchars($emprunt['auteur']) ?>
        (emprunté le <?= $emprunt['date_emprunt'] ?>)
        <a href="retour.php?id=<?= $emprunt['id'] ?>">Retourner</a>
    </li>
<?php endforeach; ?>
</ul>