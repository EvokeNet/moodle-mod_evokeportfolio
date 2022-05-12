/**
 * Add comment js logic.
 *
 * @package
 * @subpackage mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */
/* eslint-disable */
define(['jquery', 'core/ajax', 'core/templates', 'mod_evokeportfolio/tribute_init'], function($, Ajax, Templates, TributeInit) {
    var LoadTimeline = function(courseid, evokation) {
        this.courseid = courseid;

        this.evokation = evokation;

        this.tribute = TributeInit.init();

        this.controlbutton = document.getElementById('myportfolio-tab');

        this.type = this.controlbutton.dataset.timeline_type;
        this.offset = parseInt(this.controlbutton.dataset.timeline_offset);
        this.portfolioid = this.controlbutton.dataset.timeline_portfolioid;
        this.hasmoreitems = this.controlbutton.dataset.timeline_hasmoreitems;

        this.loadItems();

        document.addEventListener('scroll', function(event) {
            var scrollTop = event.target.scrollingElement.scrollTop;
            var scrollHeight = event.target.scrollingElement.scrollHeight;
            var offsetHeight = event.target.scrollingElement.offsetHeight;

            if (this.hasmoreitems && !this.wait && (scrollTop + offsetHeight > scrollHeight - 40)) {
                if (!this.hasmoreitems) {
                    return;
                }

                if (!this.wait) {
                    this.loadItems();
                }
            }
        }.bind(this), false);

        $('.nav-tabs .nav-link').click(function(event) {
            this.controlbutton = event.target;

            this.type = event.target.dataset.timeline_type;
            this.offset = parseInt(event.target.dataset.timeline_offset);
            this.portfolioid = event.target.dataset.timeline_portfolioid;
            this.hasmoreitems = event.target.dataset.timeline_hasmoreitems === 'true';

            this.targetdiv = event.target.dataset.target;

            this.loadItems();
        }.bind(this));
    }

    LoadTimeline.prototype.loadItems = function() {
        this.wait = true;

        const request = Ajax.call([{
            methodname: 'mod_evokeportfolio_loadtimeline',
            args: {
                courseid: this.courseid,
                type: this.type,
                offset: this.offset,
                portfolioid: this.portfolioid
            }
        }]);

        request[0].done(function(response) {
            var data = JSON.parse(response.data);

            this.offset = parseInt(this.offset + 1);
            this.controlbutton.dataset.timeline_offset = this.offset;
            this.hasmoreitems = data.hasmoreitems;
            this.controlbutton.dataset.timeline_hasmoreitems = data.hasmoreitems;

            this.handleLoadData(data);
        }.bind(this));
    };

    LoadTimeline.prototype.handleLoadData = function(data) {
        Templates.render('mod_evokeportfolio/submission', data).then(function(content) {
            const targetdiv = $(this.targetdiv);

            targetdiv.find('.submission_loading-placeholder').addClass('hidden');

            targetdiv.find('.submissions.timeline').append(content);

            this.tribute.reload();

            this.wait = false;
        }.bind(this));
    };

    LoadTimeline.prototype.courseid = 0;

    LoadTimeline.prototype.type = 'my';

    LoadTimeline.prototype.offset = 0;

    LoadTimeline.prototype.portfolioid = null;

    LoadTimeline.prototype.hasmoreitems = true;

    LoadTimeline.prototype.targetdiv = '#myportfolio';

    LoadTimeline.prototype.wait = false;

    LoadTimeline.prototype.tribute = null;

    LoadTimeline.prototype.controlbutton = null;

    return {
        'init': function(courseid, evokation = false) {
            return new LoadTimeline(courseid, evokation);
        }
    };
});
