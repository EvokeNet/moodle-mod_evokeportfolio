/**
 * Comment button click js logic.
 *
 * @package    mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

define(['jquery'], function($) {
    var Comment = function() {
        this.registerEventListeners();
    };

    Comment.prototype.registerEventListeners = function() {
        $(".commentbutton").click(function(event) {
            var inputtarget = $(event.currentTarget).closest('.mainpost')
                                                       .find('.add-comment .input-group .post-comment-input');

            if (inputtarget.length > 0) {
                inputtarget.focus();
            }
        });
    };

    return {
        'init': function() {
            return new Comment();
        }
    };
});
