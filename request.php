<?php

use CheckersOOP\db\DbObject;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $dbObj = new DbObject();

    echo json_encode([
        'jsonWhiteTeam'=>$dbObj->showItems('id', ['team' => 'white']),
        'jsonBlackTeam'=>$dbObj->showItems('id', ['team' => 'black'])
    ]);

} catch (Exception $e) {
    $message = $e->getMessage();
}
