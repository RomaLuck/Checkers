<?php

if (isset($_POST['white'], $_POST['black'])) {
    $_SESSION['white'] = $_POST['white'];
    $_SESSION['black'] = $_POST['black'];

    redirect('/game');
}
