<?php

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * All the steps to restore mod_evokeportfolio are defined here.
 *
 * @package     mod_evokeportfolio
 * @category    backup
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
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
            $paths[] = new restore_path_element('evokeportfolio_submission', '/activity/evokeportfolio/submissions/submission');
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

    protected function process_evokeportfolio_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->portfolioid = $this->get_new_parentid('evokeportfolio');

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
        $this->add_related_files('mod_evokeportfolio', 'intro', null);
    }
}
