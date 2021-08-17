<?php

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

require_once($CFG->dirroot.'//mod/evokeportfolio/backup/moodle2/restore_evokeportfolio_stepslib.php');

/**
 * The task that provides a complete restore of mod_evokeportfolio is defined here.
 *
 * @package     mod_evokeportfolio
 * @category    backup
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class restore_evokeportfolio_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings() {
        return;
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return base_step.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_evokeportfolio_activity_structure_step('evokeportfolio_structure', 'evokeportfolio.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('evokeportfolio', ['intro'], 'evokeportfolio');
        $contents[] = new restore_decode_content('evokeportfolio_submissions', ['comment'], 'submission');

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('EVOKEPORTFOLIOVIEWBYID', '/mod/evokeportfolio/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('EVOKEPORTFOLIOINDEX', '/mod/evokeportfolio/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied by the
     * {@see restore_logs_processor} when restoring mod_evokeportfolio logs. It
     * must return one array of {@see restore_log_rule} objects.
     *
     * @return array.
     */
    public static function define_restore_log_rules() {
        $rules = array();

        // Define the rules.

        return $rules;
    }
}
