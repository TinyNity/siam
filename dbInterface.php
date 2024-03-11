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
        $db = new SQLite3("./db.sqlite");
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
                        FOREIGN KEY(current_player_turn) REFERENCES players(id)
                        )
                    ");
                
                $db->query("
                    CREATE TABLE IF NOT EXISTS gameboard_cell (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        row INTEGER NOT NULL,
                        column INTEGER NOT NULL,
                        rotation INTEGER NOT NULL,
                        id_piece INTEGER,
                        id_player INTEGER,
                        id_game INTEGER NOT NULL,
                        FOREIGN KEY(id_piece) REFERENCES pieces(id),
                        FOREIGN KEY(id_player) REFERENCES player(id),
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
                echo $exception->getMessage();
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
        $db = new SQLite3("./db.sqlite");
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
        $db = new SQLite3("./db.sqlite");
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
        $db=new SQLite3("./db.sqlite");
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
        $db=new SQLite3("./db.sqlite");
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
        $db=new  SQLite3("./db.sqlite");
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
        $db=new SQLite3("./db.sqlite");
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
        $db = new SQLite3("./db.sqlite");
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
            echo $e->getMessage();
            return false;
        } finally {
            $db->close();
        }
    }

    public function loginUser(String $username, String $password) : string {
        $db = new SQLite3("./db.sqlite");
        if (!$this->checkUserExistence($username)) { //? Can't log in to an unregistered account
            return EStatus::NOUSER;
        }
        $pQuery = $db->prepare("SELECT password FROM users WHERE username = :username");
        $pQuery->bindParam(":username", $username, SQLITE3_TEXT);
        $result = $pQuery->execute();

        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $hashedPW = $row['password'];
            error_log("Hashed pw : " . $hashedPW);
            error_log("Clear pw : " . $password);
            if (password_verify($password, $hashedPW)) { 
                return EStatus::APPROVED;
            }
        }
        return EStatus::REJECTED;
    }

    public function createGame(String $username) : string{
        if (!$this->checkUserExistence($username)){ //? Can't create a game if the user doesn't exist
            return EStatus::NOUSER;
        }
        $db=new SQLite3("./db.sqlite");
        $pQuery=$db->prepare("
                            INSERT INTO games (status,nb_player,current_player_turn)
                            VALUES (:status,0,null) 
                            ");
        $status=GameStatus::NOTSTARTED;
        try{
            $pQuery->bindParam(":status",$status,SQLITE3_INTEGER);
            $pQuery->execute();
            $id_game=$db->lastInsertRowID();
            $status=$this->addPlayerToGame($username,$id_game);
            error_log($status);
            $status=$this->createGameBoard($id_game);
            error_log($status);
            return EStatus::GAMECREATED;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        } finally {
            $db->close();
        }
    }

    public function addPlayerToGame(String $username, int $id_game) : string {
        if (!$this->checkGameExistence($id_game)){
            return EStatus::NOGAME;
        }
        if ($this->gameIsFull($id_game)){
            return EStatus::GAMEISFULL;
        }
        $db=new SQLite3("./db.sqlite");
        $useridQuery=$db->prepare("SELECT id FROM users WHERE username=:username");
        $useridQuery->bindParam(":username",$username,SQLITE3_TEXT);
        $result=$useridQuery->execute();
        $row=$result->fetchArray(SQLITE3_ASSOC);
        $id_user=$row["id"];
        $pQuery=$db->prepare("
                            INSERT INTO players (reserved_piece,id_user,id_game)
                            VALUES (3,:id_user,:id_game) 
                            ");
        $pQuery->bindParam(":id_user",$id_user);
        $pQuery->bindParam(":id_game",$id_game);
        $pQuery->execute();
        $db->query("UPDATE games SET nb_player=nb_player + 1 WHERE id = $id_game");
        $db->close();
        return EStatus::PLAYERADDED;
    }

    public function gameIsFull(int $id_game) : bool {
        $db=new SQLite3("./db.sqlite");
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
        $db=new SQLite3("./db.sqlite");
        
        $pQuery=$db->prepare("
                            INSERT INTO gameboard_cell(row,column,rotation,id_player,id_piece,id_game)
                            VALUES (:row,:column,0,null,:id_piece,:id_game)");
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

    public function getGameBoard(int $id_game) : array {
        if (!$this->checkGameExistence($id_game)){
            error_log(EStatus::NOGAME);
            return array();
        }
        $db=new SQLite3("./db.sqlite");
        $pQuery=$db->prepare("
                            SELECT * FROM gameboard_cell
                            WHERE id_game=:id_game
                            ");
        $pQuery->bindParam(":id_game",$id_game,SQLITE3_INTEGER);
        $result=$pQuery->execute();
        if (!$result){
            return array();
        }
        $gameboardData=array();
        while ($row=$result->fetchArray(SQLITE3_ASSOC)){
            $gameboardData[]=$row;
        };
        return $gameboardData;
    }
}