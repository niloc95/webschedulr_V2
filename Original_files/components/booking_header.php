<?php
/**
 * Local variables.
 *
 * @var string $company_name
 */
?>

<div id="header" class="bg-white shadow-sm p-3">
    <div id="company-name" class="d-flex align-items-start gap-3 mb-4">
        <!-- Company Logo -->
        <img src="<?= vars('company_logo') ?: base_url('assets/img/logo_white.png') ?>" alt="logo" id="company-logo" class="img-fluid" style="max-height: 80px;">

        <!-- Company Name and Services -->
        <div class="d-flex flex-column">
            <!-- Company Name -->
            <span class="h3 fw-bold mb-1">
                <?= e($company_name) ?>
            </span>

            <!-- Services -->
            <span class="text-muted">
                <span class="display-booking-selection">
                <?= lang('service') ?> │ <?= lang('provider') ?>
            </span>
        </div>
    </div>

    <div id="steps">
        <div id="step-1" class="book-step active-step"
             data-tippy-content="<?= lang('service_and_provider') ?>">
            <strong>1</strong>
        </div>

        <div id="step-2" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_date_and_time') ?>">
            <strong>2</strong>
        </div>
        <div id="step-3" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('customer_information') ?>">
            <strong>3</strong>
        </div>
        <div id="step-4" class="book-step" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_confirmation') ?>">
            <strong>4</strong>
        </div>
    </div>
</div>
