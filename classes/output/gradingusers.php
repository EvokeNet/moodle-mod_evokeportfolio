<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\table\users as userstable;
use renderable;
use templatable;
use renderer_base;

/**
 * Entries renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class gradingusers implements renderable, templatable {
    protected $context;
    protected $course;
    protected $chapter;

    public function __construct($course, $context, $chapter) {
        $this->context = $context;
        $this->course = $course;
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
        $table = new userstable(
            'mod-evokeportfolio-users-table',
            $this->context,
            $this->chapter
        );

        $table->collapsible(false);

        ob_start();
        $table->out(30, true);
        $participantstable = ob_get_contents();
        ob_end_clean();

        $data = [
            'name' => $this->course->fullname,
            'chaptername' => $this->chapter->name,
            'participantstable' => $participantstable
        ];

        return $data;
    }
}
