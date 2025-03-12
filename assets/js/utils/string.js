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
 * Strings utility.
 *
 * This module implements the functionality of strings.
 */
window.App.Utils.String = (function () {
    /**
     * Upper case the first letter of the provided string.
     *
     * Old Name: GeneralFunctions.upperCaseFirstLetter
     *
     * @param {String} value
     *
     * @returns {string}
     */
    function upperCaseFirstLetter(value) {
        return value.charAt(0).toUpperCase() + value.slice(1);
    }

    /**
     * Escape HTML content with the use of jQuery.
     *
     * Old Name: GeneralFunctions.escapeHtml
     *
     * @param {String} content
     *
     * @return {String}
     */
    function escapeHtml(content) {
        return $('<div/>').text(content).html();
    }

    return {
        upperCaseFirstLetter,
        escapeHtml,
    };
})();
