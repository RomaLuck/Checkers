<?php

namespace CheckersOOP\db;

require_once "config.php";

class Database
{
    public function connect()
    {
        try {
            $pdo = new \PDO("mysql:host=localhost;dbname=CheckersOOP", username, password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
