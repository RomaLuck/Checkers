<?php

use CheckersOOP\src\db\DbObject;

require_once 'vendor/autoload.php';

try {
    $dbObj = new DbObject();

    echo json_encode([
        'jsonWhiteTeam'=>$dbObj->showItems('id', ['team' => 'white']),
        'jsonBlackTeam'=>$dbObj->showItems('id', ['team' => 'black'])
    ]);

} catch (Exception $e) {
    $message = $e->getMessage();
}
