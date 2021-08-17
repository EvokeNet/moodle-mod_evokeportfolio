<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use renderable;
use templatable;
use renderer_base;

/**
 * Manage sections renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class managesections implements renderable, templatable {

    public $evokeportfolio;
    public $context;

    public function __construct($evokeportfolio, $context) {
        $this->evokeportfolio = $evokeportfolio;
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
        $evokeportfolioutil = new evokeportfolio();
        $sections = $evokeportfolioutil->get_sections($this->evokeportfolio->id);

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'contextid' => $this->context->id,
            'sections' => $sections
        ];

        return $data;
    }
}
