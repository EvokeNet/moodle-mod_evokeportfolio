<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use block_evokehq\util\course;
use mod_evokeportfolio\table\entries as entriestable;
use mod_evokeportfolio\util\chapter;
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
        $courseutil = new course();

        $portfolios = $courseutil->get_course_portfolios($this->course->id);

        if (!$portfolios) {
            return ['hasportfolios' => false];
        }

        return [
            'hasportfolios' => true,
            'portfolios' => $portfolios
        ];
    }
}
