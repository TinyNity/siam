<?php

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

    public function checkUserExistence(String $username) : bool{
        $db = new SQLite3("./db.sqlite");
        $query = $db->query("
            SELECT username FROM users c
            WHERE EXISTS (
                    SELECT 1 FROM users
                    WHERE $username = c.username )
        ");
        if (!$query) {
            return false;
        }
        return true;
    }

    public function registerAccount(String $username, String $password, bool $admin = 0) : bool {
        $db = new SQLite3("./db.sqlite");
        if ($this->checkUserExistence($username)) { //? User is in database, can't create a new account with the same username
            return false;
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
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        } finally {
            $db->close();
        }
    }
}