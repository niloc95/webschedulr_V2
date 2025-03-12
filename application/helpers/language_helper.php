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

if (!function_exists('lang')) {
    /**
     * Lang
     *
     * Fetches a language variable and optionally outputs a form label
     *
     * @param string $line The language line.
     * @param string $for The "for" value (id of the form element).
     * @param array $attributes Any additional HTML attributes.
     *
     * @return string
     */
    function lang(string $line, string $for = '', array $attributes = []): string
    {
        /** @var WS_Controller $CI */
        $CI = get_instance();

        $result = $CI->lang->line($line);

        if ($for !== '') {
            $result = '<label for="' . $for . '"' . _stringify_attributes($attributes) . '>' . $result . '</label>';
        }

        return $result ?: $line;
    }
}
