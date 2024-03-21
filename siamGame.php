<?php

const BOARD_SIZE = 5;
session_start();
include_once "php/dbInterface.php";
include_once "php/utils.php";
$username=$_COOKIE["username"];
$dbInterface = DbInterface::getInstance();
error_log($_SESSION["id_game"]);
error_log($_SESSION["id_player"]);
if (!isset($_SESSION["id_game"]) || !isset($_SESSION["id_player"]) || !$dbInterface->checkUserIsPlayer($username, $_SESSION["id_player"]) || !$dbInterface->checkPlayerInGame($_SESSION["id_player"], $_SESSION["id_game"])) {
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
    <script defer src="scripts/scriptRibbon.js"></script>
</head>

<body>
    <div class="ribbon">
        <p>You're logged in as </p>
        <form id="dcForm" action="./logout.php">
            <select id="dropdownMenu" onchange="handleDropdownChange(this)">
                <option value="disconnect">Disconnect</option>
                <option value="changePassword">Change Password</option>
                <option value="history">Game history</option>
                <option value="home">Home</option>
                <option value="" disabled selected>
                    <?php echo $username; ?>
                </option>
            </select>
        </form>
    </div>
    <form>
        <input type="hidden" id="id_player" value="<?php echo $_SESSION["id_player"] ?>">
    </form>
    <div id="container">
        <div id="game-container">
            <p id="currPlayer">Current player turn : 
                <span id='playerturn'>Waiting...</span></p>
                <hr style="margin-top:43px; margin-bottom:60px;">
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
                    <button class="button" id="addpiece">Add piece to gameboard</button>
                    <button class="button" id="cancel">Cancel selection</button>
                    <button class="button" id="rotate">Rotate selected piece</button>
                    <button class="button" id="removepiece">Remove selected piece</button>
                    <button class="button" id="endturn">End turn</button>
                </div>
            </div>
            <div class="reservedpiece-container">
                <div class="addpiece-container">
                    <img src id='addpiece-container'>
                </div>
                <p>Reserved piece : <span id='reservedpiece'>0</span></p>
            </div>
        </div>
        <div id="tuto-container">
            <h2 id="tuto-header">How to play ?</h3>
            <hr>
            <p class="txt">When it's your turn, it will say so at the top of the page. When that's the case, you can add a piece to the gameboard, or move a piece that already exists</p>
            <p class="txt">You can place the piece directly on the board by clicking on it, or rotate it beforehand by clicking the <code>Rotate selected piece</code> button. </p>
            <p class="txt">You can cancel your action by clicking the <code>Cancel selection</code> button.</p>
            <p class="txt">You can remove a piece on the end of the board by clicking on it by clicking <code>Remove selected piece</code></p>
            <p class="txt">When you're finished, end your turn by clicking on the <code>End turn</code> button</p>
            <button class="button" onclick="location.href = 'https://www.educmat.fr/categories/jeux_reflexion/fiches_jeux/siam/index.php';">game rules</button>
        </div>
    </div>
</body>

</html>