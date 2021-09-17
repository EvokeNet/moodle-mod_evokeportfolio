<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Evoke utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class chapter {
    public function get_portfolios_with_user_submissions($context, $chapter, $user) {
        $portfolios = $this->get_chapter_portfolios($chapter);

        if (!$portfolios) {
            return false;
        }

        $evokeportfolioutil = new evokeportfolio();

        $data = [];
        foreach ($portfolios as $portfolio) {
            $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($context, $portfolio->id, $user->id);

            $issinglesection = count($sectionssubmissions) == 1;

            $data[] = [
                'id' => $portfolio->id,
                'name' => $portfolio->name,
                'sections' => $sectionssubmissions,
                'issinglesection' => $issinglesection
            ];
        }

        return $data;
    }

    public function get_chapter_portfolios($chapter) {
        global $DB;

        if (!$chapter->portfolios) {
            return false;
        }

        list($insql, $params) = $DB->get_in_or_equal(explode(',', $chapter->portfolios), SQL_PARAMS_NAMED);

        $sql = "SELECT *                       
                FROM {evokeportfolio}
                WHERE id {$insql}";

        $portfolios = $DB->get_records_sql($sql, $params);

        if (!$portfolios) {
            return false;
        }

        return array_values($portfolios);
    }
}
