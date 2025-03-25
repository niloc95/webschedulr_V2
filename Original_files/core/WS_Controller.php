<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N. Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * @webSchedulr controller.
 *
 * @property WS_Benchmark $benchmark
 * @property WS_Cache $cache
 * @property WS_Calendar $calendar
 * @property WS_Config $config
 * @property WS_DB_forge $dbforge
 * @property WS_DB_query_builder $db
 * @property WS_DB_utility $dbutil
 * @property WS_Email $email
 * @property WS_Encrypt $encrypt
 * @property WS_Encryption $encryption
 * @property WS_Exceptions $exceptions
 * @property WS_Hooks $hooks
 * @property WS_Input $input
 * @property WS_Lang $lang
 * @property WS_Loader $load
 * @property WS_Log $log
 * @property WS_Migration $migration
 * @property WS_Output $output
 * @property WS_Profiler $profiler
 * @property WS_Router $router
 * @property WS_Security $security
 * @property WS_Session $session
 * @property WS_Upload $upload
 * @property WS_URI $uri
 *
 * @property Admins_model $admins_model
 * @property Appointments_model $appointments_model
 * @property Service_categories_model $service_categories_model
 * @property Consents_model $consents_model
 * @property Customers_model $customers_model
 * @property Providers_model $providers_model
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Services_model $services_model
 * @property Settings_model $settings_model
 * @property Unavailabilities_model $unavailabilities_model
 * @property Users_model $users_model
 * @property Webhooks_model $webhooks_model
 * @property Blocked_periods_model $blocked_periods_model
 *
 * @property Accounts $accounts
 * @property Api $api
 * @property Availability $availability
 * @property Email_messages $email_messages
 * @property Captcha_builder $captcha_builder
 * @property Google_Sync $google_sync
 * @property Caldav_Sync $caldav_sync
 * @property Ics_file $ics_file
 * @property Instance $instance
 * @property Ldap_client $ldap_client
 * @property Notifications $notifications
 * @property Permissions $permissions
 * @property Synchronization $synchronization
 * @property Timezones $timezones
 * @property Webhooks_client $webhooks_client
 */
class WS_Controller extends CI_Controller
{
    /**
     * WS_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');

        $this->ensure_user_exists();

        $this->configure_language();

        $this->load_common_html_vars();

        $this->load_common_script_vars();

        rate_limit($this->input->ip_address());
    }

    private function ensure_user_exists()
    {
        $user_id = session('user_id');

        if (!$user_id || !$this->db->table_exists('users')) {
            return;
        }

        if (!$this->accounts->does_account_exist($user_id)) {
            session_destroy();

            abort(403, 'Forbidden');
        }
    }

    /**
     * Configure the language.
     */
    private function configure_language()
    {
        $session_language = session('language');

        if ($session_language) {
            $language_codes = config('language_codes');

            config([
                'language' => $session_language,
                'language_code' => array_search($session_language, $language_codes) ?: 'en',
            ]);
        }

        $this->lang->load('translations');
    }

    /**
     * Load common script vars for all requests.
     */
    private function load_common_html_vars()
    {
        html_vars([
            'base_url' => config('base_url'),
            'index_page' => config('index_page'),
            'available_languages' => config('available_languages'),
            'language' => $this->lang->language,
            'csrf_token' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Load common script vars for all requests.
     */
    private function load_common_script_vars()
    {
        script_vars([
            'base_url' => config('base_url'),
            'index_page' => config('index_page'),
            'available_languages' => config('available_languages'),
            'csrf_token' => $this->security->get_csrf_hash(),
            'language' => config('language'),
            'language_code' => config('language_code'),
        ]);
    }
}
