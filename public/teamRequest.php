<?php

require_once 'instances.php';

try {
    echo json_encode([
        'jsonWhiteTeam'=>$repository->findBy(['team' => 'white'])->filterByField('cell'),
        'jsonBlackTeam'=>$repository->findBy(['team' => 'black'])->filterByField('cell'),
    ]);

} catch (Exception $e) {
    $message = $e->getMessage();
}
