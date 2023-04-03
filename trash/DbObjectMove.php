<?php

declare(strict_types=1);

require_once 'Database.php';

class DbObjectMove
{
    public $tableName;
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function showAllMoves(): array
    {
        $sql = 'SELECT * FROM ' . $this->tableName;
        $result = $this->db->query($sql);
        return $result;
    }

    public function insertMove($data): void
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(pieceName) VALUES (?)';
        $this->db->query($sql, $data);
    }

    public function showLastMove(): array
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' ORDER BY id DESC LIMIT 1;';
        $result = $this->db->query($sql);
        return $result;
    }

    public function deleteMovies(): void
    {
        $deleteCache = 'TRUNCATE TABLE ' . $this->tableName;
        $this->db->query($deleteCache);
    }
}
