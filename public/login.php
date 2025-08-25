<?php
   require_once '../includes/database.php'; 

    session_start();

    $errors = [];
    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = trim(htmlspecialchars($_POST["email"]) ?? '');
        $password = $_POST["mot_de_passe"] ?? '';
        
        if(empty($password)) {
            $errors[] = "le mdp est obligatoire";
        }

        if(empty($email)) {
            $errors[] = "l'email est obligatoire";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "votre adresse ne correspond au format mail classique";
        }

        if (empty($errors)) {
            try {
                //appel de la fonction de connexion a la db
                $pdo = dbConnexion();
                //prépare une requete sql (email dynamique)
                $sql = "SELECT * FROM usagers WHERE email = ?";
                //stock ma request préparée 
                $requestDb = $pdo->prepare($sql);
                //"execute la request en lui passant en parametre l'element dynamique
                $requestDb->execute([$email]);
                //recupération des données
                $user = $requestDb->fetch();
                
                if ($user) {
                    //verification
                    if (password_verify($password, $user["mot_de_passe"])) {
                        $_SESSION["user_id"] = $user['id'];
                        $_SESSION["username"] = $user['username'];
                        $_SESSION["email"] = $user['email'];
                        $_SESSION['login'] = true;

                        $message = "super vous etes connecté " . htmlspecialchars($user['nom']);
                         header("Location: ../config/compte.php?id=" . $user['id']);
                        exit();
                    }else{
                        $errors[] = "mot de passe pas bon ma gueule";
                    }     
                } else{
                    $errors[] = "compte introuvable ma gueule";
                }  
            } catch (PDOException $e) {
                $errors[] = "problémes de connexion " . $e->getMessage();
            }
        }

        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../asset/styles.css">
</head>
<body>

  <?php
    include '../includes/header.php';
    ?>

    <?php
        if (!empty($errors)) {
            foreach($errors as $error) {
                echo $error;
            }
        }
    ?>
    <form action="" method="POST">
        <div>
            <label for="email">Votre émail</label>
            <input type="email" name="email" id="email" required placeholder="Entrez votre email">
        </div>
        <div>
            <label for="mot_de_passe">Votre mot de passe</label>
            <input type="password" name="mot_de_passe" id="password" required placeholder="entrer votre mdp">
        </div>
        <div>
           <button type="submit">Se connecter</button>
        </div>
    </form>

  <?php
    include '../includes/footer.php';
    ?>
</body>
</html>