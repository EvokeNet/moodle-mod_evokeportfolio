<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

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
class evokationadmin implements renderable, templatable {

    public $course;
    public $context;
    public $chapter;
    public $portfolio;
    public $group;

    public function __construct($course, $context, $chapter = null, $portfolio = null, $group = null) {
        $this->course = $course;
        $this->context = $context;
        $this->chapter = $chapter;
        $this->portfolio = $portfolio;
        $this->group = $group;
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

        $portfolioutil = new evokeportfolio();
        $grouputil = new group();
        $submissionutil = new submission();

        // Portfolios data.
        $portfolios = $portfolioutil->get_course_portfolio_instances($this->course->id, 1);

        $portfoliosdata = ['portfolios' => $portfolios];
        if ($this->portfolio) {
            $portfoliosdata['currentportfolioid'] = $this->portfolio->id;
        }

        // Groups data.
        $groups = $grouputil->get_course_groups($this->course, false);

        $groupsdata = ['groups' => $groups];
        $groupid = null;
        if ($this->group) {
            $groupsdata['currentgroupid'] = $this->group->id;
            $groupid = $this->group->id;
        }

        $filters = $this->get_filters($output, [], $portfoliosdata, $groupsdata);

        if ($this->portfolio) {
            $portfolios = [$this->portfolio];
        }

        foreach ($portfolios as $portfolio) {
            $portfolio->submissions = $submissionutil->get_portfolio_submissions($portfolio, $this->context, null, $groupid);
        }

        $userpicture = user::get_user_image_or_avatar($USER);

        $cangrade = has_capability('mod/evokeportfolio:grade', $this->context);

        return [
            'contextid' => $this->context->id,
            'courseid' => $this->course->id,
            'filters' => $filters,
            'groups' => $groups,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'portfolios' => $portfolios,
            'cangrade' => $cangrade
        ];
    }

    private function get_filters(renderer_base $output, $chaptersdata, $portfoliosdata, $groupsdata) {
        $filtersrenderer = new indexadminfilters($this->course->id, $chaptersdata, $portfoliosdata, $groupsdata);

        return $output->render($filtersrenderer);
    }
}
