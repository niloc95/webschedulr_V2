<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr - Online Appointments
 * @author      N N.Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/*
 * This file can only be used in a testing environment and only from the terminal.
 */

if (ENVIRONMENT !== 'testing' || !is_cli()) {
    show_404();
}

/**
 * Test controller.
 *
 * This controller does not have or need any logic, it is just used so that CI can be loaded properly during the test
 * execution.
 */
class Test extends WS_Controller
{
    /**
     * Placeholder callback.
     *
     * @return void
     */
    public function index(): void
    {
        //
    }
}
