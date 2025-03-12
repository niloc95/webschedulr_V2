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
 * Legal Settings HTTP client.
 *
 * This module implements the legal settings related HTTP requests.
 */
App.Http.LegalSettings = (function () {
    /**
     * Save legal settings.
     *
     * @param {Object} legalSettings
     *
     * @return {Object}
     */
    function save(legalSettings) {
        const url = App.Utils.Url.siteUrl('legal_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            legal_settings: legalSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
