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
class gradeuserchapter_form extends \moodleform {

    private $userid = null;
    private $chapterid = null;

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customodata = null) {
        parent::__construct(null, $customodata, 'post',  '', ['class' => 'evokeportfolio-gradeuserchapter-form'], true, $formdata);

        $this->set_display_vertical();
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        if (isset($this->_customdata['userid'])) {
            $this->userid = $this->_customdata['userid'];

            $mform->addElement('hidden', 'userid', $this->_customdata['userid']);
            $mform->setType('userid', PARAM_INT);
        }

        if (isset($this->_customdata['chapterid'])) {
            $this->chapterid = $this->_customdata['chapterid'];

            $mform->addElement('hidden', 'chapterid', $this->_customdata['chapterid']);
            $mform->setType('chapterid', PARAM_INT);
        }

        // TODO: verificar formas de avaliação.
        $mform->addElement('text', 'grade', get_string('grade', 'mod_evokeportfolio'));
        $mform->addHelpButton('grade', 'grade', 'mod_evokeportfolio');
        $mform->addRule('grade', get_string('onlynumbers', 'mod_evokeportfolio'), 'numeric', null, 'client');
        $mform->addRule('grade', get_string('required'), 'required', null, 'client');
        $mform->setType('grade', PARAM_RAW);

        $gradeutil = new grade();

        $dbusergrade = $gradeutil->get_user_chapter_grade($this->userid, $this->chapterid);

        if ($dbusergrade) {
            $mform->setDefault('grade', $dbusergrade->grade);
        }

        $this->add_action_buttons(true);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($this->is_submitted() && empty($data['grade'])) {
            $errors['grade'] = get_string('validation:graderequired', 'mod_evokeportfolio');
        }

        return $errors;
    }
}
