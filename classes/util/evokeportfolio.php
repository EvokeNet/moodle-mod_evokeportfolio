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

    public function has_submission($portfolioid, $userid = false, $groupid = null) {
        global $DB;

        if ($groupid) {
            $sql = 'SELECT COUNT(*) FROM {evokeportfolio_submissions} sub
                    INNER JOIN {evokeportfolio_sections} sec ON sec.id = sub.sectionid
                    WHERE sec.portfolioid = :portfolioid AND sub.groupid = :groupid';
            $entries = $DB->count_records_sql($sql, ['portfolioid' => $portfolioid, 'groupid' => $groupid]);

            if ($entries) {
                return true;
            }

            return false;
        }

        if ($userid) {
            $sql = 'SELECT COUNT(*) FROM {evokeportfolio_submissions} sub
                    INNER JOIN {evokeportfolio_sections} sec ON sec.id = sub.sectionid
                    WHERE sec.portfolioid = :portfolioid AND sub.userid = :userid';
            $entries = $DB->count_records_sql($sql, ['portfolioid' => $portfolioid, 'userid' => $userid]);

            if ($entries) {
                return true;
            }
        }

        return false;
    }

    public function get_course_sections($portfolioid = null, $sectionid = null) {
        global $DB;

        if ($portfolioid) {
            $portfolio = $DB->get_record('evokeportfolio', ['id' => $portfolioid], '*', MUST_EXIST);
        }

        if (!$portfolioid && $sectionid) {
            $section = $DB->get_record('evokeportfolio_sections', ['id' => $sectionid], '*', MUST_EXIST);

            $portfolio = $DB->get_record('evokeportfolio', ['id' => $section->portfolioid], '*', MUST_EXIST);
        }

        $sections = $DB->get_records('course_sections', ['course' => $portfolio->course, 'visible' => 1]);

        if (!$sections) {
            return [];
        }

        $coursesections = [];
        foreach ($sections as $section) {
            $sectionname = $section->name;

            if (!$section->name) {
                $sectionname = 'Section ' . $section->section;
            }

            $coursesections[$section->id] = $sectionname;
        }

        return $coursesections;
    }

    public function section_has_submissions($sectionid) {
        global $DB;

        $counter = $DB->count_records('evokeportfolio_submissions', ['sectionid' => $sectionid]);

        if ($counter) {
            return true;
        }

        return false;
    }

    public function get_section_submissions($sectionid, $userid = null, $groupid = null) {
        global $DB;

        if (!$userid && !$groupid) {
            throw new \Exception('You need to inform an user id or group id');
        }

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.postedby
                WHERE es.sectionid = :sectionid';

        if ($groupid) {
            $sql .= ' AND es.groupid = :groupid ORDER BY es.id desc';
            $entries = $DB->get_records_sql($sql, ['sectionid' => $sectionid, 'groupid' => $groupid]);

            if (!$entries) {
                return false;
            }

            return array_values($entries);
        }

        if ($userid) {
            $sql .= ' AND es.userid = :userid ORDER BY es.id desc';
            $entries = $DB->get_records_sql($sql, ['sectionid' => $sectionid, 'userid' => $userid]);

            if (!$entries) {
                return false;
            }

            return array_values($entries);
        }
    }

    public function get_portfolio_submissions($portfolio, $context, $userid = false, $groupid = null) {
        global $DB;

        $sectionutil = new section();

        $sections = $sectionutil->get_portfolio_sections($portfolio->id);

        if (!$sections) {
            return false;
        }

        $sectionsdata = [];
        foreach ($sections as $section) {
            $sectionsdata[] = $section->id;
        }

        list($sectioncondition, $params) = $DB->get_in_or_equal($sectionsdata, SQL_PARAMS_NAMED);

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.postedby';

        if ($groupid) {
            $sql .= ' INNER JOIN {groups_members} gm ON gm.userid = u.id';
        }

        $sql .= ' WHERE es.sectionid ' . $sectioncondition;

        if ($userid) {
            $sql .= ' AND u.id = :userid';
            $params['userid'] = $userid;
        }

        if ($groupid) {
            $sql .= ' AND gm.groupid = :groupid';
            $params['groupid'] = $groupid;
        }

        $submissions = $DB->get_records_sql($sql, $params);

        if (!$submissions) {
            return false;
        }

        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $submissionsutil = new submission();

        $submissionsutil->populate_data_with_comments($submissions);

        $submissionsutil->populate_data_with_user_info($submissions);

        $submissionsutil->populate_data_with_attachments($submissions, $context);

        $submissionsutil->populate_data_with_reactions($submissions);

        $submissionsutil->populate_data_with_evaluation($submissions, $portfolio);

        return array_values($submissions);
    }

    public function get_portfolio_group_submissions($portfolio, $context, $groupid) {
        global $DB;

        $grouputil = new group();

        $groupmembers = $grouputil->get_group_members($groupid, false);

        if (!$groupmembers) {
            return false;
        }

        $groupmembersids = [];
        foreach ($groupmembers as $groupmember) {
            $groupmembersids[] = $groupmember->id;
        }

        $sectionutil = new section();

        $sections = $sectionutil->get_portfolio_sections($portfolio->id);

        if (!$sections) {
            return false;
        }

        $sectionsdata = [];
        foreach ($sections as $section) {
            $sectionsdata[] = $section->id;
        }

        list($sectioncondition, $sectionparams) = $DB->get_in_or_equal($sectionsdata, SQL_PARAMS_NAMED);
        list($groupmemberscondition, $groupmemberparams) = $DB->get_in_or_equal($groupmembersids, SQL_PARAMS_NAMED);

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.postedby
                WHERE es.sectionid ' . $sectioncondition . ' AND u.id ' . $groupmemberscondition;

        $params = $sectionparams + $groupmemberparams;

        $submissions = $DB->get_records_sql($sql, $params);

        if (!$submissions) {
            return false;
        }

        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $submissionsutil = new submission();

        $submissionsutil->populate_data_with_comments($submissions);

        $submissionsutil->populate_data_with_user_info($submissions);

        $submissionsutil->populate_data_with_attachments($submissions, $context);

        $submissionsutil->populate_data_with_reactions($submissions);

        return array_values($submissions);
    }

    public function has_section_access($dependentsections, $context) {
        global $DB;

        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        $courseid = $context->get_course_context()->instanceid;
        $cachecoursemodinfo = \cache::make('core', 'coursemodinfo');
        $coursemodinfo = $cachecoursemodinfo->get($courseid);

        $modinfo = get_fast_modinfo($courseid);

        $sectionsinfo = [];
        foreach ($coursemodinfo->sectioncache as $number => $data) {
            $sectionsinfo[$number] = new \section_info($data, $number, null, null, $modinfo, null);
        }

        foreach ($dependentsections as $dependentsection) {
            $coursesection = $DB->get_record('course_sections', ['id' => $dependentsection], '*', MUST_EXIST);

            if (!$coursesection->visible && !$canviewhidden) {
                return false;
            }

            $sectioninfo = $sectionsinfo[$coursesection->section];

            if (!$sectioninfo->available) {
                return false;
            }
        }

        return true;
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
