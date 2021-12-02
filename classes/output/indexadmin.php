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

    public function __construct($course, $context, $chapter = null) {
        $this->course = $course;
        $this->context = $context;
        $this->chapter = $chapter;
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

        $chapters = $chapterutil->get_course_chapters($this->course->id);

        if (!$chapters) {
            return [
                'courseid' => $this->course->id,
                'chapters' => []
            ];
        }

        $currentchapter = 0;
        if ($this->chapter) {
            $currentchapter = $this->chapter;
        }

        if (!$this->chapter && $chapters) {
            $currentchapter = current($chapters);
        }

        $portfolios = $chapterutil->get_chapter_portfolios($currentchapter);
        $groups = $grouputil->get_course_groups($this->course, false);

        if ($portfolios) {
            foreach ($portfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context);
            }
        }

        $userpicture = theme_moove_get_user_avatar_or_image($USER);

        return [
            'contextid' => $this->context->id,
            'courseid' => $this->course->id,
            'chapters' => $chapters,
            'portfolios' => $portfolios,
            'groups' => $groups,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
        ];
    }
}
