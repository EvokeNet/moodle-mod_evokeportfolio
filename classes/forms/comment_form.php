<?php

namespace mod_evokeportfolio\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/formslib.php');

/**
 * Portfolio comment form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class comment_form extends \moodleform {

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

        $options = [
            'subdirs'=> 0,
            'maxbytes'=> 0,
            'maxfiles'=> 0,
            'changeformat'=> 0,
            'context'=> null,
            'noclean'=> 0,
            'trusttext'=> 0,
            'enable_filemanagement' => false
        ];

        $mform->addElement('editor', 'comment', get_string('page_submit_comment', 'mod_evokeportfolio', $options));
        $mform->setType('comment', PARAM_CLEANHTML);

        $this->add_action_buttons(true);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['comment'] && empty($data['comment']['text'])) {
            $errors['comment'] = get_string('validation:commentrequired', 'mod_evokeportfolio');
        }

        if ($data['comment'] && !empty($data['comment']['text']) && mb_strlen(strip_tags($data['comment']['text'])) < 10) {
            $errors['comment'] = get_string('validation:commentlen', 'mod_evokeportfolio');
        }

        return $errors;
    }
}
