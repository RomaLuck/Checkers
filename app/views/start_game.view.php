<?php require base_path('views/_partials/header.php') ?>

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

<?php require base_path('views/_partials/footer.php') ?>