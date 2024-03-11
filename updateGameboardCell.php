<?php
    session_start();
    include_once "dbInterface.php";
    if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["row"]) && isset($_POST["column"])){
        $dbInterface=DbInterface::getInstance();
        $row=$_POST["row"];
        $column=$_POST["column"];
        $id_piece=$_POST["id_piece"]==""?(null):($_POST["id_piece"]);
        $id_player=$_POST["id_player"]==""?(null):($_POST["id_player"]);
        $direction=$_POST["direction"]==""?(null):($_POST["direction"]);
        $status=$dbInterface->updateGameboardCell($_SESSION["id_game"],$row,$column,$id_piece,$id_player,$direction);
        error_log($status);
    }