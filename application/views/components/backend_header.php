<?php
/**
 * Local variables.
 *
 * @var string $active_menu
 * @var string $company_logo
 */
?>
<link rel="stylesheet" href="<?= base_url('assets/css/layouts/header.css') ?>">

<nav id="header" class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <!-- Logo and Brand -->
        <div id="header-logo" class="navbar-brand d-flex align-items-center">
            <!-- Logo Image -->
            <img src="<?= base_url('assets/img/logo_white.png') ?>" alt="logo" >
            
            <!-- Brand Name and Tagline -->
            <div class="d-flex flex-column">
                <h6 class="mb-0 text-white"></h6>
                
            </div>
        </div>

        <!-- Toggle Button for Mobile -->
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#header-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div id="header-menu" class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <!-- Calendar Link -->
                <?php $hidden = can('view', PRIV_APPOINTMENTS) ? '' : 'd-none'; ?>
                <?php $active = $active_menu == PRIV_APPOINTMENTS ? 'active' : ''; ?>
                <li class="nav-item <?= $active . $hidden ?>">
                    <a href="<?= site_url('calendar' . (vars('calendar_view') === CALENDAR_VIEW_TABLE ? '?view=table' : '')) ?>"
                       class="nav-link"
                       data-tippy-content="<?= lang('manage_appointment_record_hint') ?>">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?= lang('calendar') ?>
                    </a>
                </li>

                <!-- Customers Link -->
                <?php $hidden = can('view', PRIV_CUSTOMERS) ? '' : 'd-none'; ?>
                <?php $active = $active_menu == PRIV_CUSTOMERS ? 'active' : ''; ?>
                <li class="nav-item <?= $active . $hidden ?>">
                    <a href="<?= site_url('customers') ?>" class="nav-link"
                       data-tippy-content="<?= lang('manage_customers_hint') ?>">
                        <i class="fas fa-user-friends me-2"></i>
                        <?= lang('customers') ?>
                    </a>
                </li>

                <!-- Services Dropdown -->
                <?php $hidden = can('view', PRIV_SERVICES) ? '' : 'd-none'; ?>
                <?php $active = $active_menu == PRIV_SERVICES ? 'active' : ''; ?>
                <li class="nav-item dropdown <?= $active . $hidden ?>">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                       data-tippy-content="<?= lang('manage_services_hint') ?>">
                        <i class="fas fa-business-time me-2"></i>
                        <?= lang('services') ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= site_url('services') ?>">
                            <?= lang('services') ?>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('service_categories') ?>">
                            <?= lang('categories') ?>
                        </a>
                    </div>
                </li>

                <!-- Users Dropdown -->
                <?php $hidden = can('view', PRIV_USERS) ? '' : 'd-none'; ?>
                <?php $active = $active_menu == PRIV_USERS ? 'active' : ''; ?>
                <li class="nav-item dropdown <?= $active . $hidden ?>">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                       data-tippy-content="<?= lang('manage_users_hint') ?>">
                        <i class="fas fa-users me-2"></i>
                        <?= lang('users') ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= site_url('providers') ?>">
                            <?= lang('providers') ?>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('secretaries') ?>">
                            <?= lang('secretaries') ?>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('admins') ?>">
                            <?= lang('admins') ?>
                        </a>
                    </div>
                </li>

                <!-- User Settings Dropdown -->
                <?php slot('before_user_nav_item'); ?>

                <?php $hidden = can('view', PRIV_SYSTEM_SETTINGS) || can('view', PRIV_USER_SETTINGS) ? '' : 'd-none'; ?>
                <?php $active = $active_menu == PRIV_SYSTEM_SETTINGS ? 'active' : ''; ?>
                <li class="nav-item dropdown <?= $active . $hidden ?>">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                       data-tippy-content="<?= lang('settings_hint') ?>">
                        <i class="fas fa-user me-2"></i>
                        <?= e(vars('user_display_name')) ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <?php if (can('view', PRIV_SYSTEM_SETTINGS)): ?>
                            <a class="dropdown-item" href="<?= site_url('general_settings') ?>">
                                <?= lang('settings') ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php slot('after_settings_dropdown_item'); ?>
                        
                        <a class="dropdown-item" href="<?= site_url('account') ?>">
                            <?= lang('account') ?>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('about') ?>">
                            <?= lang('about') ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= site_url('logout') ?>">
                            <?= lang('log_out') ?>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Notification and Loading Elements -->
<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <div class="any-element animation is-loading">
        &nbsp;
    </div>
</div>