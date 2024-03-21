<!DOCTYPE html>
<?php
//! HOME PAGE FOR LOGGED IN USERS

include_once "php/dbInterface.php";
include_once "php/utils.php";
include_once "php/EGameStatus.php";

session_start();
$dbInterface=DbInterface::getInstance();

if (isset ($_COOKIE["username"])) {
    error_log("User cookie is set as " . $_COOKIE["username"]);
    $username = $_COOKIE["username"];
} else {
    error_log("User cookie is not set");
    redirect("./login.php");    
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styleHome.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="scripts/scriptRibbon.js" defer></script>
    <title>SIAM - Home</title>
</head>

<body>
    <div class="ribbon">
        <p>You're logged in as </p>
        <form id="dcForm" action="./logout.php">
            <select id="dropdownMenu" onchange="handleDropdownChange(this)">
                <option value="disconnect">Disconnect</option>
                <option value="changePassword">Change Password</option>
                <option value="history">Game history</option>
                <option value="" disabled selected>
                    <?php echo $username; ?>
                </option>
            </select>
        </form>
    </div>
    <h1>Siam - Home</h1>
    <hr>
    <div id="content">
        <div id="container">
            <div id="games">
                <h3>Games</h3>
                <hr>
                <?php
                error_log("Fetching games now.");
                $data = $dbInterface->fetchGames(GameStatus::NOTSTARTED);
                ?>
                <table>
                    <tr> 
                        <th>  ID  </th>
                        <th>Status</th>
                        <th>Number of players</th>
                        <th>Wanna join ?</th>
                    </tr>
            
                <?php foreach ($data as $key => $value): ?>
                    <tr> 
                        <td><?php echo $data[$key]["id"]; ?></td>
                        <td><?php switch ($data[$key]["status"]){
                                case GameStatus::NOTSTARTED :
                                    echo "Not started";
                                    break;
                                case GameStatus::STARTED :
                                    echo "Started";
                                    break;
                                case GameStatus::FINISHEDWIN :
                                    echo "Winner:".$data[$key]["winner"];
                                    break;
                                case GameStatus::FINISHEDDRAW :
                                    echo "Draw";
                                    break;
                                default:
                                    break;
                                } ?></td>
                        <td><?php echo $data[$key]["nb_player"]."/2"; ?></td>
                        <td>
                            <form action="php/joinGame.php" method="post">
                                <input type="hidden" name="id_game" value="<?php echo $data[$key]["id"]; ?>">
                                <input type="submit" value="Join" name="JoinForm">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
            <div class="buttons-container">
                <div class="buttons-container">
                    <form action="php/testCreateGame.php" method="post">
                        <input type="submit" name="createGame" value="Create a game">
                    </form>
                </div>
                <h3>Current Games</h3>
                <hr>
                <?php
                error_log("Fetching games now.");
                $data = $dbInterface->fetchGamesUser($_COOKIE["username"],GameStatus::STARTED);
                ?>
                <table>
                    <tr> 
                        <th>  ID  </th>
                        <th>Current player</th>
                       
                    </tr>
                <?php foreach ($data as $key => $value): ?>
                    <tr> 
                        <td><?php echo $data[$key]["id"]; ?></td>
                        <td><?php if ($data[$key]["current_player_turn"]!=null){
                                    echo $dbInterface->getUserFromPlayer($data[$key]["current_player_turn"]);
                                } ?></td>
                        <td>
                            <form action="php/joinGame.php" method="post">
                                <input type="hidden" name="id_game" value="<?php echo $data[$key]["id"]; ?>">
                                <input type="submit" value="Join" name="JoinForm">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>