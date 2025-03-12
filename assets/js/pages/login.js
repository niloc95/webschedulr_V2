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
 * Login page.
 *
 * This module implements the functionality of the login page.
 */
App.Pages.Login = (function () {
    const $loginForm = $('#login-form');
    const $username = $('#username');
    const $password = $('#password');

    /**
     * Login Button "Click"
     *
     * Make an ajax call to the server and check whether the user's credentials are right.
     *
     * If yes then redirect him to his desired page, otherwise display a message.
     */
    function onLoginFormSubmit(event) {
        event.preventDefault();

        const username = $username.val();
        const password = $password.val();

        if (!username || !password) {
            return;
        }

        const $alert = $('.alert');

        $alert.addClass('d-none');

        App.Http.Login.validate(username, password).done((response) => {
            if (response.success) {
                window.location.href = vars('dest_url');
            } else {
                $alert.text(lang('login_failed'));
                $alert.removeClass('d-none alert-danger alert-success').addClass('alert-danger');
            }
        });
    }

    $loginForm.on('submit', onLoginFormSubmit);

    return {};
})();
