<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use renderable;
use templatable;
use renderer_base;

/**
 * Manage sections renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class gradingchapters implements renderable, templatable {

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
        $util = new chapter();

        $data = [
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'chapters' => $util->get_course_chapters_with_portfolios($this->course->id)
        ];

        return $data;
    }
}
