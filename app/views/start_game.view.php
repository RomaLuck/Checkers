<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>

<body class="p-3 mb-2text-white">
<div class="container">
    <div class="justify-content-center align-items-center p-5 shadow"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <form action="" method="post">
            <div class="form-group mt-md-2">
                <label for="name"></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Player name" required>
                <div class="mt-2">
                    <button type="button" class="btn btn-light" id="whiteButton">
                        <input class="form-check-input" type="radio" name="white" id="whiteRadio"
                               hidden="hidden">
                        <img src="pictures/white.png" alt="white">
                        <label class="form-check-label" for="whiteRadio">
                            White
                        </label>
                    </button>
                    <button type="button" class="btn btn-light" id="blackButton">
                        <input class="form-check-input" type="radio" name="black" id="blackRadio"
                               hidden="hidden">
                        <img src="pictures/black.png" alt="black">
                        <label class="form-check-label" for="blackRadio">
                            Black
                        </label>
                    </button>
                    <p class="text-danger small text-center mt-3" id="alerts"></p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-md-2 float-end" id="save">Save</button>
        </form>
    </div>
</div>

<?php require base_path('views/_partials/footer.php') ?>
<script src="js/startForm.js"></script>
