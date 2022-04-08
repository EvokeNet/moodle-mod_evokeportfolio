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
        $submissionutil = new submission();

        // Chapters data.
        $chapters = $chapterutil->get_course_chapters($this->course->id);

        if (!$chapters) {
            return [
                'courseid' => $this->course->id
            ];
        }

        $mysubmissions = $submissionutil->get_portfolio_submissions($this->portfolio, $this->get_portfolio_context($this->portfolio->id), $USER->id);

        $userpicture = user::get_user_image_or_avatar($USER);

        $groupsutil = new group();

        $usercoursegroup = $groupsutil->get_user_group($this->course->id);

        $groupmembers = $groupsutil->get_group_members($usercoursegroup->id);

        $groupsubmissions = $submissionutil->get_portfolio_submissions($this->portfolio, $this->get_portfolio_context($this->portfolio->id), null, $usercoursegroup->id);

        $networksubmissions = $submissionutil->get_portfolio_submissions($this->portfolio, $this->get_portfolio_context($this->portfolio->id));

        return [
            'contextid' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'groupmembers' => $groupmembers,
            'hasgroup' => !empty($usercoursegroup),
            'portfolio' => $this->portfolio,
            'mysubmissions' => $mysubmissions,
            'groupsubmissions' => $groupsubmissions,
            'networksubmissions' => $networksubmissions
        ];
    }

    private function get_portfolio_context($portfolioid) {
        if (isset($this->portfoliocontexts[$portfolioid])) {
            return $this->portfoliocontexts[$portfolioid];
        }

        $coursemodule = get_coursemodule_from_instance('evokeportfolio', $portfolioid);

        $this->portfoliocontexts[$portfolioid] = \context_module::instance($coursemodule->id);

        return $this->portfoliocontexts[$portfolioid];
    }
}
