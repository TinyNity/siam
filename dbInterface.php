<?php
include_once "EStatus.php";
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
                        reserved_piece INT NOT NULL,
                        id_user INTEGER NOT NULL,
                        id_game INTEGER NOT NULL,
                        FOREIGN KEY(id_user) REFERENCES users(id),
                        FOREIGN KEY(id_game) REFERENCES games(id)
                        )
                    ");

                $db->query("
                    CREATE TABLE IF NOT EXISTS games (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        status TEXT NOT NULL,
                        nb_player INT NOT NULL,
                        current_player_turn INTEGER NOT NULL,
                        FOREIGN KEY(current_player_turn) REFERENCES players(id)
                        )
                    ");
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

    public function createGame(){
        
    }
}