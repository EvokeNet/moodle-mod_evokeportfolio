<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

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
        global $USER;

        $chapterutil = new chapter();

        $chapters = $chapterutil->get_course_chapters($this->course->id);

        foreach ($chapters as $key => $chapter) {
            $portfolioswithusersubmissions = $chapterutil->get_portfolios_with_user_submissions($this->context, $chapter, $USER);

            if (!$portfolioswithusersubmissions) {
                $chapters[$key]->portfolios = false;

                continue;
            }

            foreach ($portfolioswithusersubmissions as $pkey => $portfolioswithusersubmission) {
                $cm = get_coursemodule_from_instance('evokeportfolio', $portfolioswithusersubmission['id'], $this->course->id, MUST_EXIST);

                $portfolioswithusersubmissions[$pkey]['coursemoduleid'] = $cm->id;
                $portfolioswithusersubmissions[$pkey]['chapterid'] = $chapter->id;
                $portfolioswithusersubmissions[$pkey]['portfolioid'] = $portfolioswithusersubmission['id' ];
            }

            $chapters[$key]->portfolios = $portfolioswithusersubmissions;
        }

        return [
            'chapters' => $chapters
        ];
    }
}
