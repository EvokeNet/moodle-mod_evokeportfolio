<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;

/**
 * Index renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class indexfilters implements renderable, templatable {

    protected $courseid;
    protected $chaptersdata;

    public function __construct($courseid, $chaptersdata = null) {
        $this->courseid = $courseid;
        $this->chaptersdata = $chaptersdata;
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
        $data = [
            'courseid' => $this->courseid
        ];

        if (isset($this->chaptersdata['currentchapterid'])) {
            $data['chapterid'] = $this->chaptersdata['currentchapterid'];
        }

        $chapters = [];
        if ($this->chaptersdata['chapters']) {
            foreach ($this->chaptersdata['chapters'] as $chapter) {
                $chapter->selected = false;

                if ($chapter->id == $this->chaptersdata['currentchapterid']) {
                    $chapter->selected = true;
                }

                $chapters[] = $chapter;
            }
        }

        $data['chapters'] = $chapters;

        return $data;
    }
}
