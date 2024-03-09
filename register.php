<!DOCTYPE html>
<?php
//! LOGIN PAGE

include_once "dbInterface.php";
include_once "utils.php";
include_once "auth.php";

if (isset($_POST['registerForm'])) {
    error_log($_POST['password']);error_log($_POST['password2']);
    if ($_POST['password'] != $_POST['password2']) {
        error_log("[!] Passwords " . $_POST['password'] . " and " . $_POST['password2'] . " don\'t seem to match.");
        alert("The passwords don't match !");
        js("window.location = \"./register.php\" ");
    }   
    $data = [];
    $data[] = $_POST['username'];
    $data[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = authRegister(json_encode($data));
    error_log($status);
    if ($status == EStatus::USERCREATED) {
        alert("User created ! You can now login.");
        js("window.location = \"./login.php\" ");
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Register</title>
</head>

<body>
    <h1>Siam - Register</h1> <hr>
    <h2>Create a new account : </h2>
    <div id="formDiv">
        <form action="" method="post">
            <label for="username">Username : </label>
            <input type="text" maxlength="32" id="username" name="username" required /><br>
            <label for="password">Password : </label>
            <input type="password" id="password" name="password" required /><br>
            <label for="password2">Re-enter your password : </label>
            <input type="password" id="password2" name="password2" required /><br>

            <input class="btn" type="submit" value="register" name="registerForm">
        </form>
        <form action="login.php">
            <input class="btn" type="submit" value="Already have an account ?">
        </form>
    </div>


</body>

</html>