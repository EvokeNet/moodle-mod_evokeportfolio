<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

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
class evokation implements renderable, templatable {

    public $course;

    public function __construct($course) {
        $this->course = $course;
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
        global $DB;

        $portfolioutil = new evokeportfolio();
        $evokations = $portfolioutil->get_course_portfolio_instances($this->course->id, 1);

        if ($evokations) {
            foreach ($evokations as $evokation) {
                $sql = 'SELECT * FROM {evokeportfolio_submissions} WHERE portfolioid = :portfolioid ORDER BY timemodified DESC limit 1';

                $parameters = ['portfolioid' => $evokation->id];

                $lastsubmission = $DB->get_record_sql($sql, $parameters);

                if (!$lastsubmission) {
                    $evokation->lastupdated = false;
                    $evokation->totalsubmissions = 0;

                    continue;
                }

                $evokation->lastupdated = format_time( time() - $lastsubmission->timemodified);

                $evokation->totalsubmissions = $DB->count_records('evokeportfolio_submissions', $parameters);
            }
        }

        return [
            'contextid' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'evokations' => $evokations
        ];
    }
}
