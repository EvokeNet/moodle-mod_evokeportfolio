<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Evoke utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class user {
    public function get_user_group($userid, $courseid) {
        global $DB;

        $sql = 'SELECT g.* FROM {groups_members} gm
                INNER JOIN {groups} g ON g.id = gm.groupid
                WHERE gm.userid = :userid AND g.courseid = :courseid';

        $records = $DB->get_records_sql($sql, ['userid' => $userid, 'courseid' => $courseid]);

        if (!$records) {
            return false;
        }

        return current($records);
    }
}
