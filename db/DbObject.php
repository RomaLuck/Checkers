<?php

namespace CheckersOOP\db;

class DbObject
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function showAllItems(string $where): array
    {
        $result = [];
        $sql = 'SELECT * FROM CheckerDesk';
        $stmt = $this->db->connect()->query($sql);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row[$where];
        }
        return $result;
    }

    public function showItems(string $column, string $where, string $filter): array
    {
        $result = [];
        $sql = 'SELECT * FROM CheckerDesk WHERE ' . $where . '=?';
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$filter]);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row[$column];
        }
        return $result;
    }

    public function showItem(string $column, string $where, string $filter): string
    {
        $result = [];
        $sql = 'SELECT * FROM CheckerDesk WHERE ' . $where . '=?';
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$filter]);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row[$column];
        }
        return implode($result);
    }

    public function insertItems(string $id, string $data): void
    {
        $sql = 'INSERT INTO CheckerDesk VALUES (?,?)';
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$id, $data]);
    }

    public function updateItems(string $column, string $data, string $where, string $filter): void
    {
        $sql = 'UPDATE CheckerDesk SET ' . $column . '=? WHERE ' . $where . '=?';
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$data, $filter]);
    }

    public function deleteAll()
    {
        $sql = 'TRUNCATE TABLE CheckerDesk';
        $this->db->connect()->query($sql);
    }
}
