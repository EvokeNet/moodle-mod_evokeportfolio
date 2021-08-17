<?php

namespace mod_evokeportfolio\forms;

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\grade;
use mod_evokeportfolio\util\groups;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/formslib.php');

/**
 * Grade form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class grade_form extends \moodleform {

    private $userid = null;
    private $groupid = null;

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

        if (isset($this->_customdata['groupid'])) {
            $this->groupid = $this->_customdata['groupid'];

            $mform->addElement('hidden', 'groupid', $this->_customdata['groupid']);
            $mform->setType('groupid', PARAM_INT);
        }

        $evokeportfolioutil = new evokeportfolio();
        $evokeportfolio = $evokeportfolioutil->get_instance($this->_customdata['instanceid']);

        $this->get_grade_form_fields($mform, $evokeportfolio);

        $this->add_action_buttons(true);
    }

    private function get_grade_form_fields($mform, $evokeportfolio) {
        if (!$evokeportfolio->groupactivity) {
            $this->fill_form_with_individual_grade_fields($mform, $evokeportfolio);

            return;
        }

        if ($evokeportfolio->groupactivity) {
            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_GROUP) {
                $this->fill_form_with_individual_grade_fields($mform, $evokeportfolio);

                return;
            }

            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
                $this->fill_form_with_group_grade_fields($mform, $evokeportfolio);

                return;
            }
        }
    }

    private function fill_form_with_individual_grade_fields($mform, $evokeportfolio) {
        $usergradegrade = false;
        if (!$evokeportfolio->groupactivity) {
            $usergradegrade = $this->get_user_grade($evokeportfolio, $this->userid);
        }

        if ($evokeportfolio->groupactivity && $evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_GROUP) {
            $usergradegrade = $this->get_group_grade($evokeportfolio, $this->groupid);
        }

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

    private function fill_form_with_group_grade_fields($mform, $evokeportfolio) {
        $groupsutil = new groups();
        $groupmembers = $groupsutil->get_group_members($this->groupid);

        if (!$groupmembers) {
            return;
        }

        foreach ($groupmembers as $user) {
            $gradeelementid = 'gradeuserid-' . $user->id;
            $gradeelementlabel = get_string('gradefor', 'mod_evokeportfolio', fullname($user));

            $usergradegrade = $this->get_user_grade($evokeportfolio, $user->id);

            if ($evokeportfolio->grade > 0) {
                $mform->addElement('text', $gradeelementid, $gradeelementlabel);
                $mform->addHelpButton($gradeelementid, 'grade', 'mod_evokeportfolio');
                $mform->addRule($gradeelementid, get_string('onlynumbers', 'mod_evokeportfolio'), 'numeric', null, 'client');
//                $mform->addRule($gradeelementid, get_string('required'), 'required', null, 'client');

                $mform->setType($gradeelementid, PARAM_RAW);

                if ($usergradegrade) {
                    $mform->setDefault($gradeelementid, $usergradegrade);
                }
            }

            if ($evokeportfolio->grade < 0) {
                $grademenu = array(-1 => get_string("nograde")) + make_grades_menu($evokeportfolio->grade);

                $mform->addElement('select', $gradeelementid, $gradeelementlabel, $grademenu);
                $mform->addRule($gradeelementid, get_string('required'), 'required', null, 'client');
                $mform->setType($gradeelementid, PARAM_INT);

                if ($usergradegrade) {
                    $mform->setDefault($gradeelementid, $usergradegrade);
                }
            }
        }
    }

    private function get_user_grade($evokeportfolio, $userid) {
        $gradeutil = new grade();
        $usergrade = $gradeutil->get_user_grade($evokeportfolio, $userid);

        return $this->process_grade($evokeportfolio->grade, $usergrade);
    }

    private function get_group_grade($evokeportfolio, $groupid) {
        $gradeutil = new grade();
        $groupgrade = $gradeutil->get_group_grade($evokeportfolio, $groupid);

        return $this->process_grade($evokeportfolio->grade, $groupgrade);
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

        $evokeportfolioutil = new evokeportfolio();
        $evokeportfolio = $evokeportfolioutil->get_instance($this->_customdata['instanceid']);

        if (!$evokeportfolio->groupactivity && empty($data['grade'])) {
            $errors['grade'] = get_string('validation:graderequired', 'mod_evokeportfolio');
        }

        if ($evokeportfolio->groupactivity) {
            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_GROUP && empty($data['grade'])) {
                $errors['grade'] = get_string('validation:graderequired', 'mod_evokeportfolio');
            }

            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
                unset($data['groupid']);

                foreach ($data as $key => $usergrade) {
                    $userid = substr(strrchr($key, "gradeuserid-"), 12);

                    if (!$userid) {
                        continue;
                    }

                    if (empty($usergrade)) {
                        $errors[$key] = get_string('validation:graderequired', 'mod_evokeportfolio');
                    }
                }
            }
        }

        return $errors;
    }
}
