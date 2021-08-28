<?php

/**
 * Activity base class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace mod_evokeportfolio\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Activity base class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
abstract class activity_base extends \core_analytics\local\indicator\community_of_inquiry_activity {

    /**
     * feedback_viewed_events
     *
     * @return string[]
     */
    protected function feedback_viewed_events() {
        return [
            '\mod_evokeportfolio\event\course_module_viewed',
            '\mod_evokeportfolio\event\submission_updated'
        ];
    }

    /**
     * feedback_viewed
     *
     * @param \cm_info $cm
     * @param int $contextid
     * @param int $userid
     * @param int $after
     * @return bool
     */
    protected function feedback_viewed(\cm_info $cm, $contextid, $userid, $after = null) {
        $after = null;
        if ($this->instancedata[$cm->instance]->datelimit) {
            $after = $this->instancedata[$cm->instance]->datelimit;
        }

        return $this->feedback_post_action($cm, $contextid, $userid, $this->feedback_viewed_events(), $after);
    }

    /**
     * Returns the name of the field that controls activity availability.
     *
     * @return null|string
     */
    protected function get_timeclose_field() {
        return 'datelimit';
    }
}
