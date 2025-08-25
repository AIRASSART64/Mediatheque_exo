<?php
    require_once '../includes/database.php'; 

     $pdo = dbConnexion();
     $sql = "SELECT * FROM livres ";
     $requestDb = $pdo->prepare($sql);   
     $requestDb->execute();
     $livres = $requestDb->fetchAll(PDO::FETCH_ASSOC);

     $limit = 5;

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    $offset = ($page - 1) * $limit;

    // Critère de recherche
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Critère de tri (sécurité → liste blanche)
    $allowedSort = ['titre', 'auteur', 'annee'];
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'titre';
    if ($search != '') {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM livres 
                                WHERE titre LIKE :search OR auteur LIKE :search");
    $countStmt->execute(['search' => "%$search%"]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM livres");
}
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);
if ($search != '') {
    $stmt = $pdo->prepare("SELECT * FROM livres 
                           WHERE titre LIKE :search OR auteur LIKE :search
                           ORDER BY $sort
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM livres 
                           ORDER BY $sort
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
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
    <form method="get" action="catalogue.php">
    <input type="text" name="search" placeholder="Rechercher un titre ou un auteur"
           value="<?php echo htmlspecialchars($search); ?>">

    <button type="submit">Rechercher</button>

     <select name="sort">
        <option value="titre" <?php if ($sort=='titre') echo 'selected'; ?>>Trier par titre</option>
        <option value="auteur" <?php if ($sort=='auteur') echo 'selected'; ?>>Trier par auteur</option>
        <option value="annee" <?php if ($sort=='annee') echo 'selected'; ?>>Trier par année</option>
    </select>
</form>

      <ul class="books-list">

         <?php foreach ($livres as $livre) : ?> 
    
            <li>
                 

                    <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                    
                    <label class="Auteur" > Auteur : <?= htmlspecialchars($livre['auteur']) ?></label>
                    <label class="année"> Année de publication : <?= htmlspecialchars($livre['annee']) ?></label>
                    <label> Disponible : <?= $livre['disponible'] == 1 ? "Oui" : "Non";  ?></label>
                    
                    <a class="consult" href="livre.php?id=<?= ($livre['id']) ?>">Voir</a>
                    <?php if ($livre['disponible'] === 1): ?>
                     <a href="../config/emprunts.php?id=<?= $livre['id'] ?>" class="consult">Emprunter</a>
                    <?php else: ?>
                        <span class="">Indisponible</span>
                    <?php endif; ?>
                  
            </li>
           <?php endforeach; ?>
     </ul>
     </div>

 <?php
      include '../includes/footer.php';
    ?>
</body>
</html>