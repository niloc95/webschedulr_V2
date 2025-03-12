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
 * @webSchedulr security.
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
 */
class WS_Security extends CI_Security
{
    /**
     * CSRF Verify
     *
     * @return    CI_Security
     */
    public function csrf_verify()
    {
        // If it's not a POST request we will set the CSRF cookie
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            return $this->csrf_set_cookie();
        }

        // Check if URI has been whitelisted from CSRF checks
        if ($exclude_uris = config_item('csrf_exclude_uris')) {
            $uri = load_class('URI', 'core');
            foreach ($exclude_uris as $excluded) {
                if (preg_match('#^' . $excluded . '$#i' . (UTF8_ENABLED ? 'u' : ''), $uri->uri_string())) {
                    return $this;
                }
            }
        }

        // Check CSRF token validity, but don't error on mismatch just yet - we'll want to regenerate
        $csrf_token = $_POST[$this->_csrf_token_name] ?? ($_SERVER['HTTP_X_CSRF'] ?? null);

        $valid =
            isset($csrf_token, $_COOKIE[$this->_csrf_cookie_name]) &&
            is_string($csrf_token) &&
            is_string($_COOKIE[$this->_csrf_cookie_name]) &&
            hash_equals($csrf_token, $_COOKIE[$this->_csrf_cookie_name]);

        // We kill this since we're done, and we don't want to pollute the _POST array
        unset($_POST[$this->_csrf_token_name]);

        // Regenerate on every submission?
        if (config_item('csrf_regenerate')) {
            // Nothing should last forever
            unset($_COOKIE[$this->_csrf_cookie_name]);
            $this->_csrf_hash = null;
        }

        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        if ($valid !== true) {
            $this->csrf_show_error();
        }

        log_message('info', 'CSRF token verified');
        return $this;
    }
}
