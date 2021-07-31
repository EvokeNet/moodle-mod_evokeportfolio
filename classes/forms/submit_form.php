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
 * Portfolio entry form.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

        $mform->addElement('filemanager', 'attachments', get_string('page_submit_attachments', 'mod_evokeportfolio'), null,
            ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['document', 'image'], 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL]);

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

            $context = \context_module::instance($submission->cmid);
            $draftitemid = file_get_submitted_draft_itemid('attachments');

            file_prepare_draft_area($draftitemid, $context->id, 'mod_evokeportfolio', 'attachments', $submission->id, ['subdirs' => 0, 'maxfiles' => 1]);

            $mform->getElement('attachments')->setValue($draftitemid);
        }
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // TODO: To validate.

        return $errors;
    }
}
