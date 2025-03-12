<?php
/**
 * Local variables.
 *
 * @var string $user_display_name
 */
?>

<link rel="stylesheet" href="<?= base_url('assets/css/layouts/footer.css') ?>">

<div id="footer" class="d-lg-flex justify-content-lg-between align-items-lg-center p-3 text-center text-lg-start mt-auto bg-light border-top">
    <!-- Left Section: Logo, Version, and Links -->
    <div class="d-flex flex-column flex-lg-row align-items-center gap-3 mb-3 mb-lg-0">
        <!-- Logo and Brand -->
        <div class="d-flex align-items-center me-lg-4">
            <img class="me-2" src="<?= base_url('assets/img/logo_black.png') ?>" alt="@webSchedulr Logo" style="width: 125px;">
            
        </div>

        <!-- Favicon and Copyright -->
        <div class="d-flex align-items-center me-lg-4">
            <img class="me-2" src="<?= base_url('assets/img/favicon.ico') ?>" alt="webScheduler" style="width: 24px; height: 24px;">
            <div>
                <a href="https://webschedulr.com" class="text-decoration-none">webSchedulr</a>
                
            </div>
        </div>

        <!-- License Information -->
        
        <!-- Language Selector -->
        <!-- <div class="me-lg-4">
            <span id="select-language" class="badge bg-dark">
                <i class="fas fa-language me-2"></i>
                <?= ucfirst(config('language')) ?>
            </span>
        </div> -->

        
        <!-- Booking Page Link -->
        <div>
            <a href="<?= site_url('appointments') ?>" class="text-decoration-none">
                <?= lang('go_to_booking_page') ?>
            </a>
        </div>
    </div>

    <!-- Right Section: User Display Name -->
    <div class="ms-lg-auto text-lg-end">
        <strong id="footer-user-display-name" class="text-dark">
            <?= lang('hello') . ', ' . e($user_display_name) ?>!
        </strong>
    </div>
</div>
