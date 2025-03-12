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

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * Example:
     *
     * $debug = env('debug', FALSE);
     *
     * @param string $key Environment key.
     * @param mixed|null $default Default value in case the requested variable has no value.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    function env(string $key, mixed $default = null): mixed
    {
        if (empty($key)) {
            throw new InvalidArgumentException('The $key argument cannot be empty.');
        }

        return $_ENV[$key] ?? $default;
    }
}
