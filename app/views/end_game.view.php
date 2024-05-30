<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>

<body class="p-3 mb-2 bg-secondary text-white">
<div class="container">
    <div class="position-absolute bottom-50 end-50">
        <div class="col-5">
            <h5>Game over!</h5>
            <h5>
                <a href="/" class="badge bg-primary">Try again?</a>
            </h5>
        </div>
    </div>
</div>

<?php require base_path('views/_partials/footer.php') ?>
