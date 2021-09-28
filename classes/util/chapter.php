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

    public function get_portfolios_with_user_submissions($context, $chapter, $user) {
        $portfolios = $this->get_chapter_portfolios($chapter);

        if (!$portfolios) {
            return false;
        }

        $evokeportfolioutil = new evokeportfolio();

        $userutil = new user();
        $usergroup = $userutil->get_user_group($user->id, $chapter->course);

        $data = [];
        foreach ($portfolios as $portfolio) {

            $usergroupid = null;
            if ($usergroup && $portfolio->groupactivity) {
                $usergroupid = $usergroup->id;
            }

            $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($context, $portfolio->id, $user->id, $usergroupid);

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
