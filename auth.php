<!DOCTYPE html>
<?php
//! AUTH PAGE

include_once "dbInterface.php";
include_once "utils.php";
include_once "EStatus.php";

function authRegister($rawData) : String {
    $data = json_decode($rawData, true);
    $dbInterface = DBInterface::getInstance();
    $status = $dbInterface->registerAccount($data["username"], $data["password"]);
    return $status;
}

function authLogin($rawData) : String {
    $data = json_decode($rawData, true);
    $dbInterface = DBInterface::getInstance();
    $status = $dbInterface->loginUser($data["username"], $data["password"]);
    return $status;
}

function changeUserPassword(String $username, String $newPassword) : string {
    $dbInterface = DBInterface::getInstance();
    $status = $dbInterface->changePassword($username, $newPassword);
    return $status;
}