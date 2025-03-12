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

/**
 * Login controller.
 *
 * Handles the login page functionality.
 *
 * @package Controllers
 */
class Login extends WS_Controller
{
    /**
     * Login constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');
        $this->load->library('ldap_client');
        $this->load->library('email_messages');

        script_vars([
            'dest_url' => session('dest_url', site_url('calendar')),
        ]);
    }

    /**
     * Render the login page.
     */
    public function index(): void
    {
        if (session('user_id')) {
            redirect('calendar');
            return;
        }

        html_vars([
            'page_title' => lang('login'),
            'base_url' => config('base_url'),
            'dest_url' => session('dest_url', site_url('calendar')),
            'company_name' => setting('company_name'),
        ]);

        $this->load->view('pages/login');
    }

    /**
     * Validate the provided credentials and start a new session if the validation was successful.
     */
    public function validate(): void
    {
        try {
            $username = request('username');

            if (empty($username)) {
                throw new InvalidArgumentException('No username value provided.');
            }

            $password = request('password');

            if (empty($password)) {
                throw new InvalidArgumentException('No password value provided.');
            }

            $user_data = $this->accounts->check_login($username, $password);

            if (empty($user_data)) {
                $user_data = $this->ldap_client->check_login($username, $password);
            }

            if (empty($user_data)) {
                throw new InvalidArgumentException(lang('invalid_credentials_provided'));
            }

            $this->session->sess_regenerate();

            session($user_data); // Save data in the session.

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
