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



if (isset($_POST['registerForm'])) {
    if (!$_POST['password'] == $_POST['password2']) {

        die();
        //TODO Putain de check si p1== p2
    }
    $data = [];
    $data[] = "register";
    $data[] = $_POST['username'];
    $data[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    setcookie("user", json_encode($data), EXPIRYDATE);
    //redirect("./auth.php");
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer>
        document.addEventListener("load", () => {
            let form = document.getElementById('registerForm');
            console.log(form.elements["password"].value + ", " + form.elements["password2"].value);
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                let p1 = form.elements["password"].value;
                let p2 = form.elements["password2"].value;
                console.log(p1 + ", " + p2);
                if (p1 == p2) {
                    alert("[-] The passwords don't match !"); 
                    window.location.reload(false);
                }
            });
        })
    </script>
    <title>SIAM - Register</title>
</head>

<body>
    <h1>Siam - Register</h1> <hr>
    <h2>Create a new account : </h2>
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
            <label for="password2">Re-enter your password : </label>
            <input type="password" id="password2" name="password2" required /><br>

            <input class="btn" type="submit" value="enter" name="registerForm">
        </form>
        <form action="login.php">
            <input class="btn" type="submit" value="Already have an account ?">
        </form>
    </div>


</body>

</html>