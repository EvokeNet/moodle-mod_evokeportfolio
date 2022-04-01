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

    public function get_course_portfolio_instances($courseid, $evokation = 0) {
        global $DB;

        $sql = 'SELECT *
                FROM {evokeportfolio}
                WHERE course = :courseid AND evokation = :evokation';

        $portfolios = $DB->get_records_sql($sql, ['courseid' => $courseid, 'evokation' => $evokation]);

        if (!$portfolios) {
            return false;
        }

        $now = time();

        foreach ($portfolios as $portfolio) {
            $portfolio->isavailable = false;

            if ($portfolio->datestart < $now && $portfolio->datelimit > $now) {
                $portfolio->isavailable = true;
            }
        }

        return array_values($portfolios);
    }
}
