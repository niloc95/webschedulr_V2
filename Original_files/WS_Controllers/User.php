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
 * User controller.
 *
 * Handles the user related operations.
 *
 * @package Controllers
 */
class User extends WS_Controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');
        $this->load->library('email_messages');
    }

    /**
     * Redirect to the login page.
     */
    public function index(): void
    {
        redirect('login');
    }

    /**
     * Display the login page.
     *
     * @deprecated Since 1.5 Use the Login controller instead.
     */
    public function login(): void
    {
        redirect('login');
    }

    /**
     * Display the logout page.
     *
     * @deprecated Since 1.5 Use the Logout controller instead.
     */
    public function logout(): void
    {
        redirect('logout');
    }

    /**
     * Display the password recovery page.
     *
     * @deprecated Since 1.5 Use the Logout controller instead.
     */
    public function forgot_password(): void
    {
        redirect('recovery');
    }
}
