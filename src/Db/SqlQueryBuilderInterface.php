<?php

namespace App\Db;

interface SqlQueryBuilderInterface
{
    public function select(string $table, array $fields): SqlQueryBuilderInterface;

    public function insert(string $table, array $fields):SqlQueryBuilderInterface;

    public function update(string $table, array $fields): SqlQueryBuilderInterface;

    public function delete(string $table): SqlQueryBuilderInterface;

    public function where(string $field, string $value, string $operator = '='): SqlQueryBuilderInterface;

    public function limit(int $start, int $offset): SqlQueryBuilderInterface;

    public function getSQL(): string;

    public function setParameters(array $parameters): SqlQueryBuilderInterface;

    public function getQuery(): SqlQueryBuilderInterface;

    public function findAll(): array;

    public function findOne(): CheckerObject;
}