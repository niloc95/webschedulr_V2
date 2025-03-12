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
 * Recovery HTTP client.
 *
 * This module implements the account recovery related HTTP requests.
 */
App.Http.Recovery = (function () {
    /**
     * Perform an account recovery.
     *
     * @param {String} username
     * @param {String} email
     *
     * @return {Object}
     */
    function perform(username, email) {
        const url = App.Utils.Url.siteUrl('recovery/perform');

        const data = {
            csrf_token: vars('csrf_token'),
            username,
            email,
        };

        return $.post(url, data);
    }

    return {
        perform,
    };
})();
