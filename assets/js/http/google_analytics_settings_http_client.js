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
 * Google Analytics Settings HTTP client.
 *
 * This module implements the Google Analytics settings related HTTP requests.
 */
App.Http.GoogleAnalyticsSettings = (function () {
    /**
     * Save Google Analytics settings.
     *
     * @param {Object} googleAnalyticsSettings
     *
     * @return {Object}
     */
    function save(googleAnalyticsSettings) {
        const url = App.Utils.Url.siteUrl('google_analytics_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            google_analytics_settings: googleAnalyticsSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
