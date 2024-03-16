<!DOCTYPE html>
<?php
//! PAGE TO CHANGE PASSWORD 

include_once "dbInterface.php";
include_once "utils.php";
include_once "auth.php";

if (isset($_POST['changePasswordForm'])) {
    error_log($_POST['newPassword']);
    error_log($_POST['newPassword2']);
    if ($_POST['newPassword'] != $_POST['newPassword2']) {
        error_log("[!] Passwords " . $_POST['newPassword'] . " and " . $_POST['newPassword2'] . " don\'t seem to match.");
        alert("The passwords don't match !");
        js("window.location = \"./changePassword.php\" ");
    }
    $status = changeUserPassword($_COOKIE["username"], $_POST["newPassword"]);  
    error_log($status);
    if ($status == EStatus::APPROVED) {
        alert("Password changed ! Please log in again.");
        js("window.location = \"./login.php\" ");
    }
}

if (isset($_COOKIE["username"])) {
    error_log("User cookie is set as " . $_COOKIE["username"]);
    $username = $_COOKIE["username"];

} else {
    error_log("User cookie is not set");
    redirect("./login.php");
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="scriptHome.js" defer></script>
    <title>SIAM - Home</title>
</head>

<body>
    <h1>Change password</h1>
    <hr>
    <div id="formDiv">
        <form action="" method="post">
            <label for="newPassword">New Password : </label>
            <input type="password" id="newPassword" name="newPassword" required /><br>
            <label for="newPassword2">Re-enter your password : </label>
            <input type="password" id="newPassword2" name="newPassword2" required /><br>

            <input class="btn" type="submit" value="change password" name="changePasswordForm">
        </form>
        <form action="./home.php">
            <input class="btn" type="submit" value="cancel">
        </form>
    </div>

</body>

</html>