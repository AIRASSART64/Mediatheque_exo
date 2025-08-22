<?php
    require_once '../includes/database.php'; 

     $pdo = dbConnexion();
     $sql = "SELECT * FROM livres ";
     $requestDb = $pdo->prepare($sql);   
     $requestDb->execute();
     $livres = $requestDb->fetchAll(PDO::FETCH_ASSOC);

     
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue</title>
    <link rel="stylesheet" href="../asset/styles.css">
</head>
<body>
    <?php
      include '../includes/header.php';
    
    ?>

    <div class="listContainer">
    <h2>Consulter le catalogue</h2>

      <ul class="books-list">

         <?php foreach ($livres as $livre) : ?> 
    
            <li>
                 

                    <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                    
                    <label class="Auteur" > Auteur : <?= htmlspecialchars($livre['auteur']) ?></label>
                    <label class="année"> Année de publication : <?= htmlspecialchars($livre['annee']) ?></label>
                    <label> Disponible : <?= htmlspecialchars($livre["disponible"]) ?></label>
        
                    <!-- <p><?= htmlspecialchars($livre['description']) ?></p> -->
                    
                    <a class="consult" href="livre.php?id=<?= ($livre['id']) ?>">Voir</a>
                  
            </li>
           <?php endforeach; ?>
     </ul>
     </div>

 <?php
      include '../includes/footer.php';
    ?>
</body>
</html>