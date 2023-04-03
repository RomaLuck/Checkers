<?php

declare(strict_types=1);

require_once 'Database.php';

class DbObject
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

    public function showAllTeam(): array
    {
        $sql = 'SELECT * FROM ' . $this->tableName;
        $result = $this->db->query($sql);
        return $result;
    }

    public function insertChecker($data): void
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(pieceName) VALUES (?)';
        $this->db->query($sql, $data);
    }

    public function deleteChecker($data): void
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE pieceName =?';
        $this->db->query($sql, $data);
    }

    public function deleteTeam(): void
    {
        $deleteCache = 'TRUNCATE TABLE ' . $this->tableName;
        $this->db->query($deleteCache);
    }
}
