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
 * Google HTTP client.
 *
 * This module implements the Google Calendar related HTTP requests.
 */
App.Http.Google = (function () {
    /**
     * Select the Google Calendar for the synchronization with a provider.
     *
     * @param {Number} providerId
     * @param {String} googleCalendarId
     *
     * @return {*|jQuery}
     */
    function selectGoogleCalendar(providerId, googleCalendarId) {
        const url = App.Utils.Url.siteUrl('google/select_google_calendar');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
            calendar_id: googleCalendarId,
        };

        return $.post(url, data);
    }

    /**
     * Disable the Google Calendar syncing of a provider.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function disableProviderSync(providerId) {
        const url = App.Utils.Url.siteUrl('google/disable_provider_sync');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    /**
     * Get the available Google Calendars of the connected provider's account.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function getGoogleCalendars(providerId) {
        const url = App.Utils.Url.siteUrl('google/get_google_calendars');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    /**
     * Trigger the sync process between @webSchedulr and Google Calendar.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function syncWithGoogle(providerId) {
        const url = App.Utils.Url.siteUrl('google/sync/' + providerId);

        return $.get(url);
    }

    return {
        getGoogleCalendars,
        selectGoogleCalendar,
        disableProviderSync,
        syncWithGoogle,
    };
})();
