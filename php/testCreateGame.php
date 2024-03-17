<?php
include_once "dbInterface.php";
include_once "utils.php";
if (isset ($_COOKIE["username"])) {
    error_log("[+] TestCreateGame.php : User cookie is set as " . $_COOKIE["username"]);
    $username = $_COOKIE["username"];
} else {
    error_log("[-] TestCreateGame.php : User cookie is not set");
}
if (isset ($_POST["createGame"]) && isset ($username)) {
    $dbInterface = DbInterface::getInstance();
    $status = $dbInterface->createGame($username);
    error_log($status);
}
redirect("../home.php");
