<?php
    session_start();
    include_once "dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["id_player"]) && isset($_POST["reserved_piece"])){
        $dbInterface=DbInterface::getInstance();
        $id_player=$_POST["id_player"];
        $reserved_piece=$_POST["reserved_piece"];
        $dbInterface->updatePlayerData($id_player,$reserved_piece);
    }