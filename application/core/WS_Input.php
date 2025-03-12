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
 * @webSchedulr input.
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
 * @property string $raw_input_stream
 */
class WS_Input extends CI_Input
{
    /**
     * Fetch an item from JSON data.
     *
     * @param string|null $index Index for item to be fetched from the JSON payload.
     * @param bool|false $xss_clean Whether to apply XSS filtering
     *
     * @return mixed
     */
    public function json(?string $index = null, bool $xss_clean = false): mixed
    {
        /** @var WS_Controller $CI */
        $CI = &get_instance();

        if (strpos((string) $CI->input->get_request_header('Content-Type'), 'application/json') === false) {
            return null;
        }

        $input_stream = $CI->input->raw_input_stream;

        if (empty($input_stream)) {
            return null;
        }

        $payload = json_decode($input_stream, true);

        if ($xss_clean) {
            foreach ($payload as $name => $value) {
                $payload[$name] = $CI->security->xss_clean($value);
            }
        }

        if (empty($index)) {
            return $payload;
        }

        return $payload[$index] ?? null;
    }
}
