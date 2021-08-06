<?php
// This file is part of BBCalendar block for Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

class evokeportfolio {
    public function get_instance($id) {
        global $DB;

        return $DB->get_record('evokeportfolio', ['id' => $id], '*', MUST_EXIST);
    }

    public function has_submission($portfolioid, $userid = false, $groupid = null) {
        global $DB;

        if ($groupid) {
            $entries = $DB->count_records('evokeportfolio_submissions', ['portfolioid' => $portfolioid, 'groupid' => $groupid]);

            if ($entries) {
                return true;
            }

            return false;
        }

        if ($userid) {
            $entries = $DB->count_records('evokeportfolio_submissions', ['portfolioid' => $portfolioid, 'userid' => $userid]);

            if ($entries) {
                return true;
            }
        }

        return false;
    }

    public function get_submissions($context, $portfolioid, $userid = null, $groupid = null) {
        global $DB;

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.postedby
                WHERE es.portfolioid = :portfolioid';

        if ($groupid) {
            $sql .= ' AND es.groupid = :groupid ORDER BY es.id desc';
            $entries = $DB->get_records_sql($sql, ['portfolioid' => $portfolioid, 'groupid' => $groupid]);

            if (!$entries) {
                return false;
            }

            $this->populate_data_with_user_info($entries);

            $this->populate_data_with_attachments($entries, $context);

            return array_values($entries);
        }

        if ($userid) {
            $sql .= ' AND es.userid = :userid ORDER BY es.id desc';
            $entries = $DB->get_records_sql($sql, ['portfolioid' => $portfolioid, 'userid' => $userid]);

            if (!$entries) {
                return false;
            }

            $this->populate_data_with_user_info($entries);

            $this->populate_data_with_attachments($entries, $context);

            return array_values($entries);
        }

        return false;
    }

    private function populate_data_with_user_info($data) {
        global $PAGE, $USER;

        foreach ($data as $key => $entry) {
            $user = clone($entry);
            $user->id = $entry->uid;

            $userpicture = new \user_picture($user);

            $data[$key]->userpicture = $userpicture->get_url($PAGE)->out();

            $data[$key]->fullname = fullname($user);

            $data[$key]->isteacher = false;
            if ($entry->role == MOD_EVOKEPORTFOLIO_ROLE_TEACHER) {
                $data[$key]->isteacher = true;
            }

            $data[$key]->isowner = $user->id == $USER->id;
        }

        return $data;
    }

    private function populate_data_with_attachments($data, $context) {

        $fs = get_file_storage();

        foreach ($data as $key => $entry) {
            $files = $fs->get_area_files($context->id,
                'mod_evokeportfolio',
                'attachments',
                $entry->id,
                'timemodified',
                false);

            $data[$key]->hasattachments = false;

            if ($files) {
                $entryfiles = [];

                foreach ($files as $file) {
                    $path = [
                        '',
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $entry->id .$file->get_filepath() . $file->get_filename()
                    ];

                    $fileurl = \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);

                    $entryfiles[] = [
                        'filename' => $file->get_filename(),
                        'isimage' => $file->is_valid_image(),
                        'fileurl' => $fileurl
                    ];
                }

                $data[$key]->attachments = $entryfiles;
                $data[$key]->hasattachments = true;
            }
        }
    }
}
