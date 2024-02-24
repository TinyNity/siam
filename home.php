<!DOCTYPE html>
<?php
//! HOME PAGE FOR LOGGED IN USERS
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("dbInterface.php");
function redirect(string $url, int $statusCode = 303) { // Redirect HTTP code
    header('Location: ' . $url, true, $statusCode);
    die();
}
! defined("EXPIRYDATE") && define('EXPIRYDATE', time() + (3600 * 24 * 60), false);










if (isset($_POST['loginForm'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // if ($result) {
    //     $message = 'Inscription réussie!';
    //     header('Location: login.php');
    // } else {
    //     $message = 'Erreur lors de l\'inscription.';
    // }
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Home</title>
</head>

<body>
    <h1>Siam - Home</h1> <hr>
    <!-- 
        **Un utilisateur (administrateur ou joueur) doit pouvoir** : 

        - s'authentifier
        - modifier son mot de passe
        - créer et rejoindre une partie en attente d'un joueur
        - visualiser la liste des parties à rejoindre
        - visualiser la liste des parties que cet utilisateur a en cours. pour chaque partie, mettre  évidence le joueur dont c'est le tour
        - jouer un coup dans une partie en cours
        - se déconnecter


        **Un administrateur doit pouvoir** :

        - créer un compte joueur
        - jouer dans n'importe quelle partie quelque soit le joueur actif
        - supprimer une partie en cours
    -->


</body>

</html>