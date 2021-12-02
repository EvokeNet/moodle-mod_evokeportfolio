<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use block_evokehq\util\course;
use mod_evokeportfolio\table\entries as entriestable;
use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\group;
use renderable;
use templatable;
use renderer_base;

/**
 * Index renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class indexadmin implements renderable, templatable {

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

        $chapterutil = new chapter();
        $grouputil = new group();
        $portfolioutil = new evokeportfolio();

        // Chapters data.
        $chapters = $chapterutil->get_course_chapters($this->course->id);

        if (!$chapters) {
            return [
                'courseid' => $this->course->id
            ];
        }

        $currentchapter = new \stdClass();
        if ($this->chapter) {
            $currentchapter = $this->chapter;
        }

        if (!$this->chapter && $chapters) {
            $currentchapter = current($chapters);
        }

        $chaptersdata = [
            'currentchapterid' => $currentchapter->id,
            'chapters' => $chapters
        ];

        // Portfolios data.
        $chapterportfolios = $chapterutil->get_chapter_portfolios($currentchapter);

        $portfolios[] = $this->portfolio;
        if (!$this->portfolio) {
            $portfolios = $chapterportfolios;
        }

        $portfoliosdata = ['portfolios' => $chapterportfolios];
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

        $filters = $this->get_filters($output, $chaptersdata, $portfoliosdata, $groupsdata);

        if ($portfolios) {
            foreach ($portfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context, null, $groupid);
            }
        }

        $userpicture = theme_moove_get_user_avatar_or_image($USER);

        return [
            'contextid' => $this->context->id,
            'courseid' => $this->course->id,
            'filters' => $filters,
            'groups' => $groups,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'portfolios' => $portfolios
        ];
    }

    private function get_filters(renderer_base $output, $chaptersdata, $portfoliosdata, $groupsdata) {
        $filtersrenderer = new indexadminfilters($this->course->id, $chaptersdata, $portfoliosdata, $groupsdata);

        return $output->render($filtersrenderer);
    }
}
