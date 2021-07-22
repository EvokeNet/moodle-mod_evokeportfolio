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

    public function has_submission($cmid, $userid = false, $groupid = null) {
        global $DB;

        if ($groupid) {
            $entries = $DB->count_records('evokeportfolio_entries', ['cmid' => $cmid, 'groupid' => $groupid]);

            if ($entries) {
                return true;
            }

            return false;
        }

        if ($userid) {
            $entries = $DB->count_records('evokeportfolio_entries', ['cmid' => $cmid, 'userid' => $userid]);

            if ($entries) {
                return true;
            }
        }

        return false;
    }

    public function get_submissions($cmid, $userid = null, $groupid = null) {
        global $DB;

        $sql = 'SELECT
                    e.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_entries} e
                INNER JOIN {user} u ON u.id = e.postedby
                WHERE e.cmid = :cmid';

        if ($groupid) {
            $sql .= ' AND e.groupid = :groupid ORDER BY e.id desc';
            $entries = $DB->get_records_sql($sql, ['cmid' => $cmid, 'groupid' => $groupid]);

            if ($entries) {
                return $this->populate_data_with_user_info($entries);
            }

            return false;
        }

        if ($userid) {
            $sql .= ' AND e.userid = :userid ORDER BY e.id desc';
            $entries = $DB->get_records_sql($sql, ['cmid' => $cmid, 'userid' => $userid]);

            if ($entries) {
                return $this->populate_data_with_user_info($entries);
            }
        }

        return false;
    }

    private function populate_data_with_user_info($data) {
        global $PAGE;

        foreach ($data as $key => $entry) {
            $user = $entry;
            $user->id = $entry->uid;

            $userpicture = new \user_picture($user);

            $data[$key]->userpicture = $userpicture->get_url($PAGE)->out();

            $data[$key]->fullname = fullname($user);

            $data[$key]->isteacher = false;
            if ($entry->role == MOD_EVOKEPORTFOLIO_ROLE_TEACHER) {
                $data[$key]->isteacher = true;
            }
        }

        return array_values($data);
    }
}
