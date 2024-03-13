<!DOCTYPE html>
<?php
//! HOME PAGE FOR LOGGED IN USERS

include_once "dbInterface.php";
include_once "utils.php";

if (isset($_COOKIE["username"])) {
    error_log("User cookie is set as " . $_COOKIE["username"] );
    $username = $_COOKIE["username"];
} else {
    error_log("User cookie is not set");
    redirect("./login.php");
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleHome.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="scriptHome.js" defer></script>
    <title>SIAM - Home</title>
</head>
<body>
    <div class="ribbon">
        <p>You're logged in as </p>
        <form id="dcForm" action="./logout.php">
            <select id="dropdownMenu" onchange="handleDropdownChange(this)">
                <option value="disconnect">Disconnect</option>
                <option value="changePassword">Change Password</option>
                <option value="adminDashboard">Admin Dashboard</option>
                <option value="" disabled selected><?php echo $username; ?></option>
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
                $data = $db->fetchGames();
                foreach ($data as $key => $value) {
                    echo '
            <div class="game">
                ';
                    echo '<p style="display:inline-block;"> ';
                    echo $data[$key]["status"]. " </p>";
                    //? Username color
                    echo '
                <p style="display:inline-block">';
                    echo $data[$key]["nb_player"]. " :";
                    echo "</p>";

                    echo '
                <p style="display:inline-block;">';
                    echo $data[$key]["current_player_turn"];
                    echo "</p>";
                    echo '
            </div>
                    ';
                }
                ?>
            </div>
            <div class="buttons-container">
                <button>Button 1</button>
                <button>Button 2</button>
                <button>Button 3</button>
                <button>Button 4</button>
            </div>
        </div>
    </div>
</body>
</html>
