<?php
include_once "dbInterface.php";
include_once "utils.php";
if (isset($_COOKIE["username"])) {
    error_log("User cookie is set as " . $_COOKIE["username"] );
    $userCookie = $_COOKIE["username"];
} else {
    error_log("User cookie is not set");
}
if (isset($_POST["createGame"]) && isset($userCookie)){
    $dbInterface=DbInterface::getInstance();
    $status=$dbInterface->createGame($userCookie);
    error_log($status);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing creation of game</title>
</head>
<body>
    <form method="POST" action="">
        <input type="submit" name="createGame">
    </form>
</body>
</html>
