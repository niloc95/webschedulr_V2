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
 * LDAP Settings HTTP client.
 *
 * This module implements the LDAP settings related HTTP requests.
 */
App.Http.LdapSettings = (function () {
    /**
     * Save LDAP settings.
     *
     * @param {Object} ldapSettings
     *
     * @return {Object}
     */
    function save(ldapSettings) {
        const url = App.Utils.Url.siteUrl('ldap_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            ldap_settings: ldapSettings,
        };

        return $.post(url, data);
    }

    /**
     * Search LDAP server.
     *
     * @param {String} keyword
     *
     * @return {Object}
     */
    function search(keyword) {
        const url = App.Utils.Url.siteUrl('ldap_settings/search');

        const data = {
            csrf_token: vars('csrf_token'),
            keyword,
        };

        return $.post(url, data);
    }

    return {
        save,
        search,
    };
})();
