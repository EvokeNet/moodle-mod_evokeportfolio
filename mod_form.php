<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class mod_evokeportfolio_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name', 'mod_evokeportfolio'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $yesnooptions = [0 => get_string('no'), 1 => get_string('yes')];

        $mform->addElement('select', 'evokation', get_string('evokation', 'mod_evokeportfolio'), $yesnooptions);

        $mform->addElement('select', 'groupactivity', get_string('groupactivity', 'mod_evokeportfolio'), $yesnooptions);
        $mform->hideIf('groupactivity', 'evokation', 'eq', 1);

        $mform->addElement('date_time_selector', 'datestart', get_string('datestart', 'mod_evokeportfolio'));
        $mform->addHelpButton('datestart', 'datestart', 'mod_evokeportfolio');
        $mform->addRule('datestart', null, 'required', null, 'client');

        $mform->addElement('date_time_selector', 'datelimit', get_string('datelimit', 'mod_evokeportfolio'));
        $mform->addHelpButton('datelimit', 'datelimit', 'mod_evokeportfolio');
        $mform->addRule('datelimit', null, 'required', null, 'client');

        if ($this->current->submissionsuccessmessage) {
            $this->current->submissionsuccessmessage = array('text' => $this->current->submissionsuccessmessage, 'format' => $this->current->submissionsuccessmessageformat);
        }

        $options = [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'context' => null,
            'noclean' => 0,
            'trusttext' => 0,
            'enable_filemanagement' => false
        ];
        $mform->addElement('editor', 'submissionsuccessmessage', get_string('submissionsuccessmessage', 'mod_evokeportfolio'), $options);
        $mform->setType('submissionsuccessmessage', PARAM_RAW);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);

        if ($data->evokation) {
            $data->groupactivity = 0;
        }

        if (!empty($data->completionunlocked)) {
            // Turn off completion settings if the checkboxes aren't ticked.
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;

            if (!$autocompletion || empty($data->completionrequiresubmit)) {
                $data->completionrequiresubmit = 0;
            }
        }
    }

    /**
     * Add elements for setting the custom completion rules.
     *
     * @category completion
     * @return array List of added element names, or names of wrapping group elements.
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $mform->addElement('checkbox', 'completionrequiresubmit', get_string('completionrequiresubmit', 'mod_evokeportfolio'), get_string('completionrequiresubmit_help', 'mod_evokeportfolio'));
        $mform->setDefault('completionrequiresubmit', 1);

        return ['completionrequiresubmit'];
    }

    /**
     * Called during validation to see whether some module-specific completion rules are selected.
     *
     * @param array $data Input data not yet validated.
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionrequiresubmit']);
    }
}
