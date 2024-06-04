<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['name'] = htmlspecialchars($_POST['name']);
    $_SESSION['color'] = array_search('on', $_POST, true);

    redirect('/');
}
