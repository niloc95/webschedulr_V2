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
 * @webSchedulr loader.
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
class WS_Loader extends CI_Loader
{
    /**
     * Override the original view loader method so that layouts are also supported.
     *
     * @param string $view View filename.
     * @param array $vars An associative array of data to be extracted for use in the view.
     * @param bool $return Whether to return the view output or leave it to the Output class.
     *
     * @return object|string
     */
    public function view($view, $vars = [], $return = false)
    {
        $layout = config('layout');

        $is_layout_page = empty($layout); // This is a layout page if "layout" was undefined before the page got rendered.

        $result = $this->_ci_load([
            '_ci_view' => $view,
            '_ci_vars' => $this->_ci_prepare_view_vars($vars),
            '_ci_return' => $return,
        ]);

        $layout = config('layout');

        if ($layout && $is_layout_page) {
            $result = $this->_ci_load([
                '_ci_view' => $layout['filename'],
                '_ci_vars' => $this->_ci_prepare_view_vars($vars),
                '_ci_return' => $return,
            ]);
        }

        return $result;
    }
}
