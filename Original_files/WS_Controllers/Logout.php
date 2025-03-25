<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Logout controller.
 *
 * Handles the logout page functionality.
 *
 * @package Controllers
 */
class Logout extends WS_Controller
{
    /**
     * Render the logout page.
     */
    public function index(): void
    {
        $this->session->sess_destroy();

        $company_name = setting('company_name');

        html_vars([
            'page_title' => lang('log_out'),
            'company_name' => $company_name,
        ]);

        $this->load->view('pages/logout');
    }
}
