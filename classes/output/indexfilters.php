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
    protected $portfoliosdata;

    public function __construct($courseid, $chaptersdata = null, $portfoliosdata = null) {
        $this->courseid = $courseid;
        $this->chaptersdata = $chaptersdata;
        $this->portfoliosdata = $portfoliosdata;
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

        if (isset($this->portfoliosdata['currentportfolioid'])) {
            $data['portfolioid'] = $this->portfoliosdata['currentportfolioid'];
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

        $portfolios = [];
        if ($this->portfoliosdata['portfolios']) {
            foreach ($this->portfoliosdata['portfolios'] as $portfolio) {
                $portfolio->selected = false;

                if (isset($this->portfoliosdata['currentportfolioid']) && $portfolio->id == $this->portfoliosdata['currentportfolioid']) {
                    $portfolio->selected = true;
                }

                $portfolios[] = $portfolio;
            }
        }

        $data['portfolios'] = $portfolios;

        return $data;
    }
}
