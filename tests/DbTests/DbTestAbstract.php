<?php

namespace App\Tests\DbTests;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

class DbTestAbstract extends TestCase
{
    protected static ?PDO $pdo;
    protected static string $table = 'CheckerDeskTest';

    public static function setUpBeforeClass(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            self::$pdo = new PDO($dsn, $username, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->exec('CREATE TABLE IF NOT EXISTS ' . self::$table . ' (
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

    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP TABLE IF EXISTS ' . self::$table);
        self::$pdo = null;
    }
}