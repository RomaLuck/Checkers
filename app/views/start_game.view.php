<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>
<?php require base_path('views/_partials/nav.php') ?>

<div class="container">
    <div class="justify-content-center align-items-center p-5 shadow"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                 alt="Generic placeholder image" class="img-fluid"
                                 style="width: 120px; border-radius: 10px;"/>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 id="username" class="mb-1"><?= $username ?></h5>
                            <p class="mb-2 pb-1">
                                Senior
                            </p>
                            <div class="d-flex justify-content-start rounded-3 p-2 mb-2 bg-body-tertiary">
                                <div>
                                    <p class="small text-muted mb-1">
                                        Games
                                    </p>
                                    <p class="mb-0"><?= $gamesCount ?></p>
                                </div>
                                <div class="px-3">
                                    <p class="small text-muted mb-1">
                                        Wins
                                    </p>
                                    <p class="mb-0">976</p>
                                </div>
                                <div>
                                    <p class="small text-muted mb-1">
                                        Rating
                                    </p>
                                    <p class="mb-0">8.5</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="post" action="/create">
                <div class="mt-2 d-flex">
                    <button type="button" class="btn btn-light shadow" id="whiteButton">
                        <input class="form-check-input" type="radio" name="player" value="white" id="whiteRadio"
                               hidden="hidden">
                        <img class="w-25" src="pictures/white.png" alt="white">
                        <label class="form-check-label" for="whiteRadio">

                        </label>
                    </button>
                    <button type="button" class="btn btn-light shadow" id="blackButton">
                        <input class="form-check-input" type="radio" name="player" value="black" id="blackRadio"
                               hidden="hidden">
                        <img class="w-25" src="pictures/black.png" alt="black">
                        <label class="form-check-label" for="blackRadio">

                        </label>
                    </button>
                    <p class="text-danger small text-center mt-3" id="alerts"></p>
                </div>
                <button type="submit" class="btn btn-danger mt-md-2 float-end" id="save">Create new game</button>
            </form>
        </div>
        <div class="mt-5">
            <?php foreach ($session->getFlashBag()->all() as $type => $usernameMessages) { ?>
                <?php foreach ($usernameMessages as $usernameMessage) { ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible fade show">
                        <?= $usernameMessage ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
            <?php } ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Link</th>
                    <th>White</th>
                    <th>Black</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($gameList as $game) { ?>
                    <?php if ($game->isActive()) { ?>
                        <tr class="game-list">
                            <td><a class="btn btn-secondary" href="<?= $baseUrl . 'game?room=' . $game->getRoomId() ?>">Link</a>
                            </td>
                            <td class="username" room="<?= $game->getRoomId() ?>"
                                id="white"><?= $game->getWhiteTeamUser() ? $game->getWhiteTeamUser()->getUsername() : '' ?></td>
                            <td class="username" room="<?= $game->getRoomId() ?>"
                                id="black"><?= $game->getBlackTeamUser() ? $game->getBlackTeamUser()->getUsername() : '' ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require base_path('views/_partials/footer.php') ?>
<script src="js/startForm.js"></script>
