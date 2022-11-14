<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

use context_course;
use user_picture;

/**
 * Evoke utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class user {
    /**
     * Get all users enrolled in a course by id
     *
     * @param int $userid
     * @param context_course $context
     *
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function get_by_id($userid, context_course $context) {
        global $DB;

        $ufields = user_picture::fields('u');

        list($esql, $enrolledparams) = get_enrolled_sql($context);

        $sql = "SELECT $ufields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
                WHERE u.id = :userid";

        $params = array_merge($enrolledparams, ['userid' => $userid]);

        return $DB->get_record_sql($sql, $params, MUST_EXIST);
    }

    /**
     * Get all users enrolled in a course by name
     *
     * @param string $name
     * @param \stdClass $course
     * @param context_course $context
     *
     * @return array
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function getall_by_name($name, context_course $context) {
        global $DB;

        list($ufields, $searchparams, $wherecondition) = self::get_basic_search_conditions($name, $context);

        list($esql, $enrolledparams) = get_enrolled_sql($context);

        $sql = "SELECT $ufields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
                WHERE $wherecondition";

        list($sort, $sortparams) = users_order_by_sql('u');
        $sql = "$sql ORDER BY $sort";

        $params = array_merge($searchparams, $enrolledparams, $sortparams);

        $users = $DB->get_records_sql($sql, $params, 0, 10);

        if (!$users) {
            return false;
        }

        return array_values($users);
    }

    /**
     * Helper method used by getall_by_name().
     *
     * @param string $search the search term, if any.
     * @param context_course $context course context
     *
     * @return array with three elements:
     *     string list of fields to SELECT,
     *     array query params. Note that the SQL snippets use named parameters,
     *     string contents of SQL WHERE clause.
     */
    protected static function get_basic_search_conditions($search, context_course $context) {
        global $DB, $CFG, $USER;

        // Add some additional sensible conditions.
        $tests = ["u.id <> :guestid", "u.deleted = 0", "u.confirmed = 1", "u.id <> :loggedinuser"];
        $params = [
            'guestid' => $CFG->siteguest,
            'loggedinuser' => $USER->id
        ];

        if (!empty($search)) {
            $conditions = get_extra_user_fields($context);
            foreach (get_all_user_name_fields() as $field) {
                $conditions[] = 'u.'.$field;
            }

            $conditions[] = $DB->sql_fullname('u.firstname', 'u.lastname');

            $searchparam = '%' . $search . '%';

            $i = 0;
            foreach ($conditions as $key => $condition) {
                $conditions[$key] = $DB->sql_like($condition, ":con{$i}00", false);
                $params["con{$i}00"] = $searchparam;
                $i++;
            }

            $tests[] = '(' . implode(' OR ', $conditions) . ')';
        }

        $wherecondition = implode(' AND ', $tests);

        $fields = \core_user\fields::for_identity($context, false)->excluding('username', 'lastaccess');

        $extrafields = $fields->get_required_fields();
        $extrafields[] = 'username';
        $extrafields[] = 'lastaccess';
        $extrafields[] = 'maildisplay';

        $ufields = user_picture::fields('u', $extrafields);

        return [$ufields, $params, $wherecondition];
    }

    public static function get_user_image_or_avatar($user) {
        global $PAGE;

        if ($PAGE->theme->name == 'moove' && function_exists('theme_moove_get_user_avatar_or_image')) {
            $userpicture = theme_moove_get_user_avatar_or_image($user);
        }

        if ($PAGE->theme->name == 'evoke' && function_exists('theme_evoke_get_user_avatar_or_image')) {
            $userpicture = theme_evoke_get_user_avatar_or_image($user);
        }

        if (!$userpicture) {
            $userpicture = new \user_picture($user);
            $userpicture->size = 1;
            $userpicture = $userpicture->get_url($PAGE);
        }

        if ($userpicture instanceof \moodle_url) {
            return $userpicture->out();
        }

        return $userpicture;
    }
}
