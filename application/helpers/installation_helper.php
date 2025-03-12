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
 * Check if @webSchedulr is installed.
 *
 * This function will check some factors to determine if @webSchedulr is installed or not. It is possible that the
 * installation is properly configure without being recognized by this method.
 *
 * Notice: You can add more checks into this file in order to further check the installation state of the application.
 *
 * @return bool Returns whether webSchdeulr is installed or not.
 */
function is_app_installed(): bool
{
    $CI = &get_instance();

    return $CI->db->table_exists('users');
}
