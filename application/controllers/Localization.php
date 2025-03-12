<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Open Source Web Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) 2013 - 2020, Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.3
 * ---------------------------------------------------------------------------- */

/**
 * Localization Controller
 *
 * Contains all the localization related methods.
 *
 * @package Controllers
 */
class Localization extends WS_Controller
{
    /**
     * Change system language for current user.
     *
     * The language setting is stored in session data and retrieved every time the user visits any of the system pages.
     *
     * Notice: This method used to be in the Backend_api.php.
     */
    public function change_language(): void
    {
        try {
            // Check if language exists in the available languages.
            $language = request('language');

            if (!in_array($language, config('available_languages'))) {
                throw new RuntimeException(
                    'Translations for the given language does not exist (' . request('language') . ').',
                );
            }

            $language = request('language');

            session(['language' => $language]);

            config(['language' => $language]);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
