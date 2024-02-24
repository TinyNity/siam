<!DOCTYPE html>
<?php
//! AUTH PAGE
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("dbInterface.php");
function redirect(string $url, int $statusCode = 303)
{ // Redirect HTTP code
    header('Location: ' . $url, true, $statusCode);
    die();
}
! defined("EXPIRYDATE") && define('EXPIRYDATE', time() + (3600 * 24 * 60), false);


$data = json_decode($_COOKIE["user"]);
var_dump($data);

//* Case : Register - username already exists

//* Case : Login - No associated account with uname / pw combo 

//* Case : Login and password combo matches an account 

    // $dbInterface = DBInterface::getInstance();
    // $dbInterface->registerAccount($username, $password);



?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth</title>
</head>

<body>





</body>

</html>