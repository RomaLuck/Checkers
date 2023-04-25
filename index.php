<?php

use CheckersOOP\db\Database;
use CheckersOOP\db\DbObject;
use CheckersOOP\src\CheckerDesk;

require_once __DIR__ . '/vendor/autoload.php';

$db = new Database();
$dbObj = new DbObject($db);
$checkerDesk = new CheckerDesk($dbObj);
if (isset($_POST['white']) && isset($_POST['black'])) {
    if ($dbObj->showAllItems('id') !== []) {
        $checkerDesk->clearTable();
    }
    $checkerDesk->fillTheTable();
    header("Location: game.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form checkers</title>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body class="p-3 mb-2 bg-secondary text-white">

<div class="container">
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <form class="mx-auto" method="POST" name="players">
            <div class="form-group mt-md-2">
                <label for="white" class="visually-hidden"></label>
                <input type="text" class="form-control" id="white" name="white" placeholder="White player name">
            </div>
            <div class="form-group mt-md-2">
                <label for="black" class="visually-hidden"></label>
                <input type="text" class="form-control" id="black" name="black" placeholder="Black player name">
            </div>
            <div>
                <button type="submit" class="btn btn-primary mt-md-2 float-end" id="save">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>