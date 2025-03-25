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

if (!function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * Example:
     *
     * $logs_path = storage_path('logs'); // Returns "/path/to/installation/dir/storage/logs"
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return FCPATH . 'storage/' . trim($path);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the current installation.
     *
     * $controllers_path = base_path('application/controllers'); // Returns "/path/to/installation/dir/application/controllers"
     *
     * @param string $path
     *
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return FCPATH . trim($path);
    }
}
