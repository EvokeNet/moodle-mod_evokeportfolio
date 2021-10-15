/**
 * Add comment js logic.
 *
 * @package    mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

define(['jquery', 'core/ajax', 'mod_evokeportfolio/sweetalert'], function($, Ajax, Swal) {
    var AddComment = function() {
        this.registerEventListeners();
    };

    AddComment.prototype.registerEventListeners = function() {
        $(".post-comment-input").keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if (keycode === 13) {
                event.preventDefault();

                var target = $(event.currentTarget);

                this.saveComment(target, target.html());
            }
        }.bind(this));

        $(".post-comment-btn").click(function(event) {
            var target = $(event.currentTarget).closest('.input-group').children('.post-comment-input');

            this.saveComment(target, target.html());
        }.bind(this));
    };

    AddComment.prototype.saveComment = function(postinput, value) {
        if (value === '') {
            return;
        }

        var postdiv = postinput.closest('.mainpost');

        postinput.empty();

        if (postdiv.length === 0 || postdiv.length > 1) {
            this.showToast('error', 'Error trying to find the discussion for this comment.');

            return;
        }

        var id = postdiv.data('id');

        var request = Ajax.call([{
            methodname: 'mod_evokeportfolio_addcomment',
            args: {
                comment: {
                    submissionid: id,
                    message: value,
                }
            }
        }]);

        request[0].done(function(data) {
            this.addCommentToPost(postdiv, data.message);
        }.bind(this)).fail(function(error) {
            var message = error.message;

            if (!message) {
                message = error.error;
            }

            this.showToast('error', message);
        }.bind(this));
    };

    AddComment.prototype.showToast = function(type, message) {
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

    AddComment.prototype.addCommentToPost = function(postdiv, value) {
        var userimg = postdiv.find('.add-comment .userimg').clone();
        var userfullname = userimg.attr('alt');
        var loadallcomments = postdiv.find('.loadmore');

        var comment = $("<div class='submissioncomment fadeIn'>" +
          "<div class='userimg'>" + $('<div/>').append(userimg).html() + "</div>" +
          "<div class='entry'><div class='entry-content'>" +
          "<p class='name'>" + userfullname + "</p>" +
          "<p class='text'>" + value + "</p>" +
          "</div></div></div>");

        if (loadallcomments.length > 0) {
            comment.insertBefore(loadallcomments);
        } else {
            comment.insertBefore(postdiv.find('.add-comment'));
        }
    };

    return {
        'init': function() {
            return new AddComment();
        }
    };
});
