<!DOCTYPE html>
<?php
//! HOME PAGE FOR LOGGED IN USERS

include_once "php/dbInterface.php";
include_once "php/utils.php";
include_once "php/EGameStatus.php";
session_start();

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
    <script src="scripts/scriptHome.js" defer></script>
    <title>SIAM - Home</title>
</head>

<body>
    <div class="ribbon">
        <p>You're logged in as </p>
        <form id="dcForm" action="./logout.php">
            <select id="dropdownMenu" onchange="handleDropdownChange(this)">
                <option value="disconnect">Disconnect</option>
                <option value="changePassword">Change Password</option>
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
                $db = DBInterface::getInstance();
                error_log("Fetching games now.");
                $data = $db->fetchGames();
                ?>
                <table>
                    <tr> 
                        <th>  ID  </th>
                        <th>Status</th>
                        <th>Number of players</th>
                        <th>Current player turn</th>
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
                                    echo "Winner : ".$data[$key]["winner"];
                                    break;
                                case GameStatus::FINISHEDDRAW :
                                    echo "Draw";
                                    break;
                                default:
                                    break;
                                } ?></td>
                        <td><?php echo $data[$key]["nb_player"]; ?></td>
                        <td><?php echo $data[$key]["current_player_turn"]; ?></td>
                        <td>
                            <form action="php/PLACEHOLDERjoinGame.php" method="post">
                                <input type="hidden" name="id_game" value="<?php echo $data[$key]["id"]; ?>">
                                <input type="submit" value="Join" name="JoinForm">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
            <div class="buttons-container">
                <form action="php/testCreateGame.php" method="post">
                    <input type="submit" name="createGame" value="Create a game">
                </form>
            </div>
        </div>
    </div>
</body>

</html>