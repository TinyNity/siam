<?php

const BOARD_SIZE = 5;
session_start();
include_once "php/dbInterface.php";
include_once "php/utils.php";

$dbInterface = DbInterface::getInstance();
error_log($_SESSION["id_game"]);
error_log($_SESSION["id_player"]);
if (!isset($_SESSION["id_game"]) || !isset($_SESSION["id_player"]) || !$dbInterface->checkUserIsPlayer($_COOKIE["username"], $_SESSION["id_player"]) || !$dbInterface->checkPlayerInGame($_SESSION["id_player"], $_SESSION["id_game"])) {
    redirect("home.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styleGame.css">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Siam game</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="scripts/siamLogic.js"></script>
</head>

<body>
    <form>
        <input type="hidden" id="id_player" value="<?php echo $_SESSION["id_player"] ?>">
    </form>
    <div class="game-container">
        <p>Current player turn : <span id='playerturn'>Waiting...</span></p>
        <div class="gameboard">
            <table class="board-container">
                <?php
                for ($i = 0; $i < BOARD_SIZE; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j < BOARD_SIZE; $j++) {
                        echo "<td id='cell-$i-$j' class='cell' data-row='$i' data-col='$j'>";
                        echo "<img src id='image-$i-$j' class='piece'>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
            <div>
                <button id="addpiece">Add piece to gameboard</button>
                <button id="cancel">Cancel selection</button>
                <button id="rotate">Rotate selected piece</button>
                <button id="endturn">End turn</button>
            </div>
        </div>
        <div class="reservedpiece-container">
            <div class="addpiece-container">
                <img src id='addpiece-container'>
            </div>
            <p>Reserved piece : <span id='reservedpiece'>0</span></p>
        </div>
    </div>
</body>

</html>