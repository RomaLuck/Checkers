<?php

namespace CheckersOOP\db;

use PDO;
use PDOException;

require_once "config.php";

class Database
{

    public $connection;

    public function __construct()
    {
        $this->db_connertion();
    }


    public function db_connertion()
    {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=CheckersOOP", username, password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function query(string $sql, string $prepared = null)
    {

        $sth = $this->connection->prepare($sql);
        if ($prepared !== Null) {
            $sth->bindParam(1, $prepared);
        }
        $sth->execute();
        $result = [];
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return  $result;
    }
}
