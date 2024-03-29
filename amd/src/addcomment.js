/**
 * Add comment js logic.
 *
 * @package
 * @subpackage mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

define(['jquery', 'core/str', 'core/ajax', 'mod_evokeportfolio/sweetalert'], function($, Str, Ajax, Swal) {
    var AddComment = function() {
        this.registerEventListeners();
    };

    AddComment.prototype.registerEventListeners = function() {
        $(document).on('click', '.commentbutton', function(event) {
            var inputtarget = $(event.currentTarget).closest('.submission')
                .find('.add-comment .input-group .post-comment-input');

            if (inputtarget.length > 0) {
                inputtarget.focus();
            }
        });

        $(document).on('click', '.post-comment-btn', function(event) {
            var target = $(event.currentTarget).closest('.input-group').children('.post-comment-input');

            if (target) {
                this.saveComment(target, target.html());
            }
        }.bind(this));
    };

    AddComment.prototype.saveComment = function(postinput, value) {
        if (value === '') {
            return;
        }

        var postdiv = postinput.closest('.submission');

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
            this.addCommentToPost(postdiv, data.comment);
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

    AddComment.prototype.addCommentToPost = function(postdiv, comment) {
        Str.get_string('editcomment', 'mod_evokeportfolio').then(function(editcomment) {
            var userimg = postdiv.find('.add-comment .userimg').clone();
            var userfullname = userimg.attr('alt');
            var loadallcomments = postdiv.find('.loadmore');

            var commentcontainer = $("<div class='submissioncomment fadeIn'>" +
                "<div class='userinfo'>" +
                "<div class='userimg'>" + $('<div/>').append(userimg).html() + "</div>" +
                "<div class='nameanddate'>" +
                "<p class='username'>" + userfullname + "</p>" +
                "<span class='small'>" + comment.humantimecreated + "</span>"+
                "</div>"+
                "<div class='editbutton ml-auto'>"+
                "<a class='btn-editcomment btn btn-sm btn-primary' data-id='"+comment.id+"'>"+editcomment+"</a>"+
                "</div>"+
                "</div>"+
                "<p class='text'>" + comment.message + "</p>" +
                "</div>");

            if (loadallcomments.length > 0) {
                commentcontainer.insertBefore(loadallcomments);
            } else {
                commentcontainer.insertBefore(postdiv.find('.add-comment'));
            }

            var totalcommentsspan = postdiv.find('.reactions .actions .commentbutton .totalcomments');
            var totalcomments = postdiv.find('.submissioncomment').length;

            totalcommentsspan.empty();

            totalcommentsspan.text(totalcomments);
        });
    };

    return {
        'init': function() {
            return new AddComment();
        }
    };
});
