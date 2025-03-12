<?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 */
?>

<div id="frame-footer" class="py-3">
    <small class="d-flex justify-content-left align-items-left container">
        <span class="footer-powered-by text-muted d-flex align-items-left">
            Powered By
            <a href="https://webschedulr.co.za" target="_blank" class="text-decoration-none ms-2">
                <img src="<?= base_url('assets/img/logo_black.png') ?>" alt="webScheduler Logo" style="height: 40px;">
            </a>
        </span>
    </small>
</div>


<!-- <?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 */
?>

<div id="frame-footer" class="bg-light py-3">
    <small class="d-flex justify-content-between align-items-center container">
        <span class="footer-powered-by text-muted d-flex align-items-center">
            Powered By
            <a href="https://webschedulr.co.za" target="_blank" class="text-decoration-none ms-2">
                <img src="<?= base_url('assets/img/logo_black.png') ?>" alt="webScheduler Logo" style="height: 40px;">
            </a>
        </span>

        <span class="footer-options d-flex gap-2">
            <span id="select-language" class="badge bg-secondary d-flex align-items-center">
                <i class="fas fa-language me-2"></i>
                <?= ucfirst(config('language')) ?>
            </span>
    
            <?php if ($display_login_button): ?>
                <a class="backend-link badge bg-primary text-decoration-none px-3 py-1 d-flex align-items-center"
                   href="<?= session('user_id') ? site_url('calendar') : site_url('login') ?>">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    <?= session('user_id') ? lang('backend_section') : lang('login') ?>
                </a>
            <?php endif; ?>
        </span>
    </small>
</div> -->
