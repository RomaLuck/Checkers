<?php

namespace App\Tests\DbTests;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertTrue;

class DbTestAbstract extends TestCase
{
    protected PDO $pdo;
    protected string $table = 'CheckerDeskTest';

    protected function setUp(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $this->testDatabaseConnection();
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS ' . $this->table);
    }

    public function testDatabaseConnection(): void
    {
        $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cell VARCHAR(10),
            team VARCHAR(100),
            figure VARCHAR(100)
        )');
            self::assertTrue(true);
        } catch (PDOException $e) {
            self::fail('Database connection error: ' . $e->getMessage());
        }
    }
}