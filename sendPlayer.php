<?php
    session_start();
    include_once "php/dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"]==="GET"){
        $dbInterface=DbInterface::getInstance();
        $players=$dbInterface->getPlayers($_SESSION["id_game"]);
        header("Content-Type:application/json");
        echo json_encode($players);
    }