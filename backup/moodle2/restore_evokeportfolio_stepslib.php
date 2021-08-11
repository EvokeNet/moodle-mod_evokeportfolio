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
        $paths[] = new restore_path_element('evokeportfolio_section', '/activity/evokeportfolio/sections/section');
        if ($userinfo) {
            $paths[] = new restore_path_element('evokeportfolio_submission', '/activity/evokeportfolio/sections/section/submissions/submission');
            $paths[] = new restore_path_element('evokeportfolio_grade', '/activity/evokeportfolio/grades/grade');
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

    protected function process_evokeportfolio_section($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->portfolioid = $this->get_new_parentid('evokeportfolio');

        $newitemid = $DB->insert_record('evokeportfolio_sections', $data);

        $this->set_mapping('evokeportfolio_section', $oldid, $newitemid);
    }

    protected function process_evokeportfolio_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->groupid = $this->get_mappingid('group', $data->groupid);
        $data->postedby = $this->get_mappingid('user', $data->postedby);
        $data->sectionid = $this->get_new_parentid('evokeportfolio_section');

        $newitemid = $DB->insert_record('evokeportfolio_submissions', $data);

        $this->set_mapping('evokeportfolio_submission', $oldid, $newitemid, true);

        $this->add_related_files('mod_evokeportfolio', 'attachments', 'evokeportfolio_submission', null, $oldid);
    }

    protected function process_evokeportfolio_grade($data) {
        global $DB;

        $data = (object)$data;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->grader = $this->get_mappingid('user', $data->grader);
        $data->portfolioid = $this->get_new_parentid('evokeportfolio');

        $DB->insert_record('evokeportfolio_grades', $data);
    }

    protected function after_execute() {
        global $DB;

        // Fixes userid and groupid assigned with 0 to null.
        $sql = 'SELECT su.* FROM {evokeportfolio_submissions} su
                INNER JOIN {evokeportfolio_sections} se ON se.id = su.sectionid
                WHERE se.portfolioid = :portfolioid AND (su.userid = 0 OR su.groupid = 0)';

        $records = $DB->get_records_sql($sql, ['portfolioid' => $this->get_new_parentid('evokeportfolio')]);

        if (!$records) {
            return;
        }

        foreach ($records as $record) {
            if ($record->userid == 0) {
                $record->userid = null;
            }

            if ($record->groupid == 0) {
                $record->groupid = null;
            }

            $DB->update_record('evokeportfolio_submissions', $record);
        }
    }
}
