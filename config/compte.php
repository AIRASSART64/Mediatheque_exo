<!-- <?php
require_once  '../includes/database.php'; 


if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
//  Requete et execution sql dans la db
$pdo = dbConnexion();
$sql = "SELECT * FROM usagers WHERE id = ?";
$requestDb = $pdo->prepare($sql);
$requestDb->execute([$id]);
$usager = $requestDb->fetch(PDO::FETCH_ASSOC);

// Vérification de l'execution de la requete
if (!$usager) {
    echo "compte introuvable";
    exit;
}
}

?> -->
<?php
// compte.php
session_start();
require_once __DIR__ . '/../includes/database.php';

try {
    $pdo = dbConnexion(); 
} catch (Throwable $e) {
    http_response_code(500);
    die("Erreur de connexion BDD");
}


$id = null;
if (!empty($_SESSION['user']['id'])) {
    $id = (int) $_SESSION['user']['id'];
} elseif (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $error = "Compte inexistant";
}

// 2) Récupération en base
$usager = null;
if (!isset($error)) {
    $stmt = $pdo->prepare("SELECT * FROM usagers WHERE id = ?");
    $stmt->execute([$id]);
    $usager = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usager) {
        $error = "Compte introuvable.";
    }
}


if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$idUsager = $_SESSION['user_id'];
$pdo = dbConnexion();

$sql = "SELECT e.id, l.titre, l.auteur, e.date_emprunt, e.date_retour, e.statut
        FROM emprunts e
        JOIN livres l ON e.id_livre = l.id
        WHERE e.id_usager = ? AND e.statut = 'en_cours'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUsager]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <link rel="stylesheet" href="../asset/styles.css">
  
</head>

   
<body>
    <?php
    include  '../includes/header.php';
    ?>
    <h2>Mon compte</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <div class="listContainer">
            <h3><?= htmlspecialchars($usager['nom']) ?></h3>
            <div class="bookslist">
                <label>Email : <?= htmlspecialchars($usager['email']) ?></label>
                <label>Statut : <?= htmlspecialchars($usager['statut']) ?></label> 
            </div>
            <div>
             <ul>
                   <h2>Mes emprunts</h2>
            <?php foreach ($emprunts as $emprunt): ?>
                <li>
                    <?= htmlspecialchars($emprunt['titre']) ?> - <?= htmlspecialchars($emprunt['auteur']) ?>
                    (emprunté le <?= $emprunt['date_emprunt'] ?>)
                    <a href="retour.php?id=<?= $emprunt['id'] ?>">Retourner</a>
                </li>
            <?php endforeach; ?>
            </ul>
            </div>
            
            <div>
                <a class="btn" href="../index.php">Se déconnecter</a>
            </div>
        </div>
    <?php endif; ?>
</div>

    <?php
      include  '../includes/footer.php';
    ?>
</body>
</html> 