<?php
    session_start();
    include_once "php/dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"]==="GET"){
        $dbInterface=DbInterface::getInstance();
        $game=$dbInterface->getGameData($_SESSION["id_game"]);
        header("Content-Type:application/json");
        echo json_encode($game);
    }