<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\evokeportfolio;
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
class evokation implements renderable, templatable {

    public $course;
    public $chapter;
    public $portfolio;
    private $portfoliocontexts = [];

    public function __construct($course, $chapter = null, $portfolio = null) {
        $this->course = $course;
        $this->chapter = $chapter;
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

        $submissionutil = new submission();
        $portfolioutil = new evokeportfolio();

        // Evokations data.
        $portfolios = $portfolioutil->get_course_portfolio_instances($this->course->id, 1);

        // Workaround to clone portfolios array and its objects.
        $groupportfolios = array_map(function ($object) { return clone $object; }, $portfolios);

        // Workaround to clone portfolios array and its objects.
        $networkportfolios = array_map(function ($object) { return clone $object; }, $portfolios);

        if ($portfolios) {
            foreach ($portfolios as $portfolio) {
                $portfolio->submissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), $USER->id);

                $portfolio->isevaluated = $portfolio->grade != 0;
            }
        }

        $userpicture = user::get_user_image_or_avatar($USER);

        $groupsutil = new group();

        $usercoursegroup = $groupsutil->get_user_group($this->course->id);

        $groupmembers = $groupsutil->get_group_members($usercoursegroup->id);

        if ($groupportfolios && $usercoursegroup) {
            foreach ($groupportfolios as $portfolio) {
                $portfolio->submissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), null, $usercoursegroup->id);
            }
        }

        if ($networkportfolios) {
            foreach ($networkportfolios as $portfolio) {
                $portfolio->submissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id));
            }
        }

        return [
            'contextid' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'groupmembers' => $groupmembers,
            'hasgroup' => !empty($usercoursegroup),
            'portfolios' => $portfolios,
            'groupportfolios' => $groupportfolios,
            'networkportfolios' => $networkportfolios
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
