<?php
    session_start();
    include_once "dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"]==="GET"){
        $dbInterface=DbInterface::getInstance();
        $players=$dbInterface->getPlayer($_SESSION["id_game"]);
        header("Content-Type:application/json");
        echo json_encode($players);
    }