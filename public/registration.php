<?php
    require_once '../includes/database.php'; 

    $errors = [];
    $message = "";
    // =========================================
    // condition qui contient la logique de traitement du formulaire quand on recoit une request POST
    // ==========================================
    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        //recuperation des données du formulaire
        //nettoyage des données du formulaire
        $username = trim(htmlspecialchars($_POST["nom"]) ?? '');
        $email = trim(htmlspecialchars($_POST["email"]) ?? '');
        $password = $_POST["mot_de_passe"] ?? '';
        $confirmPassword = $_POST["confirmPassword"] ?? '';

        //validation username
        //valide que le champ soit remplis
        if (empty($username)) {
            $errors[] = "nom obligatoire !";
        //valide avec la function strlen si la string est de plus de 3 carac
        }elseif (strlen($username) < 3) {
            $errors[] = "mini 3 carac";
        //valide avec la function strlen si la string est de moins de 55 carac
        }elseif (strlen($username) > 55) {
            $errors[] = "max 55 carac";
        }
        //validation email
        if (empty($email)) {
            $errors[] = "email obligatoire ! ";
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "votre adresse ne correspond au format mail classique";
        }

        //validation password
        if (empty($password)) {
            $errors[] = "password obligatoire";
        }elseif ( strlen($password) < 3 ) {
            $errors[] = "password trop juste";
            // normalement ici on met un pattern pour le mdp
        }elseif ( $password !== $confirmPassword ) {
            $errors[] = "mot de passe doivent etre identique";
        }
        
        if (empty($errors)) {
            //logique de traitement en db
            $pdo = dbConnexion();

            //verifier si l'adresse mail est utilisé ou non
            $checkEmail = $pdo->prepare("SELECT id FROM usagers WHERE email = ?");

            //la methode execute de mon objet pdo execute la request préparée
            $checkEmail->execute([$email]);

            //une condition pour vérifier si je recupere quelque chose
            if ($checkEmail->rowCount() > 0) {
                $errors[] = "email inexistant";
            } else {
                //dans le cas ou tout va bien ! email pas utilisé

                //hashage du mdp avec la fonction password_hash
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);

                //insertion des données en db
                // INSERT INTO users (username, email, password)VALUES ("atif","atif@gmail.com","lijezfoifjerlkjf")
                $insertUser = $pdo->prepare("
                INSERT INTO usagers (nom, email, mot_de_passe) 
                VALUES (?, ?, ?)
                ");

                $insertUser->execute([$username, $email, $hashPassword]);

                $message = "super mega cool vous êtes enregistré $username";
            }
    
        }

        
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/styles.css">
    <title>Document</title>
</head>
  <?php
    include '../includes/header.php';
    ?>

<body>
    <section>
        <?php
                foreach ($errors as $error) {
                    echo $error;
                }
                if(!empty($message)) {
                    echo $message;
                }
            ?>
       <form action="" method="POST">
                <div class="inputContainer">
                     <label for="nom">Votre nom</label>
                    <input placeholder="" type="text" id="nom" name="nom"  class="input">
                </div>
                <div class="inputContainer">
                     <label for="email">Votre mail</label>
                    <input placeholder="" type="text" id="email" name="email"  class="input">
                </div>
                <div class="inputContainer">
                    <label for="mot_de_passe">Votre mot de passe</label>
                    <input placeholder="" type="text" id="mot_de_passe" name="mot_de_passe" class="input">
                </div>
                 <div class="inputContainer">
                    <label for="confPassword">Confirmer votre mot de passe</label>
                    <input placeholder="confirm password" type="text" id="confPassword" name="confirmPassword"  class="input">
                </div>

                <div >
                    <input class="buttonSend" type="submit" value="Envoyer">
                </div>
            </form>

   </section>

     <?php
    include '../includes/footer.php';
    ?>
</body>
</html>