<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\table\entries as entriestable;
use renderable;
use templatable;
use renderer_base;

/**
 * Entries renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class entries implements renderable, templatable {

    public $evokeportfolio;
    public $context;
    public $coursemodule;

    public function __construct($evokeportfolio, $context, $coursemodule) {
        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;
        $this->coursemodule = $coursemodule;
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
        $table = new entriestable(
            'mod-evokeportfolio-entries-table',
            $this->context,
            $this->evokeportfolio,
            $this->coursemodule,
        );

        $table->collapsible(false);

        ob_start();
        $table->out(30, true);
        $participantstable = ob_get_contents();
        ob_end_clean();

        $data = [
            'name' => $this->evokeportfolio->name,
            'participantstable' => $participantstable
        ];

        return $data;
    }
}
