<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\evokeportfolio;
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
        $portfolios = $chapterutil->get_chapter_portfolios($currentchapter);

        if ($portfolios) {
            foreach ($portfolios as $portfolio) {
                $portfolio->submissions = $portfolioutil->get_portfolio_submissions($portfolio, $this->context, $USER->id);
            }
        }

        $filtersrenderer = new indexfilters($this->course->id, $chaptersdata);

        $filters = $output->render($filtersrenderer);

        $userpicture = theme_moove_get_user_avatar_or_image($USER);

        return [
            'contextid' => $this->context->id,
            'courseid' => $this->course->id,
            'filters' => $filters,
            'portfolios' => $portfolios,
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
        ];
    }
}
