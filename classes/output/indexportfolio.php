<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\submission;
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
class indexportfolio implements renderable, templatable {

    public $course;
    public $portfolio;
    private $portfoliocontexts = [];

    public function __construct($course, $portfolio = null) {
        $this->course = $course;
        $this->portfolio = $portfolio;
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

        $chapterutil = new chapter();

        // Chapters data.
        $chapters = $chapterutil->get_course_chapters($this->course->id);

        if (!$chapters) {
            return [
                'courseid' => $this->course->id
            ];
        }

        $groupsutil = new group();

        $usercoursegroups = $groupsutil->get_user_groups($this->portfolio->course);

        $groupsmembers = [];
        if ($usercoursegroups) {
            $groupsmembers = $groupsutil->get_groups_members($usercoursegroups);
        }

        $data['contextid'] = \context_course::instance($this->portfolio->course)->id;
        $data['groupsmembers'] = $groupsmembers;
        $data['hasgroup'] = !empty($usercoursegroups);
        $data['portfolioid'] = $this->portfolio->id;

        return [
            'contextid' => \context_course::instance($this->course->id)->id,
            'courseid' => $this->course->id,
            'userfullname' => fullname($USER),
            'groupsmembers' => $groupsmembers,
            'hasgroup' => !empty($usercoursegroups),
            'portfolio' => $this->portfolio,
        ];
    }
}
