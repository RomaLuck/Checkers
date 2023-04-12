<?php

namespace CheckersOOP\db;

class DbObject
{
    public $tableName;
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function showAllItems(): array
    {
        $sql = 'SELECT * FROM ' . $this->tableName;
        $result = $this->db->query($sql);
        return $result;
    }

    public function showItem($id)
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE id= "' . $id . '"';
        $result = $this->db->query($sql);
        return array_shift($result);
    }

    public function showItems($column): array
    {
        $items = [];
        $sql = 'SELECT ' . $column . ' FROM ' . $this->tableName;
        $result = $this->db->query($sql);
        foreach ($result as $key => $value) {
            $items[] = array_shift($value);
        }
        return $items;
    }

    public function insertItem(string $column, string $data): void
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(' . $column . ') VALUES (?)';
        $this->db->query($sql, $data);
    }

    public function deleteItem(string $column, string $data): void
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $column . ' =?';
        $this->db->query($sql, $data);
    }

    public function updateItem(string $column, ?string $data, string $id): void
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET ' . $column . '=?'.' WHERE id="' . $id . '"';
        $this->db->query($sql, $data);
    }

    public function deleteItems(): void
    {
        $deleteCache = 'TRUNCATE TABLE ' . $this->tableName;
        $this->db->query($deleteCache);
    }
}
