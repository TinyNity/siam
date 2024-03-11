<?php
    const BOARD_SIZE=5;
    session_start();
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
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script defer src="siamLogic.js"></script>
    </head>

    <body>
        <form>
            <input type="hidden" id="id_player" value="<?php echo $_GET["id_player"] ?>">
        </form>
        <table class="board-container">
            <?php
                for ($i=0;$i<BOARD_SIZE;$i++){
                    echo "<tr>";
                    for ($j=0;$j<BOARD_SIZE;$j++){
                        echo "<td id='cell-$i-$j' class='cell' data-row='$i' data-col='$j'>";
                        echo "<img src id='image-$i-$j' class='piece'>";    
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            ?>
            
        </table>
        
        <button>Add piece to gameboard</button>
    </body>
</html>