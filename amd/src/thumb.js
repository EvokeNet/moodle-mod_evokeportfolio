/* eslint-disable */

/**
 * Thumb js logic.
 *
 * @package
 * @subpackage    mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

define(['jquery'], function($) {
    var Thumb = function() {
        const modal = '<div id="image-modal" class="modal" aria-hidden="true">\n' +
            '    <span class="close" aria-label="Close">&times;</span>\n' +
            '    <img class="modal-content">\n' +
            '</div>';
        $('body').append(modal);

        this.registerEventListeners();
    };

    Thumb.prototype.registerEventListeners = function() {
        $(document).on('click', '.thumbnail-container', function(event) {
            var imgsrc = $(event.currentTarget).find('.img-responsive').attr('src');
            var originalimage = imgsrc.replace('/thumb', '');
            var modal = $('#image-modal');

            modal.css('display', 'block');
            modal.attr('aria-hidden', 'false');

            var imagecontaier = $('#image-modal .modal-content');
            imagecontaier.attr('src', originalimage);
        });

        $(document).on('click', '#image-modal .close',function() {
            var modal = $('#image-modal');
            modal.css('display', 'none');
            modal.attr('aria-hidden', 'true');
        });

        $(document).on('keydown', function(event) {
            var modal = $('#image-modal');

            if (event.which == 27) {
                modal.css('display', 'none');
                modal.attr('aria-hidden', 'true');
            }
        });
    }

    return {
        'init': function() {
            return new Thumb();
        }
    };
});
