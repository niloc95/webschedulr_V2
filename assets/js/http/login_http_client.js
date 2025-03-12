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
 * Login HTTP client.
 *
 * This module implements the account login related HTTP requests.
 */
App.Http.Login = (function () {
    /**
     * Perform an account recovery.
     *
     * @param {String} username
     * @param {String} password
     *
     * @return {Object}
     */
    function validate(username, password) {
        const url = App.Utils.Url.siteUrl('login/validate');

        const data = {
            csrf_token: vars('csrf_token'),
            username,
            password,
        };

        return $.post(url, data);
    }

    return {
        validate,
    };
})();
