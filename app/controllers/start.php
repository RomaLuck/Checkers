<?php

declare(strict_types=1);

if (isset($_POST['white'], $_POST['black'])) {
    $_SESSION['white'] = htmlspecialchars($_POST['white']);
    $_SESSION['black'] = htmlspecialchars($_POST['black']);

    redirect('/game');
}
