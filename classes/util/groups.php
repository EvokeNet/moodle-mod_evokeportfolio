<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Groups utility class helper
 *
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
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

    public function get_group_members($groupid, $withfulluserinfo = true) {
        global $DB, $PAGE;

        $sql = "SELECT u.*
                FROM {groups_members} gm
                INNER JOIN {user} u ON u.id = gm.userid
                WHERE gm.groupid = :groupid";

        $groupmembers = $DB->get_records_sql($sql, ['groupid' => $groupid]);

        if (!$groupmembers) {
            return false;
        }

        if ($withfulluserinfo) {
            foreach ($groupmembers as $key => $groupmember) {
                $userpicture = new \user_picture($groupmember);

                $groupmembers[$key]->userpicture = $userpicture->get_url($PAGE)->out();

                $groupmembers[$key]->fullname = fullname($groupmember);
            }
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