<?php require base_path('views/_partials/header.php') ?>
<?php require base_path('views/_partials/nav.php') ?>
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

<div class="container p-5 border shadow">
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="d-flex justify-content-center">
                <div class="table-responsive text-center" id="table-responsive">
                    <table class="chess-board" id="chess-board">
                        <?php
                        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
                        $numbers = [1, 2, 3, 4, 5, 6, 7, 8];
                        $i = 0; ?>
                        <tr>
                            <?php foreach ($numbers as $number) { ?>
                                <th><?= $number ?></th>
                            <?php } ?>
                        </tr>

                        <?php foreach ($game->getDesk()->getDeskData() as $deskRow) {
                            $j = 1;
                            ?>
                            <tr>
                                <?php foreach ($deskRow as $cell) { ?>
                                    <td id="<?= $letters[$i] . $j++ ?>"><?= $cell ?></td>
                                <?php } ?>
                                <th>
                                    <?= $letters[$i] ?>
                                </th>
                            </tr>
                            <?php
                            $i++;
                        } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <h2 class="text-center">Game Log</h2>
            <div class="d-flex justify-content-center">
                <ul id="game-log">
                    <?php foreach ($logs as $log) { ?>
                        <li class="text-<?= strtolower($log['logLevel'])?>"><?= $log['message'] ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div id="queue" class="d-none"><?= $queue ?></div>
<script src="js/script.js"></script>

<?php require base_path('views/_partials/footer.php') ?>