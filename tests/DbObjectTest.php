<?php

use PHPUnit\Framework\TestCase;
use CheckersOOP\db\Database;
use CheckersOOP\db\DbObject;

class DbObjectTest extends TestCase
{
    public function testShowAllItems()
    {
        // Створюємо mock для об'єкту Database
        $dbMock = $this->createMock(Database::class);

        // Вказуємо, що метод connect() поверне mock об'єкт PDO
        $pdoMock = $this->createMock(PDO::class);
        $dbMock->expects($this->once())
            ->method('connect')
            ->willReturn($pdoMock);

        // Вказуємо, що метод query() поверне mock об'єкт PDOStatement з певними даними
        $stmtMock = $this->createMock(PDOStatement::class);
        $pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM CheckerDesk')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls(['id' => 1, 'data' => 'data1'], ['id' => 2, 'data' => 'data2'], false);

        // Створюємо об'єкт класу DbObject з mock об'єктом Database
        $dbObject = new DbObject($dbMock);

        // Викликаємо метод showAllItems() та перевіряємо, чи повертає він правильні дані
        $this->assertEquals(['data1', 'data2'], $dbObject->showAllItems('data'));
    }
}
