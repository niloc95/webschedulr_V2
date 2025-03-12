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
 * Validation utility.
 *
 * This module implements the functionality of validation.
 */
window.App.Utils.Validation = (function () {
    /**
     * Validate the provided email.
     *
     * @param {String} value
     *
     * @return {Boolean}
     */
    function email(value) {
        const re =
            /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        return re.test(value);
    }

    /**
     * Validate the provided phone.
     *
     * @param {String} value
     *
     * @return {Boolean}
     */
    function phone(value) {
        const re = /^[+]?([0-9]*[\.\s\-\(\)]|[0-9]+){3,24}$/;

        return re.test(value);
    }

    return {
        email,
        phone,
    };
})();
