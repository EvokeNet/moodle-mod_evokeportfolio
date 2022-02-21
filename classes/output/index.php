<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

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
class index implements renderable, templatable {

    public $course;
    public $context;
    public $chapter;
    public $portfolio;

    public function __construct($course, $context, $chapter = null, $portfolio = null) {
        $this->course = $course;
        $this->context = $context;
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

        $chapterutil = new chapter();
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

        $filtersrenderer = new indexfilters($this->course->id, $chaptersdata, $portfoliosdata);

        $filters = $output->render($filtersrenderer);

        // Workaround to clone portfolios array and its objects.
        $groupportfolios = array_map(function ($object) { return clone $object; }, $portfolios);

        // Workaround to clone portfolios array and its objects.
        $networkportfolios = array_map(function ($object) { return clone $object; }, $portfolios);

        if ($portfolios) {
            foreach ($portfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context, $USER->id);
            }
        }

        $userpicture = theme_evoke_get_user_avatar_or_image($USER);

        $groupsutil = new group();

        $usercoursegroup = $groupsutil->get_user_group($this->course->id);

        $groupmembers = $groupsutil->get_group_members($usercoursegroup->id);

        if ($groupportfolios && $usercoursegroup) {
            foreach ($groupportfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context, null, $usercoursegroup->id);
            }
        }

        if ($networkportfolios) {
            foreach ($networkportfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context);
            }
        }

        return [
            'contextid' => $this->context->id,
            'courseid' => $this->course->id,
            'filters' => $filters,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'groupmembers' => $groupmembers,
            'hasgroup' => !empty($usercoursegroup),
            'portfolios' => $portfolios,
            'groupportfolios' => $groupportfolios,
            'networkportfolios' => $networkportfolios
        ];
    }
}
