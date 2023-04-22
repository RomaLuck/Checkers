<?php

use CheckersOOP\db\Database;
use CheckersOOP\db\DbObject;
use CheckersOOP\src\BlackTeam;
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\CheckerObject;
use CheckersOOP\src\WhiteTeam;

require_once __DIR__ . '/vendor/autoload.php';

$db = new Database();
try {
    $dbObj = new DbObject($db);
    $checkerDesk = new CheckerDesk($dbObj);
    $white = new WhiteTeam(htmlspecialchars($_POST["white"]));
    $black = new BlackTeam(htmlspecialchars($_POST["black"]));
    $whiteObject = new CheckerObject($dbObj, $white, $checkerDesk);
    $blackObject = new CheckerObject($dbObj, $black, $checkerDesk);
    $whiteObject->createFigure('checker');
    $blackObject->createFigure('checker');

    if (in_array($_POST['choose_figure'], $dbObj->showItems('id', ['team' => 'white']))) {
        $whiteObject->move($_POST['choose_figure'], $_POST['set_step']);
    } elseif (in_array($_POST['choose_figure'], $dbObj->showItems('id', ['team' => 'black']))) {
        $blackObject->move($_POST['choose_figure'], $_POST['set_step']);
    }
} catch (Exception $e) {
    $message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkers</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-3 mb-2 bg-secondary text-white">
<div class="container-md">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <form method="POST">
                <div class="row gx-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="choose_figure" id="form1"
                               placeholder="choose a checker">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="set_step" id="form2" placeholder="select a cell">
                    </div>
                    <div class="col-md-2">
                        <input type="submit" class="btn btn-primary" name="submit" id="submit" value="enter">
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-md-3">
            <h5 class="text-center">
                <?php
                $jsonWhiteTeam = json_encode($dbObj->showItems('id', ['team' => 'white']));
                $jsonBlackTeam = json_encode($dbObj->showItems('id', ['team' => 'black']));

                if ($dbObj->showItems('team', ['team' => 'black']) === []) {
                    $message = "<br>WHITE TEAM WON!<br>";
                    header("refresh:5;url=end_game.php");
                }
                if ($dbObj->showItems('team', ['team' => 'white']) === []) {
                    $message = "<br>BLACK TEAM WON!<br>";
                    header("refresh:5;url=end_game.php");
                }
                if ($message !== null) {
                    echo $message;
                }
                ?>
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="table-responsive">
                <table class="chess-board">
                    <tr>
                        <th></th>
                        <th>a</th>
                        <th>b</th>
                        <th>c</th>
                        <th>d</th>
                        <th>e</th>
                        <th>f</th>
                        <th>g</th>
                        <th>h</th>
                    </tr>
                    <tr>
                        <th>8</th>
                        <td class="white" id="a8"></td>
                        <td class="black" id="b8"></td>
                        <td class="white" id="c8"></td>
                        <td class="black" id="d8"></td>
                        <td class="white" id="e8"></td>
                        <td class="black" id="f8"></td>
                        <td class="white" id="g8"></td>
                        <td class="black" id="h8"></td>
                    </tr>
                    <tr>
                        <th>7</th>
                        <td class="black" id="a7"></td>
                        <td class="white" id="b7"></td>
                        <td class="black" id="c7"></td>
                        <td class="white" id="d7"></td>
                        <td class="black" id="e7"></td>
                        <td class="white" id="f7"></td>
                        <td class="black" id="g7"></td>
                        <td class="white" id="h7"></td>
                    </tr>
                    <tr>
                        <th>6</th>
                        <td class="white" id="a6"></td>
                        <td class="black" id="b6"></td>
                        <td class="white" id="c6"></td>
                        <td class="black" id="d6"></td>
                        <td class="white" id="e6"></td>
                        <td class="black" id="f6"></td>
                        <td class="white" id="g6"></td>
                        <td class="black" id="h6"></td>
                    </tr>
                    <tr>
                        <th>5</th>
                        <td class="black" id="a5"></td>
                        <td class="white" id="b5"></td>
                        <td class="black" id="c5"></td>
                        <td class="white" id="d5"></td>
                        <td class="black" id="e5"></td>
                        <td class="white" id="f5"></td>
                        <td class="black" id="g5"></td>
                        <td class="white" id="h5"></td>
                    </tr>
                    <tr>
                        <th>4</th>
                        <td class="white" id="a4"></td>
                        <td class="black" id="b4"></td>
                        <td class="white" id="c4"></td>
                        <td class="black" id="d4"></td>
                        <td class="white" id="e4"></td>
                        <td class="black" id="f4"></td>
                        <td class="white" id="g4"></td>
                        <td class="black" id="h4"></td>
                    </tr>
                    <tr>
                        <th>3</th>
                        <td class="black" id="a3"></td>
                        <td class="white" id="b3"></td>
                        <td class="black" id="c3"></td>
                        <td class="white" id="d3"></td>
                        <td class="black" id="e3"></td>
                        <td class="white" id="f3"></td>
                        <td class="black" id="g3"></td>
                        <td class="white" id="h3"></td>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td class="white" id="a2"></td>
                        <td class="black" id="b2"></td>
                        <td class="white" id="c2"></td>
                        <td class="black" id="d2"></td>
                        <td class="white" id="e2"></td>
                        <td class="black" id="f2"></td>
                        <td class="white" id="g2"></td>
                        <td class="black" id="h2"></td>
                    </tr>
                    <tr>
                        <th>1</th>
                        <td class="black" id="a1"></td>
                        <td class="white" id="b1"></td>
                        <td class="black" id="c1"></td>
                        <td class="white" id="d1"></td>
                        <td class="black" id="e1"></td>
                        <td class="white" id="f1"></td>
                        <td class="black" id="g1"></td>
                        <td class="white" id="h1"></td>
                    </tr>
                </table>
                <a href="end_game.php" class="btn btn-danger mt-2">Finish game</a>
            </div>
        </div>
    </div>
</div>

<script>
    /**receiving a json array and displaying the movement of checkers on the field */
    const table = document.getElementsByTagName("td");
    const form1 = document.getElementById("form1");
    const form2 = document.getElementById("form2");
    var white = JSON.parse('<?php echo $jsonWhiteTeam ?>');
    var black = JSON.parse('<?php echo $jsonBlackTeam ?>');

    document.addEventListener("click", function (event) {
        const target = event.target.classList.contains("white") ||
            event.target.classList.contains("black") ||
            event.target.classList.contains("white-piece") ||
            event.target.classList.contains("black-piece");

        if (target) {
            if (form1.value === "") {
                form1.value = event.target.id || event.target.parentNode.id;
            } else if (event.target.id !== form1.value || event.target.parentNode.id !== form1.value) {
                form2.value = event.target.id || event.target.parentNode.id;
            }
        }
    });

    for (var i = 0; i < table.length; i++) {
        if (white.includes(table[i].id)) {
            const piece = document.createElement('div');
            piece.className = "white-piece";
            table[i].appendChild(piece);
        }
    }

    for (var i = 0; i < table.length; i++) {
        if (black.includes(table[i].id)) {
            const piece = document.createElement('div');
            piece.className = "black-piece";
            table[i].appendChild(piece);
        }
    }
</script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>