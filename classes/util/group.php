<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Groups utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class group {
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
        global $DB;

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
                $userpicture = theme_evoke_get_user_avatar_or_image($groupmember);

                $groupmembers[$key]->userpicture = $userpicture;

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

    public function get_course_groups($course, $withimage = true) {
        global $DB;

        $groups = $DB->get_records('groups', ['courseid' => $course->id]);

        if (!$groups) {
            return false;
        }

        if ($withimage) {
            foreach ($groups as $group) {
                $group->groupimage = $this->get_group_image($group);
            }
        }

        return array_values($groups);
    }

    public function get_group_image($group) {
        global $CFG;

        $pictureurl = get_group_picture_url($group, $group->courseid, true);

        if ($pictureurl) {
            return $pictureurl->out();
        }

        return $CFG->wwwroot . '/blocks/evokehq/pix/defaultgroupimg.png';
    }
}