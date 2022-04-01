<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Evoke utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class evokeportfolio {
    public function get_instance($id) {
        global $DB;

        return $DB->get_record('evokeportfolio', ['id' => $id], '*', MUST_EXIST);
    }

    public function get_course_portfolio_instances_select($courseid, $chapterid = null) {
        $portfolios = $this->get_course_portfolio_instances($courseid);

        if (!$portfolios) {
            return [];
        }

        $data = [];
        foreach ($portfolios as $portfolio) {
            $data[$portfolio->id] = $portfolio->name;
        }

        return $data;
    }

    public function get_course_portfolio_instances($courseid) {
        global $DB;

        $sql = 'SELECT e.*
                FROM {evokeportfolio} e
                WHERE e.course = :courseid';

        return $DB->get_records_sql($sql, ['courseid' => $courseid]);
    }
}
