<?php

namespace CheckersOOP\src\db;

use PDO;

class DbObject
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function showAllItems(string $where): array
    {
        $result = [];
        $sql = 'SELECT * FROM CheckerDesk';
        $stmt = $this->db->connect()->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row[$where];
        }
        return $result;
    }

    public function showItems(string $column, array $condition): array
    {
        $result = [];
        $dataForExecute = [];
        $sql = "SELECT * FROM CheckerDesk WHERE ";
        foreach ($condition as $col => $filter):
            $sql .= $col . "=? AND ";
            $dataForExecute[] = $filter;
        endforeach;
        $sql = rtrim($sql, ' AND');
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($dataForExecute);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row[$column];
        }
        return $result;
    }

    public function showItem(string $column, array $condition): string
    {
        return implode($this->showItems($column, $condition));
    }

    public function insertItems(array $data): void
    {
        $dataForExecute = [];
        $sql = "INSERT INTO CheckerDesk(";
        foreach (array_keys($data) as $key) :
            $sql .= $key . ",";
        endforeach;
        $sql = rtrim($sql, ",");
        $sql .= ") VALUES (";
        foreach ($data as $value) :
            $sql .= "?,";
            $dataForExecute[] = $value;
        endforeach;
        $sql = rtrim($sql, ",");
        $sql .= ")";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($dataForExecute);
    }

    public function updateItems(array $data, array $condition): void
    {
        $dataForExecute = [];
        $sql = "UPDATE CheckerDesk SET ";
        foreach ($data as $key => $value):
            $sql .= $key . "='" . $value . "',";
        endforeach;
        $sql = rtrim($sql, ',');
        $sql .= " WHERE ";
        foreach ($condition as $col => $filter):
            $sql .= $col . "=? AND ";
            $dataForExecute[] = $filter;
        endforeach;
        $sql = rtrim($sql, ' AND');
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($dataForExecute);
    }

    public function deleteAll(): void
    {
        $sql = 'TRUNCATE TABLE CheckerDesk';
        $this->db->connect()->query($sql);
    }
}