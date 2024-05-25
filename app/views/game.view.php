<?php require base_path('views/_partials/header.php') ?>
<body>
<div class="container mt-5">
    <form method="POST" class="form d-flex justify-content-center d-none" autocomplete="off">
        <div class="col-md-4">
            <input type="text" class="form-control" name="from" id="form1"
                   placeholder="choose a checker" autocomplete="off">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="to" id="form2" placeholder="select a cell"
                   autocomplete="off">
        </div>
        <div class="col-md-2">
            <input type="submit" class="form-control btn btn-primary" name="submit" id="submit"
                   value="enter">
        </div>
    </form>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex justify-content-center">
                <div class="table-responsive text-center" id="table-responsive">
                    <table class="chess-board">
                        <?php
                        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
                        $i = 0;
                        foreach ($game->getDesk() as $deskRow) {
                            $j = 1;
                            ?>
                            <tr>
                                <?php foreach ($deskRow as $cell) { ?>
                                    <td id="<?= $letters[$i] . $j++ ?>"><?= $cell ?></td>
                                <?php } ?>
                            </tr>
                            <?php
                            $i++;
                        } ?>
                    </table>
                    <div class="row justify-content-center">
                        <a href="/end" class="btn btn-danger mt-2">Finish game</a>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h2 class="text-center">Game Log</h2>
            <div class="d-flex justify-content-center">
                <ul id="game-log">
                    <?php foreach ($logs as $log) { ?>
                        <li><?= $log ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>

<?php require base_path('views/_partials/footer.php') ?>