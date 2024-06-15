<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>

<div class="container">
    <div class="justify-content-center align-items-center p-5 shadow"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <!-- Pills navs -->
        <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-login" data-mdb-pill-init href="#pills-login" role="tab"
                   aria-controls="pills-login" aria-selected="true">Login</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-register" data-mdb-pill-init href="/registration" role="tab"
                   aria-controls="pills-register" aria-selected="false">Registration</a>
            </li>
        </ul>
        <!-- Pills navs -->

        <!-- Pills content -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                <form method="post">
                    <div class="text-center mb-3">
                        <p>Sign in with:</p>
                        <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                            <i class="bi bi-facebook"></i>
                        </button>

                        <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                            <i class="bi bi-google"></i>
                        </button>

                        <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                            <i class="bi bi-twitter"></i>
                        </button>

                        <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                            <i class="bi bi-github"></i>
                        </button>
                    </div>

                    <p class="text-center">or:</p>

                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="username" id="loginName" class="form-control"/>
                        <label class="form-label" for="loginName">Email or username</label>
                        <?php foreach ($session->getFlashBag()->get('username') as $usernameMessage) { ?>
                            <div class="small text-danger"><?= $usernameMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="password" id="loginPassword" class="form-control"/>
                        <label class="form-label" for="loginPassword">Password</label>
                        <?php foreach ($session->getFlashBag()->get('password') as $passwordMessage) { ?>
                            <div class="small text-danger"><?= $passwordMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- 2 column grid layout -->
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex justify-content-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-3 mb-md-0">
                                <input class="form-check-input" type="checkbox" value="" id="loginCheck" checked/>
                                <label class="form-check-label" for="loginCheck"> Remember me </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" data-mdb-button-init data-mdb-ripple-init
                            class="btn btn-primary btn-block mb-4">Sign in
                    </button>

                    <?php foreach ($session->getFlashBag()->get('warning') as $warningMessage) { ?>
                        <div class="small text-danger"><?= $warningMessage ?></div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require base_path('views/_partials/footer.php') ?>
