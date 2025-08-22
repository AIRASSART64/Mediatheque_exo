<?php 

function dbConnexion() {
  
    $host = "localhost";
    $dbname = "mediatheque_exo";
    $username = "root";
    $password = "";
    $port = 3306;
    $charset = "utf8mb4";

    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset;port=$port";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;

    } catch (PDOException $e) {
        die("Impossible de se connecter à la database: " . $e->getMessage());
    }    
}

//   $connectionDb = dbConnexion();
// //   var_dump($connectionDb);
//   echo "la connexion fonctionne!"


?>