<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\user;
use renderable;
use templatable;
use renderer_base;

/**
 * Index renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class evokation implements renderable, templatable {

    public $course;

    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Export the data
     *
     * @param renderer_base $output
     *
     * @return array|\stdClass
     *
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $userpicture = user::get_user_image_or_avatar($USER);

        $groupsutil = new group();

        $usercoursegroups = $groupsutil->get_user_groups($this->course->id);

        $context = \context_course::instance($this->course->id);

        $groupsmembers = [];
        if ($usercoursegroups) {
            $groupsmembers = $groupsutil->get_groups_members($usercoursegroups, true, $context);
        }

        return [
            'contextid' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'groupsmembers' => $groupsmembers,
            'hasgroup' => !empty($usercoursegroups)
        ];
    }
}
