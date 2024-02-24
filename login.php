<!DOCTYPE html>
<?php
//! LOGIN PAGE
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("dbInterface.php");
function redirect(string $url, int $statusCode = 303) { // Redirect HTTP code
    header('Location: ' . $url, true, $statusCode);
    die();
}
! defined("EXPIRYDATE") && define('EXPIRYDATE', time() + (3600 * 24 * 60), false);
setcookie("user", "", EXPIRYDATE);





if (isset($_POST['loginForm'])) {
    $data = [];
    $data[] = "login";
    $data[] = $_POST['username'];
    $data[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    setcookie("user", json_encode($data), EXPIRYDATE);
    redirect("./auth.php");
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Login</title>
</head>

<body>
    <h1>Siam - Login</h1> <hr>
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

    <div id="formDiv">
        <form action="" method="post">
            <label for="username">Username : </label>
            <input type="text" maxlength="32" id="username" name="username" required /><br>
            <label for="password">Password : </label>
            <input type="password" id="password" name="password" required /><br>

            <input class="btn" type="submit" value="enter" name="loginForm">
        </form>
        <form action="register.php">
            <input class="btn" type="submit" value="No account ?">
        </form>
    </div>


</body>

</html>