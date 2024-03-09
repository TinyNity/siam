<!DOCTYPE html>
<?php
//! LOGIN PAGE
setcookie("username", "", 60 * 60 * 24); // 24 hours

include_once "dbInterface.php";
include_once "utils.php";
include_once "auth.php";

if (isset($_POST['loginForm']) and $_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [];
    $data["username"] = $_POST['username'];
    $data["password"] = $_POST['password'];
    error_log("Login with '".$_POST['username']."' and '".$_POST['password']."' ...");
    $status = authLogin(json_encode($data));
    error_log("Got $status");
    error_log("");
    if ($status == EStatus::APPROVED) {
        error_log("Setting up the cookie with " . $_POST["username"] . "...");
        //! POURQUOI CE COOKIE DE MERDE IL PASSE PAS LA TA GRAND MERE LA PUTE
        setcookie("username", $_POST["username"], 60 * 60 * 24); // 24 hours
        var_dump($_COOKIE);
        //redirect("./home.php", false);
    } else {
        alert($status);
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Login</title>
</head>

<body>
    <h1>Siam - Login</h1> <hr>
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