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
 * Booking layout.
 *
 * This module implements the booking layout functionality.
 */
window.App.Layouts.Booking = (function () {
    const $selectLanguage = $('#select-language');

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Utils.Lang.enableLanguageSelection($selectLanguage);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
