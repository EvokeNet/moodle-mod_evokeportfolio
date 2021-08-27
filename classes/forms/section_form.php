<?php

namespace mod_evokeportfolio\forms;

use mod_evokeportfolio\util\evokeportfolio;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating a section
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class section_form extends \moodleform {

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customodata = null) {
        parent::__construct(null, $customodata, 'post',  '', ['class' => 'evokeportfolio-section-form'], true, $formdata);

        $this->set_display_vertical();
    }

    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        $mform = $this->_form;

        $id = !(empty($this->_customdata['id'])) ? $this->_customdata['id'] : null;
        $name = !(empty($this->_customdata['name'])) ? $this->_customdata['name'] : null;
        $dependentsections = !(empty($this->_customdata['dependentsections'])) ? $this->_customdata['dependentsections'] : null;

        if (!empty($this->_customdata['portfolioid'])) {
            $mform->addElement('hidden', 'portfolioid', $this->_customdata['portfolioid']);
        }

        $mform->addElement('hidden', 'id', $id);

        $mform->addElement('text', 'name', get_string('name', 'mod_evokeportfolio'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $portfolioutil = new evokeportfolio();

        $sections = $portfolioutil->get_course_sections($this->_customdata['portfolioid'], $this->_customdata['id']);
        $mform->addElement('select', 'dependentsections', get_string('dependentcoursesections', 'mod_evokeportfolio'), $sections);
        $mform->getElement('dependentsections')->setMultiple(true);

        if ($dependentsections) {
            $mform->getElement('dependentsections')->setSelected(explode(",", $dependentsections));
        }

        if ($name) {
            $mform->setDefault('name', $name);
        }
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $name = isset($data['name']) ? $data['name'] : null;

        if ($this->is_submitted() && (empty($name) || strlen($name) < 3)) {
            $errors['name'] = get_string('validation:namelen', 'mod_evokeportfolio');
        }

        return $errors;
    }
}
