<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use renderable;
use templatable;
use renderer_base;

/**
 * Manage chapters renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class managechapters implements renderable, templatable {

    public $course;
    public $context;

    public function __construct($course, $context) {
        $this->course = $course;
        $this->context = $context;
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
        $chapterutil = new chapter();

        $data = [
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'chapters' => $chapterutil->get_course_chapters($this->course->id)
        ];

        return $data;
    }
}
