<?php
    include_once "dbInterface.php";
    include_once "utils.php";
    $dbInterface=DbInterface::getInstance();
    if (isset($_GET["id_game"]) && isset($_GET["id_player"]) && $dbInterface->checkUserIsPlayer($_COOKIE["username"],$_GET["id_player"]) && $dbInterface->checkPlayerInGame($_GET["id_player"],$_GET["id_game"])){

    }
    else {
        redirect("home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styleGame.css">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Siam game</title>
        <script defer src="siamLogic.js"></script>
    </head>

    <body>
        <table class="board-container">
            <tr>
                <td id="cell-0-0" class="cell"></td>
                <td id="cell-0-1" class="cell"></td>
                <td id="cell-0-2" class="cell"></td>
                <td id="cell-0-3" class="cell"></td>
                <td id="cell-0-4" class="cell"></td>    
            </tr>
            <tr>
                <td id="cell-1-0" class="cell"></td>
                <td id="cell-1-1" class="cell"></td>
                <td id="cell-1-2" class="cell"></td>
                <td id="cell-1-3" class="cell"></td>
                <td id="cell-1-4" class="cell"></td>    
            </tr>
            <tr>
                <td id="cell-2-0" class="cell"></td>
                <td id="cell-2-1" class="cell"></td>
                <td id="cell-2-2" class="cell"></td>
                <td id="cell-2-3" class="cell"></td>
                <td id="cell-2-4" class="cell"></td>    
            </tr>
            <tr>
                <td id="cell-3-0" class="cell"></td>
                <td id="cell-3-1" class="cell"></td>
                <td id="cell-3-2" class="cell"></td>
                <td id="cell-3-3" class="cell"></td>
                <td id="cell-3-4" class="cell"></td>    
            </tr>
            <tr>
                <td id="cell-4-0" class="cell"></td>
                <td id="cell-4-1" class="cell"></td>
                <td id="cell-4-2" class="cell"></td>
                <td id="cell-4-3" class="cell"></td>
                <td id="cell-4-4" class="cell"></td>    
            </tr>
        </table>
        
        <button>Add piece to gameboard</button>
    </body>
</html>