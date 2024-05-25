<?php

session_start();

if (isset($_POST['white'], $_POST['black'])) {
    $_SESSION['white'] = $_POST['white'];
    $_SESSION['black'] = $_POST['black'];

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body class="p-3 mb-2 bg-secondary text-white">

<div class="container position-relative">
    <img src="pictures/checkers.jpg" class="mt-5 img-thumbnail" alt="checkers">
    <div class="justify-content-center align-items-center"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>