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
 * Recovery page.
 *
 * This module implements the functionality of the recovery page.
 */
App.Pages.Recovery = (function () {
    const $form = $('form');
    const $username = $('#username');
    const $email = $('#email');
    const $getNewPassword = $('#get-new-password');

    /**
     * Event: Login Button "Click"
     *
     * Make an HTTP request to the server and check whether the user's credentials are right. If yes then redirect the
     * user to the destination page, otherwise display an error message.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        const $alert = $('.alert');

        $alert.addClass('d-none');

        $getNewPassword.prop('disabled', true);

        const username = $username.val();
        const email = $email.val();

        App.Http.Recovery.perform(username, email)
            .done((response) => {
                $alert.removeClass('d-none alert-danger alert-success');

                if (response.success) {
                    $alert.addClass('alert-success');
                    $alert.text(lang('new_password_sent_with_email'));
                } else {
                    $alert.addClass('alert-danger');
                    $alert.text(
                        'The operation failed! Please enter a valid username ' +
                            'and email address in order to get a new password.',
                    );
                }
            })
            .always(() => {
                $getNewPassword.prop('disabled', false);
            });
    }

    $form.on('submit', onFormSubmit);

    return {};
})();
