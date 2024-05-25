<?php

$router->get('/', 'index.php');
$router->post('/', 'start.php');
$router->get('/game', 'game.php');
$router->post('/game', 'update.php');
$router->get('/end', 'end.php');