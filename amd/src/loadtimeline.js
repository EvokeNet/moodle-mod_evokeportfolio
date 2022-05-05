/**
 * Add comment js logic.
 *
 * @package
 * @subpackage mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */
/* eslint-disable */
define(['jquery', 'core/ajax', 'core/templates'], function($, Ajax, Templates) {
    var LoadTimeline = function(courseid, evokation) {
        this.courseid = courseid;

        this.evokation = evokation;

        this.loaditems();

        document.addEventListener('scroll', function(event) {
            var scrollTop = event.target.scrollingElement.scrollTop;
            var scrollHeight = event.target.scrollingElement.scrollHeight;
            var offsetHeight = event.target.scrollingElement.offsetHeight;

            if (this.hasmoreitems && !this.wait && (scrollTop + offsetHeight > scrollHeight - 40)) {
                this.loaditems();
            }
        }.bind(this));

        $('.nav-tabs .nav-link').click(function(event) {
            this.type = this.target = event.target.dataset.timeline_type;
            this.limit = this.target = event.target.dataset.timeline_limit;
            this.hasmoreitems = event.target.dataset.timeline_hasmoreitems === 'true';

            this.targetdiv = event.target.dataset.target;

            this.loaditems();
        }.bind(this));
    }

    LoadTimeline.prototype.courseid = 0;

    LoadTimeline.prototype.type = 'my';

    LoadTimeline.prototype.limit = 0;

    LoadTimeline.prototype.hasmoreitems = true;

    LoadTimeline.prototype.targetdiv = '#myportfolio';

    LoadTimeline.prototype.wait = false;

    LoadTimeline.prototype.loaditems = function() {
        this.wait = true;

        Ajax.call([{
            methodname: 'mod_evokeportfolio_loadtimeline',
            args: {
                courseid: this.courseid,
                type: this.type,
                limit: this.limit,
                hasmoreitems: this.hasmoreitems
            },
            done: this.handleLoadData.bind(this)
        }]);
    };

    LoadTimeline.prototype.handleLoadData = function(response) {
        const data = JSON.parse(response.data);

        Templates.render('mod_evokeportfolio/submission', data).then(function(content) {
            const targetdiv = $(this.targetdiv);

            targetdiv.find('.submission_loading-placeholder').addClass('hidden');

            targetdiv.find('.submissions.timeline').append(content);
        }.bind(this));
    };

    return {
        'init': function(courseid, evokation = false) {
            return new LoadTimeline(courseid, evokation);
        }
    };
});
