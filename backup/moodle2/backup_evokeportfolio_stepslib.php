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
            'groupactivity', 'groupgradingmode', 'timecreated', 'timemodified']);

        $sections = new backup_nested_element('sections');
        $section = new backup_nested_element('section', ['id'], [
            'name', 'timecreated', 'timemodified']);

        $submissions = new backup_nested_element('submissions');
        $submission = new backup_nested_element('submission', ['id'], [
            'userid', 'groupid', 'postedby', 'role', 'comment', 'commentformat',
            'timecreated', 'timemodified']);

        $grades = new backup_nested_element('grades');
        $grade = new backup_nested_element('grade', ['id'], [
            'userid', 'grader', 'grade', 'timecreated', 'timemodified']);

        $evokeportfolio->add_child($sections);
        $sections->add_child($section);
        $section->add_child($submissions);
        $submissions->add_child($submission);
        $evokeportfolio->add_child($grades);
        $grades->add_child($grade);

        // Define the source tables for the elements.
        $evokeportfolio->set_source_table('evokeportfolio', ['id' => backup::VAR_ACTIVITYID]);
        $section->set_source_table('evokeportfolio_sections', ['portfolioid' => backup::VAR_ACTIVITYID]);

        // User views are included only if we are including user info.
        if ($userinfo) {
            // Define sources.
            $submission->set_source_table('evokeportfolio_submissions', ['sectionid' => backup::VAR_PARENTID]);
            $grade->set_source_table('evokeportfolio_grades', ['portfolioid' => backup::VAR_ACTIVITYID]);
        }

        $submission->annotate_ids('user', 'userid');
        $submission->annotate_ids('group', 'groupid');
        $submission->annotate_ids('user', 'postedby');

        $grade->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'grader');

        // Define file annotations.
        $submission->annotate_files('mod_evokeportfolio', 'attachments', 'id');

        return $this->prepare_activity_structure($evokeportfolio);
    }
}
