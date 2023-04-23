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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
        $data = json_decode($_POST['formData'], true);
        $chooseFigure = $data['form1'];
        $setStep = $data['form2'];

        // Обробка даних форми
        if (in_array($chooseFigure, $dbObj->showItems('id', ['team' => 'white']))) {
            $whiteObject->move($chooseFigure, $setStep);
        } elseif (in_array($chooseFigure, $dbObj->showItems('id', ['team' => 'black']))) {
            $blackObject->move($chooseFigure, $setStep);
        }
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
            <form method="POST" class="form form-control" autocomplete="off">
                <div class="row gx-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="choose_figure" id="form1" placeholder="choose a checker" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="set_step" id="form2" placeholder="select a cell" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <input type="submit" class="form-control btn btn-primary" name="submit" id="submit" value="enter">
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
                if ($message !== '') {
                    echo $message;
                }
                ?>
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="table-responsive text-center" id="table-responsive">
                <table class="chess-board" id="original-table">
                    <tr>
                        <td></td>
                        <td>a</td>
                        <td>b</td>
                        <td>c</td>
                        <td>d</td>
                        <td>e</td>
                        <td>f</td>
                        <td>g</td>
                        <td>h</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td class="white" id="a8"></td>
                        <td class="black" id="b8"></td>
                        <td class="white" id="c8"></td>
                        <td class="black" id="d8"></td>
                        <td class="white" id="e8"></td>
                        <td class="black" id="f8"></td>
                        <td class="white" id="g8"></td>
                        <td class="black" id="h8"></td>
                        <td>8</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td class="black" id="a7"></td>
                        <td class="white" id="b7"></td>
                        <td class="black" id="c7"></td>
                        <td class="white" id="d7"></td>
                        <td class="black" id="e7"></td>
                        <td class="white" id="f7"></td>
                        <td class="black" id="g7"></td>
                        <td class="white" id="h7"></td>
                        <td>7</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td class="white" id="a6"></td>
                        <td class="black" id="b6"></td>
                        <td class="white" id="c6"></td>
                        <td class="black" id="d6"></td>
                        <td class="white" id="e6"></td>
                        <td class="black" id="f6"></td>
                        <td class="white" id="g6"></td>
                        <td class="black" id="h6"></td>
                        <td>6</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td class="black" id="a5"></td>
                        <td class="white" id="b5"></td>
                        <td class="black" id="c5"></td>
                        <td class="white" id="d5"></td>
                        <td class="black" id="e5"></td>
                        <td class="white" id="f5"></td>
                        <td class="black" id="g5"></td>
                        <td class="white" id="h5"></td>
                        <td>5</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td class="white" id="a4"></td>
                        <td class="black" id="b4"></td>
                        <td class="white" id="c4"></td>
                        <td class="black" id="d4"></td>
                        <td class="white" id="e4"></td>
                        <td class="black" id="f4"></td>
                        <td class="white" id="g4"></td>
                        <td class="black" id="h4"></td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="black" id="a3"></td>
                        <td class="white" id="b3"></td>
                        <td class="black" id="c3"></td>
                        <td class="white" id="d3"></td>
                        <td class="black" id="e3"></td>
                        <td class="white" id="f3"></td>
                        <td class="black" id="g3"></td>
                        <td class="white" id="h3"></td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td class="white" id="a2"></td>
                        <td class="black" id="b2"></td>
                        <td class="white" id="c2"></td>
                        <td class="black" id="d2"></td>
                        <td class="white" id="e2"></td>
                        <td class="black" id="f2"></td>
                        <td class="white" id="g2"></td>
                        <td class="black" id="h2"></td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td class="black" id="a1"></td>
                        <td class="white" id="b1"></td>
                        <td class="black" id="c1"></td>
                        <td class="white" id="d1"></td>
                        <td class="black" id="e1"></td>
                        <td class="white" id="f1"></td>
                        <td class="black" id="g1"></td>
                        <td class="white" id="h1"></td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>a</td>
                        <td>b</td>
                        <td>c</td>
                        <td>d</td>
                        <td>e</td>
                        <td>f</td>
                        <td>g</td>
                        <td>h</td>
                        <td></td>
                    </tr>
                </table>
                <button class="btn btn-primary mt-2 float-start" id="reverse-button">Rotate board</button>
                <a href="end_game.php" class="btn btn-danger mt-2 float-end">Finish game</a>
            </div>
        </div>
    </div>
</div>

<script>
    /**receiving a json array and displaying the movement of checkers on the field */
    const tableContainer = document.getElementById('table-responsive');
    const tablePiece = document.querySelectorAll("table td:not(:first-child)");
    const form1 = document.getElementById("form1");
    const form2 = document.getElementById("form2");
    var white = JSON.parse('<?php echo $jsonWhiteTeam ?>');
    var black = JSON.parse('<?php echo $jsonBlackTeam ?>');

    tableContainer.addEventListener("click", function (event) {
        const target = event.target.classList.contains("white") ||
            event.target.classList.contains("black") ||
            event.target.classList.contains("white-piece") ||
            event.target.classList.contains("black-piece");

        if (target) {
            if (form1.value === "") {
                form1.value = event.target.id || event.target.parentNode.id;
            } else if (event.target.id !== form1.value || event.target.parentNode.id !== form1.value) {
                form2.value = event.target.id || event.target.parentNode.id;

                const formData = new FormData();
                formData.append('formData', JSON.stringify({
                    form1: form1.value,
                    form2: form2.value
                }));

                fetch('game.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        console.log(data);
                        form1.value = "";
                        form2.value = "";
                        location.reload();
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });
            }
        }
    });

    function sendData() {
        const xhr = new XMLHttpRequest();
        const url = "game.php";
        const formData = new FormData();

        formData.append("form1", form1.value);
        formData.append("form2", form2.value);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
            }
        };

        xhr.open("POST", url);
        xhr.send(formData);
    }

    for (var i = 0; i < tablePiece.length; i++) {
        if (white.includes(tablePiece[i].id)) {
            const piece = document.createElement('div');
            piece.className = "white-piece";
            tablePiece[i].appendChild(piece);
        }
    }

    for (var i = 0; i < tablePiece.length; i++) {
        if (black.includes(tablePiece[i].id)) {
            const piece = document.createElement('div');
            piece.className = "black-piece";
            tablePiece[i].appendChild(piece);
        }
    }

    const originalTable = document.getElementById('original-table');
    const reverseButton = document.getElementById('reverse-button');
    let isReversed = false;
    let reversedTable;

    reverseButton.addEventListener('click', () => {
        if (!isReversed) {
            reversedTable = document.createElement('table');

            // Перевертаємо рядки таблиці і додаємо їх до нової таблиці
            for (let i = originalTable.rows.length - 1; i >= 0; i--) {
                const row = originalTable.rows[i];
                const newRow = reversedTable.insertRow();
                for (let j = 0; j < row.cells.length; j++) {
                    const cell = row.cells[j];
                    const newCell = newRow.insertCell();
                    newCell.innerHTML = cell.innerHTML;
                    newCell.className = cell.className; // додати класи td
                    newCell.id = cell.id; // зберегти id td
                }
            }

            // Замінюємо оригінальну таблицю на перевернуту, якщо вона має батьківський елемент
            if (originalTable.parentNode) {
                originalTable.parentNode.replaceChild(reversedTable, originalTable);
            }

            isReversed = true;
            reverseButton.innerHTML = 'Rotate the table';
        } else {
            // Повертаємо таблицю на початковий стан, якщо вона має батьківський елемент
            if (reversedTable.parentNode) {
                reversedTable.parentNode.replaceChild(originalTable, reversedTable);
            }

            isReversed = false;
            reverseButton.innerHTML = 'Перевернути таблицю';
        }
    });
</script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>