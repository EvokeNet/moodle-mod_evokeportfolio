<?php

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Backup steps for mod_evokeportfolio are defined here.
 *
 * @package     mod_evokeportfolio
 * @category    backup
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class backup_evokeportfolio_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Build the tree with these elements with $evokeportfolio as the root of the backup tree.
        $evokeportfolio = new backup_nested_element('evokeportfolio', ['id'], [
            'course', 'name', 'intro', 'introformat', 'grade', 'datelimit',
            'timecreated', 'timemodified']);

        $submissions = new backup_nested_element('submissions');
        $submission = new backup_nested_element('submission', ['id'], [
            'userid', 'comment', 'commentformat', 'timecreated', 'timemodified']);

        $grades = new backup_nested_element('grades');
        $grade = new backup_nested_element('grade', ['id'], [
            'userid', 'grader', 'grade', 'timecreated', 'timemodified']);

        $evokeportfolio->add_child($submissions);
        $submissions->add_child($submission);
        $evokeportfolio->add_child($grades);
        $grades->add_child($grade);

        // Define the source tables for the elements.
        $evokeportfolio->set_source_table('evokeportfolio', ['id' => backup::VAR_ACTIVITYID]);

        // User views are included only if we are including user info.
        if ($userinfo) {
            // Define sources.
            $submission->set_source_table('evokeportfolio_submissions', ['portfolioid' => backup::VAR_ACTIVITYID]);
            $grade->set_source_table('evokeportfolio_grades', ['portfolioid' => backup::VAR_ACTIVITYID]);
        }

        $submission->annotate_ids('user', 'userid');

        $grade->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'grader');

        // Define file annotations.
        $submission->annotate_files('mod_evokeportfolio', 'attachments', 'id');

        $evokeportfolio->annotate_files('mod_evokeportfolio', 'intro', null);

        return $this->prepare_activity_structure($evokeportfolio);
    }
}
