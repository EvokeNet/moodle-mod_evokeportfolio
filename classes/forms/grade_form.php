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

namespace mod_evokeportfolio\forms;

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\groups;

/**
 * The main mod_evokeportfolio submit form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/formslib.php');

/**
 * Portfolio comment form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_form extends \moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        if (isset($this->_customdata['userid'])) {
            $mform->addElement('hidden', 'userid', $this->_customdata['userid']);
            $mform->setType('userid', PARAM_INT);
        }

        if (isset($this->_customdata['groupid'])) {
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
            $this->fill_form_with_individual_grade_fields($mform, $evokeportfolio->grade);

            return;
        }

        if ($evokeportfolio->groupactivity) {
            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_GROUP) {
                $this->fill_form_with_individual_grade_fields($mform, $evokeportfolio->grade);

                return;
            }

            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
                $this->fill_form_with_group_grade_fields($mform, $evokeportfolio->grade);

                return;
            }
        }
    }

    private function fill_form_with_individual_grade_fields($mform, $grade) {
        if ($grade > 0) {
            $mform->addElement('text', 'grade', get_string('grade', 'mod_evokeportfolio'));
            $mform->addHelpButton('grade', 'grade', 'mod_evokeportfolio');
            $mform->addRule('grade', get_string('onlynumbers', 'mod_evokeportfolio'), 'numeric', null, 'client');
            $mform->addRule('grade', get_string('required'), 'required', null, 'client');
            $mform->setType('grade', PARAM_RAW);
        }

        if ($grade < 0) {
            $grademenu = array(-1 => get_string("nograde")) + make_grades_menu($grade);

            $mform->addElement('select', 'grade', get_string('gradenoun') . ':', $grademenu);
            $mform->setType('grade', PARAM_INT);
            $mform->addRule('grade', get_string('required'), 'required', null, 'client');
        }
    }

    private function fill_form_with_group_grade_fields($mform, $grade) {
        $groupsutil = new groups();
        $groupmembers = $groupsutil->get_group_members($this->_customdata['groupid']);

        if (!$groupmembers) {
            return;
        }

        foreach ($groupmembers as $user) {
            $gradeelementid = 'gradeuserid-' . $user->id;
            $gradeelementlabel = get_string('gradefor', 'mod_evokeportfolio', fullname($user));

            if ($grade > 0) {
                $mform->addElement('text', $gradeelementid, $gradeelementlabel);
                $mform->addHelpButton($gradeelementid, 'grade', 'mod_evokeportfolio');
                $mform->addRule($gradeelementid, get_string('onlynumbers', 'mod_evokeportfolio'), 'numeric', null, 'client');
                $mform->addRule($gradeelementid, get_string('required'), 'required', null, 'client');

                $mform->setType($gradeelementid, PARAM_RAW);
            }

            if ($grade < 0) {
                $grademenu = array(-1 => get_string("nograde")) + make_grades_menu($grade);

                $mform->addElement('select', $gradeelementid, $gradeelementlabel, $grademenu);
                $mform->addRule($gradeelementid, get_string('required'), 'required', null, 'client');
                $mform->setType($gradeelementid, PARAM_INT);
            }
        }


    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // TODO: To validate.

        return $errors;
    }
}
