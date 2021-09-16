<?php

namespace mod_evokeportfolio\forms;

use mod_evokeportfolio\util\evokeportfolio;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating a chapter
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class chapter_form extends \moodleform {

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customodata = null) {
        parent::__construct(null, $customodata, 'post',  '', ['class' => 'evokeportfolio-chapter-form'], true, $formdata);

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
        $portfolios = !(empty($this->_customdata['portfolios'])) ? $this->_customdata['portfolios'] : null;

        if (!empty($this->_customdata['course'])) {
            $mform->addElement('hidden', 'course', $this->_customdata['course']);
        }

        $mform->addElement('hidden', 'id', $id);

        $mform->addElement('text', 'name', get_string('name', 'mod_evokeportfolio'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $portfolioutil = new evokeportfolio();

        $courseportfolios = $portfolioutil->get_unused_course_portfolio_instances_select($this->_customdata['course'], $id);
        $mform->addElement('select', 'portfolios', get_string('chaptersportfolios', 'mod_evokeportfolio'), $courseportfolios);
        $mform->addRule('portfolios', get_string('required'), 'required', null, 'client');
        $mform->getElement('portfolios')->setMultiple(true);

        if ($portfolios) {
            $mform->getElement('portfolios')->setSelected(explode(",", $portfolios));
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
