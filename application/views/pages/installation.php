<!doctype html>
<html lang="<?= config('language_code') ?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation | @webSchedulr</title>
    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/themes/default.min.css') ?>">
    <!-- <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>"> -->
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/pages/installation.min.css') ?>">
    
    
    <!-- Add Google Fonts for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
    
    </style>
</head>
<body>
<div id="loading" class="d-none">
    <img src="<?= base_url('assets/img/loading.gif') ?>" alt="loading">
</div>

<header class="bg-dark py-4">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center">
            <!-- Logo -->
            <img src="<?= asset_url('assets/img/logo_white.png') ?>" alt="@webSchedulr Logo" class="img-fluid me-md-3 mb-3 mb-md-0 logo-image">
            <!-- Page Title -->
            <!-- <h1 class="page-title mb-0"></h1> -->
        </div>
    </div>
</header>  

<main class="content container py-4">
    <section class="welcome mb-5">
        <h2 class="text-center mb-4">Simple scheduling for anything</h2>
        <p class="text-center">
            This page will guide you through the initial setup of @webSchedulr.
            <!-- <a href="<?= site_url('user/login') ?>" class="text-primary"><?= site_url('user/login') ?></a>. -->
        </p>
        
    </section>

    <div class="alert alert-danger" role="alert" hidden></div>

    <form id="installation-form" class="row g-4">
        <div class="admin-settings col-12 col-md-6">
            <h3 class="text-black-50 mb-3 fw-light">Administrator Settings</h3>

            <div class="mb-3">
                <label for="first-name" class="form-label"><?= lang('first_name') ?> <span class="text-danger">*</span></label>
                <input type="text" id="first-name" class="form-control" required maxlength="256">
            </div>

            <div class="mb-3">
                <label for="last-name" class="form-label"><?= lang('last_name') ?> <span class="text-danger">*</span></label>
                <input type="text" id="last-name" class="form-control" required maxlength="512">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><?= lang('email') ?> <span class="text-danger">*</span></label>
                <input type="email" id="email" class="form-control" required maxlength="512">
            </div>

            <div class="mb-3">
                <label for="phone-number" class="form-label"><?= lang('phone_number') ?> <span class="text-danger">*</span></label>
                <input type="tel" id="phone-number" class="form-control" required maxlength="128">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label"><?= lang('username') ?> <span class="text-danger">*</span></label>
                <input type="text" id="username" class="form-control" required maxlength="256">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label"><?= lang('password') ?> <span class="text-danger">*</span></label>
                <input type="password" id="password" class="form-control" required maxlength="512">
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label"><?= lang('retype_password') ?> <span class="text-danger">*</span></label>
                <input type="password" id="password-confirm" class="form-control" required maxlength="512">
            </div>

            <!-- <div class="mb-3">
                <label for="language" class="form-label"><?= lang('language') ?> <span class="text-danger">*</span></label>
                <select id="language" class="form-select" required>
                    <?php foreach (vars('available_languages') as $lang): ?>
                        <option value="<?= $lang ?>" <?= $lang == config('language') ? 'selected' : '' ?>>
                            <?= ucfirst($lang) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div> -->
        </div>

        <div class="company-settings col-12 col-md-6">
            <h3 class="text-black-50 mb-3 fw-light"><?= lang('company') ?> Settings</h3>

            <div class="mb-3">
                <label for="company-name" class="form-label"><?= lang('company_name') ?> <span class="text-danger">*</span></label>
                <input type="text" id="company-name" class="form-control" required>
                <small class="form-text text-muted"><?= lang('company_name_hint') ?></small>
            </div>

            <div class="mb-3">
                <label for="company-email" class="form-label"><?= lang('company_email') ?> <span class="text-danger">*</span></label>
                <input type="email" id="company-email" class="form-control" required>
                <small class="form-text text-muted"><?= lang('company_email_hint') ?></small>
            </div>

            <div class="mb-3">
                <label for="company-link" class="form-label"><?= lang('company_link') ?> <span class="text-danger">*</span></label>
                <input type="url" id="company-link" class="form-control" required>
                <small class="form-text text-muted"><?= lang('company_link_hint') ?></small>
            </div>
        </div>

        <div class="col-12 text-center">
            <button type="submit" id="install" class="btn btn-primary btn-lg">
                <i class="icon-white icon-ok me-2"></i>
                Install @webSchedulr
            </button>
        </div>
    </form>

    <section class="license mt-5">
        
        <p class="text-center">
            By using @webSchedulr, you agree to the terms described in the 
            <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">GPL-3.0 License</a>.
        </p>
    </section>
</main>

<footer class="bg-light py-3 text-center">
    <p>Powered by <a href="https://webschedulr.co.za">@webSchedulr</a></p>
</footer>

<?php component('js_vars_script'); ?>
<?php component('js_lang_script'); ?>

<script src="<?= asset_url('assets/vendor/jquery/jquery.min.js') ?>" defer></script>
<script src="<?= asset_url('assets/vendor/@popperjs-core/popper.min.js') ?>" defer></script>
<script src="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.js') ?>" defer></script>
<script src="<?= asset_url('assets/js/app.js') ?>" defer></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>" defer></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>" defer></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>" defer></script>
<script src="<?= asset_url('assets/js/pages/installation.js') ?>" defer></script>

</body>
</html>