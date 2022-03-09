/**
 * Add comment js logic.
 *
 * @package
 * @subpackage    mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

/* eslint-disable no-console */
define(['jquery', 'core/ajax', 'mod_evokeportfolio/sweetalert'], function($, Ajax, Swal) {
    var ToggleLike = function() {
        this.registerEventListeners();
    };

    ToggleLike.prototype.registerEventListeners = function() {
        $(".likebutton").click(function(event) {
            var submissiondiv = $(event.currentTarget).closest('.submission');

            if (submissiondiv.length === 0 || submissiondiv.length > 1) {
                this.showToast('error', 'Error trying to find the discussion for this comment.');

                return;
            }

            var id = submissiondiv.data('id');

            var request = Ajax.call([{
                methodname: 'mod_evokeportfolio_togglereaction',
                args: {
                    submissionid: id,
                    reactionid: 1
                }
            }]);

            request[0].done(function(data) {

                var statusdiv = submissiondiv.find('.reactions .status');
                var likebutton = submissiondiv.find('.actions .likebutton');

                statusdiv.empty();

                likebutton.toggleClass('hasreacted');

                if (data.message == false || data.message == 'false') {
                    return;
                }

                statusdiv.html('<i class="fa fa-thumbs-up"></i> ' + data.message);
            }.bind(this)).fail(function(error) {
                var message = error.message;

                if (!message) {
                    message = error.error;
                }

                this.showToast('error', message);
            }.bind(this));
        }.bind(this));
    };

    ToggleLike.prototype.showToast = function(type, message) {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 8000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    };

    return {
        'init': function() {
            return new ToggleLike();
        }
    };
});
