<?php

declare(strict_types=1);

require base_path('views/_partials/header.php') ?>

<body class="p-3 mb-2text-white">
<div class="container">
    <div class="justify-content-center align-items-center p-5 shadow"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <!-- Pills navs -->
        <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-register" data-mdb-pill-init href="/login" role="tab"
                   aria-controls="pills-register" aria-selected="false">Login</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-login" data-mdb-pill-init href="#pills-login" role="tab"
                   aria-controls="pills-login" aria-selected="true">Registration</a>
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

                    <!-- Username input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="username" id="registerUsername" class="form-control"/>
                        <label class="form-label" for="registerUsername">Username</label>
                        <?php foreach ($session->getFlashBag()->get('username') as $usernameMessage) { ?>
                            <div class="small text-danger"><?= $usernameMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" id="registerEmail" class="form-control"/>
                        <label class="form-label" for="registerEmail">Email</label>
                        <?php foreach ($session->getFlashBag()->get('email') as $emailMessage) { ?>
                            <div class="small text-danger"><?= $emailMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="password" id="registerPassword" class="form-control"/>
                        <label class="form-label" for="registerPassword">Password</label>
                        <?php foreach ($session->getFlashBag()->get('password') as $passwordMessage) { ?>
                            <div class="small text-danger"><?= $passwordMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- Repeat Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="repeat-password" id="registerRepeatPassword" class="form-control"/>
                        <label class="form-label" for="registerRepeatPassword">Repeat password</label>
                        <?php foreach ($session->getFlashBag()->get('repeatPassword') as $repeatPasswordMessage) { ?>
                            <div class="small text-danger"><?= $repeatPasswordMessage ?></div>
                        <?php } ?>
                    </div>

                    <!-- Checkbox -->
                    <div class="form-check d-flex justify-content-center mb-4">
                        <input class="form-check-input me-2" type="checkbox" value="" id="registerCheck" checked
                               aria-describedby="registerCheckHelpText"/>
                        <label class="form-check-label" for="registerCheck">
                            I have read and agree to the terms
                        </label>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" data-mdb-button-init data-mdb-ripple-init
                            class="btn btn-primary btn-block mb-3">Register
                    </button>
                </form>
            </div>
        </div>
        <!-- Pills content -->

        <?php require base_path('views/_partials/footer.php') ?>
