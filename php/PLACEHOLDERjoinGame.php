<?php

session_start();

include_once "dbInterface.php";
include_once "utils.php";


$dbInterface=DbInterface::getInstance();

if (isset($_POST['JoinForm'])) {
    if(isset($_POST['id_game'])) {
        $_SESSION["id_game"]=$_POST["id_game"];
        $player=$dbInterface->getPlayerFromUser($_POST["id_game"],$_COOKIE["username"]);
        switch ($player) {
            case -1:
                redirect("/siam/home.php");
                break;
            case 0:
                $newPlayer=$dbInterface->addPlayerToGame($_COOKIE["username"],$_POST["id_game"]);
                if ($newPlayer!=-1) {
                    $_SESSION["id_player"]=$newPlayer;
                    redirect("/siam/siamGame.php");
                    break;
                }
                else {
                    
                    redirect("/siam/home.php");
                }
            default:
                $_SESSION["id_player"]=$player;
                redirect("/siam/siamGame.php");
                break;
        }
    }
}