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
 * General Settings HTTP client.
 *
 * This module implements the general settings related HTTP requests.
 */
App.Http.GeneralSettings = (function () {
    /**
     * Save general settings.
     *
     * @param {Object} generalSettings
     *
     * @return {Object}
     */
    function save(generalSettings) {
        const url = App.Utils.Url.siteUrl('general_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            general_settings: generalSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
