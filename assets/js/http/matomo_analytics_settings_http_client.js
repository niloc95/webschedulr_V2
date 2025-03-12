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
 * Matomo Analytics Settings HTTP client.
 *
 * This module implements the Matomo Analytics settings related HTTP requests.
 */
App.Http.MatomoAnalyticsSettings = (function () {
    /**
     * Save Matomo Analytics settings.
     *
     * @param {Object} matomoAnalyticsSettings
     *
     * @return {Object}
     */
    function save(matomoAnalyticsSettings) {
        const url = App.Utils.Url.siteUrl('matomo_analytics_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            matomo_analytics_settings: matomoAnalyticsSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
