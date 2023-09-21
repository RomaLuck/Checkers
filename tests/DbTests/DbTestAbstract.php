<?php

namespace App\Tests\DbTests;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

//require __DIR__ . '/../../vendor/autoload.php';

class DbTestAbstract extends TestCase
{
    protected PDO $pdo;
    protected string $table = 'CheckerDeskTest';

    protected function setUp(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $this->testDatabaseConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
            $this->assertTrue(true);
        } catch (PDOException $e) {
            $this->fail('Помилка підключення до бази даних: ' . $e->getMessage());
        }
    }
}