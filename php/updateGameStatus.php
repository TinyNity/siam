<?php
    session_start();
    include_once "dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["status"]) && isset($_POST["current_player_turn"])){
        $dbInterface=DbInterface::getInstance();
        $status=$_POST["status"];
        $current_player_turn=$_POST["current_player_turn"];
        $winner=$_POST["winner"]==""?(null):($_POST["winner"]);
        $dbInterface->updateGameStatus($_SESSION["id_game"],$status,$current_player_turn,$winner);
    }