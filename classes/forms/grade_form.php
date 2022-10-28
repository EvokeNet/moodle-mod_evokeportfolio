<?php

namespace mod_evokeportfolio\forms;

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\grade;
use mod_evokeportfolio\util\group;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/formslib.php');

/**
 * Grade form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class grade_form extends \moodleform {

    private $userid = null;

    public function __construct($formdata, $customdata = null) {
        parent::__construct(null, $customdata, 'post',  '', ['class' => 'evokeportfolio-grade-form'], true, $formdata);

        $this->set_display_vertical();
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        if (isset($this->_customdata['userid'])) {
            $this->userid = $this->_customdata['userid'];

            $mform->addElement('hidden', 'userid', $this->_customdata['userid']);
            $mform->setType('userid', PARAM_INT);
        }

        if (isset($this->_customdata['instanceid'])) {
            $mform->addElement('hidden', 'instanceid', $this->_customdata['instanceid']);
            $mform->setType('instanceid', PARAM_INT);
        }

        $evokeportfolioutil = new evokeportfolio();
        $evokeportfolio = $evokeportfolioutil->get_instance($this->_customdata['instanceid']);

        $this->fill_form_with_grade_fields($mform, $evokeportfolio);

        $this->add_action_buttons(true);
    }

    private function fill_form_with_grade_fields($mform, $evokeportfolio) {
        $usergradegrade = $this->get_user_grade($evokeportfolio, $this->userid);

        if ($evokeportfolio->grade > 0) {
            $mform->addElement('text', 'grade', get_string('grade', 'mod_evokeportfolio'));
            $mform->addHelpButton('grade', 'grade', 'mod_evokeportfolio');
            $mform->addRule('grade', get_string('onlynumbers', 'mod_evokeportfolio'), 'numeric', null, 'client');
            $mform->addRule('grade', get_string('required'), 'required', null, 'client');
            $mform->setType('grade', PARAM_RAW);

            if ($usergradegrade) {
                $mform->setDefault('grade', $usergradegrade);
            }
        }

        if ($evokeportfolio->grade < 0) {
            $grademenu = array(-1 => get_string("nograde")) + make_grades_menu($evokeportfolio->grade);

            $mform->addElement('select', 'grade', get_string('gradenoun') . ':', $grademenu);
            $mform->setType('grade', PARAM_INT);
            $mform->addRule('grade', get_string('required'), 'required', null, 'client');

            if ($usergradegrade) {
                $mform->setDefault('grade', $usergradegrade);
            }
        }
    }

    private function get_user_grade($evokeportfolio, $userid) {
        $gradeutil = new grade();
        $usergrade = $gradeutil->get_user_grade($evokeportfolio, $userid);

        return $this->process_grade($evokeportfolio->grade, $usergrade);
    }

    private function process_grade($portfoliograde, $grade = null) {
        // Grade in decimals.
        if ($grade && $portfoliograde > 0) {
            return number_format($grade, 1, '.', '');
        }

        // Grade in scale.
        if ($grade && $portfoliograde < 0) {
            return (int) $grade;
        }

        return false;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['grade'])) {
            $errors['grade'] = get_string('validation:graderequired', 'mod_evokeportfolio');
        }

        return $errors;
    }
}
