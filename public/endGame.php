<?php

session_start();
session_unset();
session_destroy();

require_once 'instances.php';

$repository->clearTable();
?>
<!--page the end of the game -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game over</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="p-3 mb-2 bg-secondary text-white">
    <div class="container">
        <div class="position-absolute bottom-50 end-50">
            <div class="col-5">
                <h5>Game over!</h5>
                <h5>
                    <a href="startGame.php" class="badge bg-primary">Try again?</a>
                </h5>
            </div>
        </div>
    </div>
</body>

</html>