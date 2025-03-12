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
 * Localization HTTP client.
 *
 * This module implements the account related HTTP requests.
 */
App.Http.Localization = (function () {
    /**
     * Change language.
     *
     * @param {String} language
     */
    function changeLanguage(language) {
        const url = App.Utils.Url.siteUrl('localization/change_language');

        const data = {
            csrf_token: vars('csrf_token'),
            language,
        };

        return $.post(url, data);
    }

    return {
        changeLanguage,
    };
})();
