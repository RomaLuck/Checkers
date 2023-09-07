<?php

namespace CheckersOOP\tests\dbTests;

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function setUp(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
    }
    public function testDatabaseConnection(): void
    {
        $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            $pdo = new PDO($dsn, $username, $password);
            $this->assertTrue(true);
        } catch (PDOException $e) {
            $this->fail('Помилка підключення до бази даних: ' . $e->getMessage());
        }
    }
}