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
    public function get_course_chapters($courseid) {
        global $DB;

        $sql = 'SELECT *
                FROM {evokeportfolio_chapters}
                WHERE course = :courseid';

        $chapters = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (!$chapters) {
            return false;
        }

        return array_values($chapters);
    }

    public function get_course_chapters_with_portfolios($courseid) {
        $chapterutil = new chapter();

        $chapters = $this->get_course_chapters($courseid);

        if (!$chapters) {
            return false;
        }

        $data = [];
        foreach ($chapters as $chapter) {
            if (!$chapter->portfolios) {
                continue;
            }

            $portfolios = $chapterutil->get_chapter_portfolios($chapter);

            if (!$portfolios) {
                $data[] = [
                    'id' => $chapter->id,
                    'name' => $chapter->name
                ];

                continue;
            }

            $portfoliosdata = [];
            foreach ($portfolios as $portfolio) {
                $portfoliosdata[] = [
                    'id' => $portfolio->id,
                    'name' => $portfolio->name
                ];
            }

            $data[] = [
                'id' => $chapter->id,
                'name' => $chapter->name,
                'portfolios' => $portfoliosdata
            ];
        }

        return $data;
    }

    public function get_portfolios_with_user_submissions($chapter, $user) {
        $submissionutil = new submission();

        $portfolios = $this->get_chapter_portfolios($chapter);

        if (!$portfolios) {
            return false;
        }

        $data = [];
        foreach ($portfolios as $portfolio) {
            $portfolio->submissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), $user->id);

            $data[] = $portfolio;
        }

        return $data;
    }

    public function get_chapter_portfolios($chapter, $evokation = 0) {
        global $DB;

        if (!$chapter->portfolios) {
            return false;
        }

        list($insql, $params) = $DB->get_in_or_equal(explode(',', $chapter->portfolios), SQL_PARAMS_NAMED);

        $sql = "SELECT * 
                FROM {evokeportfolio}
                WHERE evokation = :evokation AND id {$insql}";

        $params['evokation'] = $evokation;

        $portfolios = $DB->get_records_sql($sql, $params);

        if (!$portfolios) {
            return false;
        }

        foreach ($portfolios as $portfolio) {
            $portfolio->isevaluated = false;

            $coursemodule = get_coursemodule_from_instance('evokeportfolio', $portfolio->id);

            $portfolio->intro = format_module_intro('evokeportfolio', $portfolio, $coursemodule->id);

            if ($portfolio->grade != 0) {
                $portfolio->isevaluated = true;
            }
        }

        return array_values($portfolios);
    }

    private function get_portfolio_context($portfolioid) {
        if (isset($this->portfoliocontexts[$portfolioid])) {
            return $this->portfoliocontexts[$portfolioid];
        }

        $coursemodule = get_coursemodule_from_instance('evokeportfolio', $portfolioid);

        $this->portfoliocontexts[$portfolioid] = \context_module::instance($coursemodule->id);

        return $this->portfoliocontexts[$portfolioid];
    }
}
