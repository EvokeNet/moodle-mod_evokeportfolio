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

        $usercoursegroup = $groupsutil->get_user_group($this->course->id);

        $groupmembers = $groupsutil->get_group_members($usercoursegroup->id);

        return [
            'contextid' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'groupmembers' => $groupmembers,
            'hasgroup' => !empty($usercoursegroup)
        ];
    }
}
