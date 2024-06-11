<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>
<?php require base_path('views/_partials/nav.php') ?>

<div class="container p-5 border shadow">
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="d-flex justify-content-center">
                <div class="table-responsive text-center" id="table-responsive">

                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <h2 class="text-center">Game Log</h2>
            <div class="d-flex justify-content-center">
                <ul id="game-log"></ul>
            </div>
        </div>
    </div>
</div>
<div id="color" hidden="hidden"><?= $color ?></div>
<script src="js/game.js"></script>

<?php require base_path('views/_partials/footer.php') ?>
