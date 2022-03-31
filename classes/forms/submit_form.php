<?php

namespace mod_evokeportfolio\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/formslib.php');

/**
 * Portfolio entry form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class submit_form extends \moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        if (isset($this->_customdata['submissionid'])) {
            $mform->addElement('hidden', 'submissionid', $this->_customdata['submissionid']);
            $mform->setType('submissionid', PARAM_INT);
        }

        if (isset($this->_customdata['userid'])) {
            $mform->addElement('hidden', 'userid', $this->_customdata['userid']);
            $mform->setType('userid', PARAM_INT);
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

        $mform->addElement('filemanager', 'attachments', get_string('page_submit_attachments', 'mod_evokeportfolio'), null,
            ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['document', 'presentation', 'image'], 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL]);

        $this->add_action_buttons(true);
    }

    public function definition_after_data() {
        global $DB;

        $mform = $this->_form;

        if (isset($this->_customdata['submissionid'])) {
            $submission = $DB->get_record('evokeportfolio_submissions', ['id' => $this->_customdata['submissionid']], '*', MUST_EXIST);

            $mform->getElement('comment')->setValue([
                'text' => $submission->comment,
                'format' => $submission->commentformat
            ]);

            $cm = get_coursemodule_from_instance('evokeportfolio', $this->_customdata['portfolioid']);

            $context = \context_module::instance($cm->id);
            $draftitemid = file_get_submitted_draft_itemid('attachments');

            file_prepare_draft_area($draftitemid, $context->id, 'mod_evokeportfolio', 'attachments', $submission->id, ['subdirs' => 0, 'maxfiles' => 1]);

            $mform->getElement('attachments')->setValue($draftitemid);
        }
    }

    public function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);

        $usercontext = \context_user::instance($USER->id);

        $files = array();
        if(isset($data['attachments'])) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['attachments']);
        }

        if (empty($files) && ($data['comment'] && empty($data['comment']['text']))) {
            $errors['attachments'] = get_string('validation:commentattachmentsrequired', 'mod_evokeportfolio');
            $errors['comment'] = get_string('validation:commentattachmentsrequired', 'mod_evokeportfolio');
        }

        if ($data['comment'] && !empty($data['comment']['text']) && mb_strlen(strip_tags($data['comment']['text'])) < 10) {
            $errors['comment'] = get_string('validation:commentlen', 'mod_evokeportfolio');
        }

        return $errors;
    }
}
