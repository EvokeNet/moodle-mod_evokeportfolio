<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * All the steps to restore mod_evokeportfolio are defined here.
 *
 * @package     mod_evokeportfolio
 * @category    backup
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Defines the structure step to restore one mod_evokeportfolio activity.
 */
class restore_evokeportfolio_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('evokeportfolio', '/activity/evokeportfolio');
        if ($userinfo) {
            $paths[] = new restore_path_element('evokeportfolio_submissions', '/activity/evokeportfolio/submissions/submission');
            $paths[] = new restore_path_element('evokeportfolio_grades', '/activity/evokeportfolio/grades/grade');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes the elt restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_evokeportfolio($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('evokeportfolio', $data);

        $this->apply_activity_instance($newitemid);
    }

    protected function process_evokeportfolio_submissions($data) {
        global $DB;

        $data = (object)$data;

        $data->portfolioid = $this->get_new_parentid('evokeportfolio');

        $DB->insert_record('evokeportfolio_submissions', $data);
    }

    protected function process_evokeportfolio_grades($data) {
        global $DB;

        $data = (object)$data;

        $data->portfolioid = $this->get_new_parentid('evokeportfolio');

        $DB->insert_record('evokeportfolio_grades', $data);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        return;
    }
}
