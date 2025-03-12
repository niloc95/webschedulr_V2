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
 * Booking Settings HTTP client.
 *
 * This module implements the booking settings related HTTP requests.
 */
App.Http.BookingSettings = (function () {
    /**
     * Save booking settings.
     *
     * @param {Object} bookingSettings
     *
     * @return {Object}
     */
    function save(bookingSettings) {
        const url = App.Utils.Url.siteUrl('booking_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            booking_settings: bookingSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
