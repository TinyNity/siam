<!DOCTYPE html>
<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
function redirect(string $url, int $statusCode = 303) { //! Redirect HTTP code
    header('Location: ' . $url, true, $statusCode);
    die();
}
! defined("EXPIRYDATE") && define('EXPIRYDATE', time() + (3600 * 24 * 60), false);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Login</title>
</head>
<body>
    <fieldset>
        <form action="" method="post">
            <label for="website"> Login </label> <br>
            <input type="text" id="login" name="login" required /><br>
            <input type="text" id="password" name="password" required /><br>

            <input type="submit" value="Login" name="loginForm">
        </form>
    </fieldset>

    <?php
    if (isset($_POST['loginForm'])) { //! Name
        if (! ($_POST["login"] == $userLogin[0] &&
            $_POST["password"] == $userLogin[1])) {
            echo "<br> <p> Wrong login or password </p> ";
        } else {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['password'] = $_POST['password'];
            redirect($newURL);
        }
    }
    ?>
    
</body>
</html>