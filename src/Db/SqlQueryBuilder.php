<?php

namespace App\Db;

use PDO;
use PDOStatement;
use stdClass;

class SqlQueryBuilder implements SqlQueryBuilderInterface
{
    private PDO $pdo;
    private stdClass $query;
    private PDOStatement $stmt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function reset(): void
    {
        $this->query = new stdClass();
        $this->query->parameters = [];
    }

    public function select(string $table, array $fields = []): self
    {
        $this->reset();
        if ($fields !== []) {
            $this->query->base = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;
        } else {
            $this->query->base = 'SELECT * FROM ' . $table;
        }

        $this->query->type = 'select';

        return $this;
    }

    public function insert(string $table, array $fields): self
    {
        $keys = array_keys($fields);
        $values = array_values($fields);

        $sql = "INSERT INTO $table (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ")";

        $this->reset();
        $this->query->base = $sql;
        $this->query->type = 'insert';

        return $this;
    }

    public function delete(string $table): self
    {
        $this->reset();
        $this->query->base = 'DELETE FROM ' . $table;
        $this->query->type = 'delete';

        return $this;
    }

    public function update(string $table, array $fields): self
    {
        $query = '';
        foreach ($fields as $key => $value) {
            $query .= $key . '=' . $value . ',';
        }
        $this->reset();
        $this->query->base = 'UPDATE ' . $table . ' SET ' . rtrim($query, ',');
        $this->query->type = 'update';

        return $this;
    }

    public function where(string $field, string $value, string $operator = '='): self
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new \RuntimeException("WHERE can only be added to SELECT, UPDATE OR DELETE");
        }
        $this->query->where[] = "$field $operator $value";

        return $this;
    }

    public function limit(int $start, int $offset): self
    {
        if ($this->query->type !== 'select') {
            throw new \RuntimeException("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    public function getSQL(): string
    {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        if (isset($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= ";";

        return $sql;
    }

    public function setParameters(array $parameters): self
    {
        $this->query->parameters = $parameters;

        return $this;
    }

    public function getQuery(): self
    {
        $this->stmt = $this->pdo->prepare($this->getSQL());
        if ($this->query->parameters !== []) {
            $this->stmt->execute($this->query->parameters);
        } else {
            $this->stmt->execute();
        }

        return $this;
    }

    public function findAll(): array
    {
        $result = [];
        while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    public function findOne(): array
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteAll($table): void
    {
        $sql = 'TRUNCATE TABLE ' . $table;
        $this->pdo->query($sql);
    }
}