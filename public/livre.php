<?php
require_once '../includes/database.php'; 

// on se positionne sur toutes les données db associées à la tâche selectionnée dans la liste des tâches et identifiées par un ID  ---
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
//  Requete et execution sql dans la db
$pdo = dbConnexion();
$sql = "SELECT * FROM livres WHERE id = ?";
$requestDb = $pdo->prepare($sql);
$requestDb->execute([$id]);
$livre = $requestDb->fetch(PDO::FETCH_ASSOC);

// Vérification de l'execution de la requete
if (!$livre) {
    echo "Livre introuvable";
    exit;
}
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre</title>
    <link rel="stylesheet" href="../asset/styles.css">
    <script src="../assets/scripts.js" defer></script>
</head>

   <?php
    include '../includes/header.php';
    ?>
<body>
    <h2>Détail</h2>
    <div class="listContainer">
    <h3><?= htmlspecialchars($livre['titre']) ?></h3>
    <div class="bookslist">
    <label class="" >Auteur : <?= htmlspecialchars($livre['auteur']) ?></label>
    <label class=""> Année : <?= ($livre['annee']) ?></label>
    <p class="">Disponible : <?= $livre['disponible'] == 1 ? "Oui" : "Non";?></p>
    </div>
    <div >
        <a class="consult" href="catalogue.php">←  retour</a>
    </div> 
    </div>

    <?php
      include '../includes/footer.php';
    ?>
</body>
</html> 