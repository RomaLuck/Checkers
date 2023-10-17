<?php

session_start();

use App\GameCore\BlackTeam;
use App\GameCore\WhiteTeam;

require_once 'instances.php';

//if (isset($_SESSION["white"])) {
//    $white = new WhiteTeam($_SESSION["white"]);
//    var_dump($white);
//}
//if (isset($_SESSION["black"])) {
//    $black = new BlackTeam($_SESSION["black"]);
//}
$white = new WhiteTeam('R');
$black = new BlackTeam('O');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
    $data = json_decode($_POST['formData'], true);
    $chooseFigure = htmlspecialchars($data['form1']);
    $setStep = htmlspecialchars($data['form2']);

    if (in_array($chooseFigure, $repository->findBy(['team' => 'white'])->filterByField('cell'))) {
        $white->setFigureType($checker)->move($chooseFigure, $setStep);
    } elseif (in_array($chooseFigure, $repository->findBy(['team' => 'black'])->filterByField('cell'))) {
        $black->setFigureType($checker)->move($chooseFigure, $setStep);
    }
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
    <link rel="stylesheet" href="styles/style.css">
</head>

<body class="p-3 mb-2 bg-secondary text-white">
<div class="container-md">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <form method="POST" class="form form-control" autocomplete="off">
                <div class="row gx-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="choose_figure" id="form1"
                               placeholder="choose a checker" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="set_step" id="form2" placeholder="select a cell"
                               autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <input type="submit" class="form-control btn btn-primary" name="submit" id="submit"
                               value="enter">
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-md-3">
            <h5 class="text-center">
                Hello!
            </h5>
        </div>
    </div>
    <div class="row" id="table-responsive">
        <div class="d-flex justify-content-center">
            <div class="table-responsive text-center">
                <table class="chess-board" id="first-table" style="display: none;">
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
                        <th></th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <td class="black" id="h1"></td>
                        <td class="white" id="g1"></td>
                        <td class="black" id="f1"></td>
                        <td class="white" id="e1"></td>
                        <td class="black" id="d1"></td>
                        <td class="white" id="c1"></td>
                        <td class="black" id="b1"></td>
                        <td class="white" id="a1"></td>
                        <th>1</th>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td class="white" id="h2"></td>
                        <td class="black" id="g2"></td>
                        <td class="white" id="f2"></td>
                        <td class="black" id="e2"></td>
                        <td class="white" id="d2"></td>
                        <td class="black" id="c2"></td>
                        <td class="white" id="b2"></td>
                        <td class="black" id="a2"></td>
                        <th>2</th>
                    </tr>
                    <tr>
                        <th>3</th>
                        <td class="black" id="h3"></td>
                        <td class="white" id="g3"></td>
                        <td class="black" id="f3"></td>
                        <td class="white" id="e3"></td>
                        <td class="black" id="d3"></td>
                        <td class="white" id="c3"></td>
                        <td class="black" id="b3"></td>
                        <td class="white" id="a3"></td>
                        <th>3</th>
                    </tr>
                    <tr>
                        <th>4</th>
                        <td class="white" id="h4"></td>
                        <td class="black" id="g4"></td>
                        <td class="white" id="f4"></td>
                        <td class="black" id="e4"></td>
                        <td class="white" id="d4"></td>
                        <td class="black" id="c4"></td>
                        <td class="white" id="b4"></td>
                        <td class="black" id="a4"></td>
                        <th>4</th>
                    </tr>
                    <tr>
                        <th>5</th>
                        <td class="black" id="h5"></td>
                        <td class="white" id="g5"></td>
                        <td class="black" id="f5"></td>
                        <td class="white" id="e5"></td>
                        <td class="black" id="d5"></td>
                        <td class="white" id="c5"></td>
                        <td class="black" id="b5"></td>
                        <td class="white" id="a5"></td>
                        <th>5</th>
                    </tr>
                    <tr>
                        <th>6</th>
                        <td class="white" id="h6"></td>
                        <td class="black" id="g6"></td>
                        <td class="white" id="f6"></td>
                        <td class="black" id="e6"></td>
                        <td class="white" id="d6"></td>
                        <td class="black" id="c6"></td>
                        <td class="white" id="b6"></td>
                        <td class="black" id="a6"></td>
                        <th>6</th>
                    </tr>
                    <tr>
                        <th>7</th>
                        <td class="black" id="h7"></td>
                        <td class="white" id="g7"></td>
                        <td class="black" id="f7"></td>
                        <td class="white" id="e7"></td>
                        <td class="black" id="d7"></td>
                        <td class="white" id="c7"></td>
                        <td class="black" id="b7"></td>
                        <td class="white" id="a7"></td>
                        <th>7</th>
                    </tr>
                    <tr>
                        <th>8</th>
                        <td class="white" id="h8"></td>
                        <td class="black" id="g8"></td>
                        <td class="white" id="f8"></td>
                        <td class="black" id="e8"></td>
                        <td class="white" id="d8"></td>
                        <td class="black" id="c8"></td>
                        <td class="white" id="b8"></td>
                        <td class="black" id="a8"></td>
                        <th>8</th>
                    </tr>
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
                        <th></th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <div class="table-responsive text-center">
                <table class="chess-board" id="second-table">
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
                        <th></th>
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
                        <th>8</th>
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
                        <th>7</th>
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
                        <th>6</th>
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
                        <th>5</th>
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
                        <th>4</th>
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
                        <th>3</th>
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
                        <th>2</th>
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
                        <th>1</th>
                    </tr>
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
                        <th></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <button class="btn btn-primary mt-2 float-start" id="reverse-button">Rotate board</button>
            <a href="endGame.php" class="btn btn-danger mt-2 float-end">Finish game</a>
        </div>
    </div>

    <script src="js/script.js"></script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
            crossorigin="anonymous"></script>
</body>

</html>