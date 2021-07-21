<?php
// This file is part of BBCalendar block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\table\entries as entriestable;
use renderable;
use templatable;
use renderer_base;

/**
 * Competency Self Assessment renderable class.
 *
 * @copyright  2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
