<!DOCTYPE html>
<?php
//! HOME PAGE FOR LOGGED IN USERS

include_once "dbInterface.php";
include_once "utils.php";
var_dump($_COOKIE);
if (isset($_COOKIE["username"])) {
    error_log("User cookie is set as " . $_COOKIE["username"] );
    $userCookie = $_COOKIE["username"];
} else {
    error_log("User cookie is not set");
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleHome.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAM - Home</title>
</head>

<body>
    <div class="ribbon">
    <p>You're logged in as <?php echo $userCookie[0]; ?></p>
        <input type="button" id="dcButton" value="Disconnect">  
    </div>
    <h1>Siam - Home</h1> <hr>
    <div class="buttons-container">
        <button>Button 1</button>
        <button>Button 2</button>
        <button>Button 3</button>
        <button>Button 4</button>
    </div>
</body>

</html>