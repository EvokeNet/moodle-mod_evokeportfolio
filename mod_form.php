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

        $mform->addElement('date_time_selector', 'datelimit', get_string('datelimit', 'mod_evokeportfolio'));
        $mform->addHelpButton('datelimit', 'datelimit', 'mod_evokeportfolio');
        $mform->addRule('datelimit', null, 'required', null, 'client');

        $mform->addElement('selectyesno', 'groupactivity', get_string('groupactivity', 'mod_evokeportfolio'), get_string('groupactivity', 'mod_evokeportfolio'));
        $mform->setDefault('groupactivity', 0);

        $options = [
            MOD_EVOKEPORTFOLIO_GRADING_GROUP => 'Group grading',
            MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL => 'Individual grading'
        ];
        $mform->addElement('select', 'groupgradingmode', get_string('groupgradingmode', 'mod_evokeportfolio'), $options);
        $mform->addHelpButton('groupgradingmode', 'groupgradingmode', 'mod_evokeportfolio');
        $mform->setDefault('groupgradingmode', 1);
        $mform->hideIf('groupgradingmode', 'groupactivity', 'eq', 0);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
