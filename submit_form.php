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
class mod_evokeportfolio_submit_form extends moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

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
        $mform->setType('comment', PARAM_RAW);

        $mform->addElement('filemanager', 'attachments', get_string('page_submit_attachments', 'mod_evokeportfolio'), null,
            array('subdirs' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
                'accepted_types' => ['document', 'image'], 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
    }
}
