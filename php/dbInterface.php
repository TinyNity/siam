<?php
include_once "EStatus.php";
include_once "EGameStatus.php";
include_once "utils.php";
//? This file is used to handle every interaction with the master database

class DbInterface {
    private static $instance;
    function __construct() {
        $this->init();
    }

    private function init() : void {
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        $DBExistenceQuery = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        if (! ($DBExistenceQuery && $DBExistenceQuery->fetchArray())) {
            try {
                //* Note : SQLite does not have a separate Boolean storage class. Instead, Boolean values are stored as integers 0 (false) and 1 (true). 
                $db->query("
                    CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        username TEXT NOT NULL,
                        password TEXT NOT NULL,
                        isAdmin INTEGER NOT NULL
                        )
                ");

                $db->query("
                    CREATE TABLE IF NOT EXISTS players (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        reserved_piece INTEGER NOT NULL,
                        id_piece INTEGER,
                        id_user INTEGER NOT NULL,
                        id_game INTEGER NOT NULL,
                        FOREIGN KEY(id_user) REFERENCES users(id),
                        FOREIGN KEY(id_game) REFERENCES games(id)
                        )
                    ");

                $db->query("
                    CREATE TABLE IF NOT EXISTS games (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        status INTEGER NOT NULL,
                        nb_player INTEGER NOT NULL,
                        current_player_turn INTEGER,
                        winner INTEGER,
                        FOREIGN KEY(current_player_turn) REFERENCES players(id),
                        FOREIGN KEY(winner) REFERENCES players(id)
                        )
                    ");
                
                $db->query("
                    CREATE TABLE IF NOT EXISTS gameboard_cell (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        row INTEGER NOT NULL,
                        column INTEGER NOT NULL,
                        direction INTEGER,
                        id_piece INTEGER,
                        id_player INTEGER,
                        id_game INTEGER NOT NULL,
                        FOREIGN KEY(id_piece) REFERENCES pieces(id),
                        FOREIGN KEY(id_player) REFERENCES players(id),
                        FOREIGN KEY(id_game) REFERENCES games(id)
                        )
                    ");

                $db->query("
                    CREATE TABLE IF NOT EXISTS pieces(
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        piece_name TEXT NOT NULL
                        )
                    ");
                
                $this->createPiecesData();
            } catch (Exception $exception) {
                error_log($exception->getMessage());
            } finally {
                $db->close();
            }
        }
    }

    public static function getInstance(): DbInterface { //? Insures that there is only one instance of the DbInterface class
        if (!self::$instance) {
            self::$instance = new DbInterface();
        }
        return self::$instance;
    }

    public function checkUserExistence(String $username) : bool {
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        error_log("Checking " . $username);
        $pQuery = $db->prepare("
            SELECT username FROM users c
            WHERE EXISTS (
                SELECT 1 FROM users
                WHERE username = :username
            )
        ");
        $pQuery->bindParam(':username', $username, SQLITE3_TEXT);
        $res = $pQuery->execute();

        if (!$res) {
            $db->close();
            return false;
        }

        $row = $res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return ($row !== false);
    }

    public function checkGameExistence(int $id_game) : bool {
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        error_log("Checking game " . $id_game);
        $pQuery = $db->prepare("
            SELECT id FROM games c
            WHERE EXISTS (
                SELECT 1 FROM games
                WHERE id = :id_game
            )
        ");
        $pQuery->bindParam(':id_game', $id_game, SQLITE3_INTEGER);
        $res = $pQuery->execute();

        if (!$res) {
            $db->close();
            return false;
        }

        $row = $res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return ($row !== false);
    }

    public function checkPlayerExistence(int $id_player):bool{
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        error_log("Checking player ".$id_player);
        $pQuery = $db->prepare("
            SELECT id FROM players c
            WHERE EXISTS (
                SELECT 1 FROM players
                WHERE id = :id_player
            )
        ");
        $pQuery->bindParam(':id_player', $id_player, SQLITE3_INTEGER);
        $res = $pQuery->execute();

        if (!$res) {
            $db->close();
            return false;
        }

        $row = $res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return ($row !== false);
    }

    public function checkPlayerInGame(int $id_player,int $id_game):bool{
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            $db->close();
            return false;
        }
        if (!$this->checkPlayerExistence($id_player)){
            error_log(EStatus::NOPLAYER);
            $db->close();
            return false;
        }
        error_log("Checking player ".$id_player." in game ".$id_game);
        $pQuery=$db->prepare("
            SELECT id_game FROM players WHERE id=:id_player
            ");
        $pQuery->bindParam(":id_player",$id_player,SQLITE3_INTEGER);
        $res=$pQuery->execute();

        if (!$res){
            $db->close();
            return false;
        }
        $row=$res->fetchArray(SQLITE3_ASSOC);
        $status=$row["id_game"]==$id_game;
        if($status) error_log(EStatus::PLAYERINGAME);
        else error_log(EStatus::PLAYERNOTINGAME);
        $db->close();
        return $status;
    }

    public function checkUserIsPlayer(String $username, int $id_player) : bool {
        $db=new  SQLite3(PARENTPATH."/db.sqlite");
        if (!$this->checkUserExistence($username)){
            error_log(EStatus::NOUSER);
            $db->close();
            return false;
        }
        if (!$this->checkPlayerExistence($id_player)){
            error_log(EStatus::NOPLAYER);
            $db->close();
            return false;
        }

        error_log("Checking user ".$username." is player ".$id_player);
        $pQuery=$db->prepare("
            SELECT id_user 
            FROM players p
            INNER JOIN users u
                on p.id_user = u.id
            WHERE
                u.username=:username AND p.id=:id_player
            ");
        $pQuery->bindParam(":id_player",$id_player,SQLITE3_INTEGER);
        $pQuery->bindParam(":username",$username,SQLITE3_TEXT);
        $res=$pQuery->execute();

        if (!$res){
            $db->close();
            return false;
        }
        $row = $res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        $status=$row!==false;
        if ($status) error_log(EStatus::USERISPLAYER);
        else error_log(EStatus::USERISNOTPLAYER);
        return $status;
    }
    public function createPiecesData(){
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                            INSERT INTO pieces(piece_name)
                            VALUES (:piece_name)
                            "
                        );
        $rock="rocher";
        $pQuery->bindParam(":piece_name",$rock,SQLITE3_TEXT);
        $pQuery->execute();
        $elephant="elephant";
        $pQuery->bindParam(":piece_name",$elephant,SQLITE3_TEXT);
        $pQuery->execute();
        $rhinoceros="rhinoceros";
        $pQuery->bindParam(":piece_name",$rhinoceros,SQLITE3_TEXT);
        $pQuery->execute();
        $db->close();
    }

    public function registerAccount(String $username, String $password, bool $admin = false) : String {
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        if ($this->checkUserExistence($username)) { //? User is in database, can't create a new account with the same username
            return EStatus::USERINDB;
        }
        $pQuery = $db->prepare("
            INSERT INTO users (username, password, isAdmin)
            VALUES (:username, :password, :isAdmin)
        ");
        try {
            $pQuery->bindParam(":username", $username, SQLITE3_TEXT);
            $pQuery->bindParam(":password", $password, SQLITE3_TEXT);
            $pQuery->bindParam(":isAdmin", $admin, SQLITE3_TEXT);
            $pQuery->execute();
            return EStatus::USERCREATED;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            $db->close();
        }
    }

    public function loginUser(String $username, String $password) : String {
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        if (!$this->checkUserExistence($username)) { //? Can't log in to an unregistered account
            return EStatus::NOUSER;
        }
        $pQuery = $db->prepare("SELECT password FROM users WHERE username = :username");
        $pQuery->bindParam(":username", $username, SQLITE3_TEXT);
        $result = $pQuery->execute();

        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $hashedPW = $row['password'];
            error_log("Hashed pw : $hashedPW");
            error_log("Clear pw : $password");
            if (password_verify($password, $hashedPW)) { 
                return EStatus::APPROVED;
            }
        }
        return EStatus::REJECTED;
    }


    public function changePassword(String $username, String $newPassword) : String {
        error_log("Changing $username's password to $newPassword..");
        $db = new SQLite3(PARENTPATH."/db.sqlite");
        if (!$this->checkUserExistence($username)) { //? Can't access an unregistered account
            return EStatus::NOUSER;
        }
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
        $pQuery = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
        $pQuery->bindValue(1, $hashedPassword);
        $pQuery->bindValue(2, $username);
        
        $success = $pQuery->execute();
        $pQuery->close();

        if ($success === false) {
            return EStatus::REJECTED;
        } else {
            return EStatus::APPROVED;
        }
    }

    public function createGame(String $username) : string{
        if (!$this->checkUserExistence($username)){ //? Can't create a game if the user doesn't exist
            return EStatus::NOUSER;
        }
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                            INSERT INTO games (status,nb_player,current_player_turn)
                            VALUES (:status,0,null) 
                            ");
        $status=GameStatus::NOTSTARTED;
        try{
            $pQuery->bindParam(":status",$status,SQLITE3_INTEGER);
            $pQuery->execute();
            $id_game=$db->lastInsertRowID();
            $status=$this->addPlayerToGame($username, $id_game);
            error_log("createGame status 1 : $status");
            $status=$this->createGameBoard($id_game);
            error_log("createGame status 2 : $status");
            return EStatus::GAMECREATED;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            $db->close();
        }
    }

    public function addPlayerToGame(String $username, int $id_game) : int {
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return -1;
        }
        if ($this->isGameFull($id_game)){
            error_log(EStatus::GAMEISFULL);
            return -1;
        }
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $useridQuery=$db->prepare("SELECT id FROM users WHERE username=:username");
        $useridQuery->bindParam(":username",$username,SQLITE3_TEXT);
        $result=$useridQuery->execute();
        $row=$result->fetchArray(SQLITE3_ASSOC);
        $id_user=$row["id"];
        $pQuery=$db->prepare("
                            INSERT INTO players (reserved_piece,id_piece,id_user,id_game)
                            VALUES (5,null,:id_user,:id_game) 
                            ");
        $pQuery->bindParam(":id_user",$id_user);
        $pQuery->bindParam(":id_game",$id_game);
        $pQuery->execute();
        $db->query("UPDATE games SET nb_player=nb_player + 1 WHERE id = $id_game");
        $player=$db->lastInsertRowID();
        $db->close();
        if ($this->isGameFull($id_game)){
            $this->startGame($id_game);
        }
        error_log(EStatus::PLAYERADDED);
        return $player;
    }

    public function isGameFull(int $id_game) : bool {
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("SELECT nb_player FROM games WHERE id=:id_game");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        if ($result){
            $row=$result->fetchArray(SQLITE3_ASSOC);
            $nb_player=$row["nb_player"];
        }
        $db->close();
        return $nb_player==2;
    }
    

    public function createGameBoard(int $id_game) : string {
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        
        $pQuery=$db->prepare("
                            INSERT INTO gameboard_cell(row,column,direction,id_player,id_piece,id_game)
                            VALUES (:row,:column,null,null,:id_piece,:id_game)");
        $board_size=5;
        $rock_row=2;
        $min_rock_col=1;
        $max_rock_col=3;
        for ($i=0;$i<$board_size;$i++){
            for ($j=0;$j<$board_size;$j++){
                $pQuery->bindParam(":row",$i,SQLITE3_INTEGER);
                $pQuery->bindParam(":column",$j,SQLITE3_INTEGER);
                if ($i==$rock_row && $j>=$min_rock_col && $j<=$max_rock_col){
                    $id_piece=1; //Rock
                    $pQuery->bindParam(":id_piece",$id_piece,SQLITE3_INTEGER);
                }
                else {
                    $id_piece=null; //Void
                    $pQuery->bindParam(":id_piece",$id_piece,SQLITE3_NULL);
                }
                $pQuery->bindParam(":id_game",$id_game);

                $pQuery->execute();
            }
        }
        $db->close();
        return EStatus::GAMEBOARDCREATED;
    }

    public function getGameboard(int $id_game) : array {
        $gameboardData=array();
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return $gameboardData;
        }
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                            SELECT * FROM gameboard_cell
                            WHERE id_game=:id_game
                            ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        if (!$result){
            $db->close();
            return $gameboardData;
        }
        while ($row=$result->fetchArray(SQLITE3_ASSOC)){
            $gameboardData[]=$row;
        };
        $db->close();
        return $gameboardData;
    }

    function updateGameboardCell(int $id_game,int $row,int $column,$id_piece,$id_player,$direction):string{
        if(!$this->checkGameExistence($id_game)){
            return EStatus::NOGAME;
        }
        error_log("Trying to update gameboard of game ".$id_game);
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                        UPDATE gameboard_cell 
                        SET id_piece=:id_piece, id_player=:id_player,direction=:direction
                        WHERE id_game=:id_game AND row=:row AND column=:column
                        ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $pQuery->bindParam(":row",$row,SQLITE3_INTEGER);
        $pQuery->bindParam(":column",$column,SQLITE3_INTEGER);
        $pQuery->bindParam(":id_player",$id_player,SQLITE3_INTEGER);
        $pQuery->bindParam(":id_piece",$id_piece,SQLITE3_INTEGER);
        $pQuery->bindParam(":direction",$direction,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        if (!$result){
            $db->close();
            return EStatus::GAMEBOARDUPDATEFAILED;
        }
        $db->close();
        return EStatus::GAMEBOARDUPDATED;
    }

    function getPlayers(int $id_game) : array{
        $players=array();
        if(!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return $players;
        }
        error_log("Trying to get players of game ".$id_game);
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                            SELECT p.id,p.reserved_piece,p.id_piece,u.username
                            FROM players p
                            INNER JOIN users u
                                ON u.id=p.id_user
                            WHERE id_game=:id_game
                            ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        if (!$result){
            $db->close();
            error_log(EStatus::NOPLAYER);
            return $players;
        }

        while ($row=$result->fetchArray(SQLITE3_ASSOC)){
            $players[]=$row;
        }

        $db->close();
        return $players;
    }

    function getUserFromPlayer(int $id_player){
        if (!$this->checkPlayerExistence($id_player)){
            error_log(EStatus::NOPLAYER);
            return;
        }
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                    SELECT u.username
                    FROM users u
                    INNER JOIN players p
                        ON p.id_user=u.id
                    WHERE
                        p.id=:id_player");
        $pQuery->bindParam(":id_player",$id_player,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        $username=$result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $username["username"];
    }

    function getPlayerFromUser(int $id_game,string $username) : int {
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return -1;
        }

        if (!$this->checkUserExistence($username)){
            error_log(EStatus::NOUSER);
            return -1;
        }

        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                SELECT p.id 
                FROM players p
                INNER JOIN users u
                    on p.id_user = u.id
                WHERE
                    u.username=:username AND p.id_game=:id_game
        ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $pQuery->bindParam(":username",$username,SQLITE3_TEXT);
        $result=$pQuery->execute();
        if (!$result){
            return -1;
        }
        $player=$result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        if ($player["id"]==null){
            return 0;
        }
        return $player["id"];
    }

    function getGameData(int $id_game) : array{
        $game=array();
        if(!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return $game;
        }
        
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("
                            SELECT * FROM games WHERE id=:id_game
                            ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        $game=$result->fetchArray(SQLITE3_ASSOC);

        $db->close();
        return $game;
    }


    function fetchGames($status) : array {
        $db = new SQLite3(PARENTPATH."/db.sqlite");

        $query = $db->query("
            SELECT g.* 
            FROM games g
            INNER JOIN players p
                ON g.id=p.id_game
            WHERE g.status=$status
            ORDER BY id ASC
        ");
    
        if (! $query) {
            error_log( "[-] :" . $db->lastErrorMsg());
            return [];
        }
        $ret = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $ret[] = $row;
        }
        $db->close();
        return $ret;
    }

    function getUser($username){
        $db=new SQLite3(PARENTPATH."/db.sqlite");

        $pQuery=$db->prepare("
                            SELECT id 
                            FROM users 
                            WHERE username=:username
                            ");
        $pQuery->bindParam(":username",$username,SQLITE3_TEXT);
        $res=$pQuery->execute();
        if (!$res){
            error_log("[-] :".$db->lastErrorMsg());
            return -1;
        }

        $ret=$res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $ret["id"];
    }

    function fetchGamesUser($username,...$statuses) : array {
        $id_user=$this->getUser($username);

        $db = new SQLite3(PARENTPATH."/db.sqlite");
        $whereClause = '';
        if (!empty($statuses)) {
            $whereClause = "WHERE status IN (" . implode(',', $statuses) . ")";
        }
        $query = $db->query("
            SELECT g.*
            FROM games g
            INNER JOIN players p
                ON g.id=p.id_game
            $whereClause AND p.id_user=$id_user
            ORDER BY id ASC
        ");
    
        if (! $query) {
            error_log( "[-] :" . $db->lastErrorMsg());
            return [];
        }
        $ret = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $ret[] = $row;
        }
        $db->close();
        return $ret;
    }

    function countWin($username){
        $games=$this->fetchGamesUser($username,GameStatus::FINISHEDDRAW,GameStatus::FINISHEDWIN);
        $cpt=0;
        for ($i=0;$i<count($games);$i++){
            $player=$games[$i]["winner"];
            if ($player!=null && $this->getUserFromPlayer($player)==$username) $cpt++;
        }
        return $cpt;
    }

    function startGame($id_game){
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return;
        }
        $players=$this->getPlayers($id_game);
        $coin=random_int(0,1);
        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("UPDATE games SET status=:status, current_player_turn=:id_player WHERE id=:id_game");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $pQuery->bindParam(":id_player",$players[$coin]["id"],SQLITE3_INTEGER);
        $status=GameStatus::STARTED;
        $pQuery->bindParam(":status",$status,SQLITE3_INTEGER);
        $pQuery->execute();

        $pQuery=$db->prepare("UPDATE players SET id_piece=:id_piece WHERE id=:id_player");
        
        for ($i=0;$i<2;$i++){
            $piece=2+$i;
            $pQuery->bindParam(":id_piece",$piece,SQLITE3_INTEGER);
            $pQuery->bindParam(":id_player",$players[$i]["id"],SQLITE3_INTEGER);
            $pQuery->execute();
        }

        $db->close();
    }

    function updateGameStatus($id_game,$status,$current_player_turn,$winner){
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return;
        }
        $db=new SQLite3(PARENTPATH."/db.sqlite");

        $pQuery=$db->prepare("UPDATE games SET status=:status,winner=:winner, current_player_turn=:current_player_turn WHERE id=:id_game");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $pQuery->bindParam(":status",$status,SQLITE3_INTEGER);
        $pQuery->bindParam(":winner",$winner,SQLITE3_INTEGER);
        $pQuery->bindParam(":current_player_turn",$current_player_turn,SQLITE3_INTEGER);
        $pQuery->execute();
        
        $db->close();
    }

    function updatePlayerData($id_player,$reserved_piece){
        if (!$this->checkPlayerExistence($id_player)){
            error_log(EStatus::NOPLAYER);
            return;
        }

        $db=new SQLite3(PARENTPATH."/db.sqlite");
        $pQuery=$db->prepare("UPDATE players SET reserved_piece=:reserved_piece WHERE id=:id_player");
        $pQuery->bindParam(":id_player",$id_player,SQLITE3_INTEGER);
        $pQuery->bindParam(":reserved_piece",$reserved_piece,SQLITE3_INTEGER);
        $pQuery->execute();
        $db->close();
    }
}