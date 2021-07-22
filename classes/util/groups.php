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

class groups {
    public function get_user_group($courseid, $userid = null) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $sql = "SELECT g.id, g.name
                FROM {groups} g
                JOIN {groups_members} gm ON gm.groupid = g.id
                WHERE gm.userid = :userid AND g.courseid = :courseid
                LIMIT 1";

        $usergroup = $DB->get_record_sql($sql, ['courseid' => $courseid, 'userid' => $userid]);

        if (!$usergroup) {
            return false;
        }

        return $usergroup;
    }

    public function get_group_members($groupid) {
        global $DB, $PAGE;

        $sql = "SELECT u.*
                FROM {groups_members} gm
                INNER JOIN {user} u ON u.id = gm.userid
                WHERE gm.groupid = :groupid";

        $groupmembers = $DB->get_records_sql($sql, ['groupid' => $groupid]);

        if (!$groupmembers) {
            return false;
        }

        foreach ($groupmembers as $key => $groupmember) {
            $userpicture = new \user_picture($groupmember);

            $groupmembers[$key]->userpicture = $userpicture->get_url($PAGE)->out();

            $groupmembers[$key]->fullname = fullname($groupmember);
        }

        return array_values($groupmembers);
    }

    public function get_total_groups_in_course($courseid) {
        global $DB;

        return $DB->count_records('groups', ['courseid' => $courseid]);
    }

    public function is_group_member($groupid, $userid) {
        global $DB;

        return $DB->count_records('groups_members', ['groupid' => $groupid, 'userid' => $userid]);
    }
}