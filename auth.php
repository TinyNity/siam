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
    switch ($status) {
        case EStatus::USERINDB:
            return EStatus::USERINDB;
        case EStatus::USERCREATED:
            return EStatus::USERCREATED;
        default:
            error_log("[-] Auth register : Unreachable code reached");
            return null;
    }
}

function authLogin($rawData) : String {
    $data = json_decode($rawData, true);
    $dbInterface = DBInterface::getInstance();
    $status = $dbInterface->loginUser($data["username"], $data["password"]);
    switch ($status) {
        case EStatus::NOUSER:
            error_log("[-] Auth login : User not found");
            return EStatus::NOUSER;
        case EStatus::REJECTED:
            error_log("[-] Auth login : Wrong password");
            return EStatus::REJECTED;
        case EStatus::APPROVED:     //? Authentification is approved, user is logged in
            error_log("[+] Auth login : Approved");
            return EStatus::APPROVED;
        default:
            error_log("[-] Auth login : Unreachable code reached");
            return null;
    }
}