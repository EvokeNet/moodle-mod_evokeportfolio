<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Portfolio sections utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

class section {
    public function get_portfolio_sections($portfolioid) {
        global $DB;

        $sections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $portfolioid]);

        if (!$sections) {
            return false;
        }

        return array_values($sections);
    }

    public function get_sections_submissions($context, $portfolioid, $userid = null, $groupid = null) {
        $sections = $this->get_portfolio_sections($portfolioid);

        if (!$sections) {
            return false;
        }

        foreach ($sections as $key => $section) {
            if ($section->dependentsections) {
                $dependentsections = explode(",", $section->dependentsections);

                if (!$this->has_section_access($dependentsections, $context)) {
                    unset($sections[$key]);

                    continue;
                }
            }

            $submissions = $this->get_section_submissions($context, $section->id, $userid, $groupid);
            $sections[$key]->nosubmissions = false;

            if (!$submissions) {
                $sections[$key]->submissions = [];
                $sections[$key]->nosubmissions = true;

                continue;
            }

            $sections[$key]->submissions = $submissions;
        }

        return $sections;
    }

    public function get_section_submissions($context, $sectionid, $userids = null, $groupid = null) {
        global $DB;

        if (!$userids && !$groupid) {
            throw new \Exception('You need to inform either an user or group id');
        }

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.postedby
                WHERE es.sectionid = :sectionid';

        if ($groupid) {
            $sql .= ' AND es.groupid = :groupid ORDER BY es.id desc';
            $submissions = $DB->get_records_sql($sql, ['sectionid' => $sectionid, 'groupid' => $groupid]);

            if (!$submissions) {
                return false;
            }

            return $this->populate_submission_with_data($submissions, $context);
        }

        if ($userids) {
            $params = ['sectionid' => $sectionid];

            if (is_array($userids)) {
                list($sqld, $paramsd) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

                $sql .= ' AND es.userid '. $sqld .' ORDER BY es.id desc';

                $params = $params + $paramsd;
            } else {
                $sql .= ' AND es.userid = :userid ORDER BY es.id desc';

                $params = $params + ['userid' => $userids];
            }

            $submissions = $DB->get_records_sql($sql, $params);

            if (!$submissions) {
                return false;
            }

            return $this->populate_submission_with_data($submissions, $context);
        }
    }

    private function populate_submission_with_data($submissions, $context) {
        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $submissionsutil = new submission();

        $submissionsutil->populate_data_with_comments($submissions);

        $submissionsutil->populate_data_with_user_info($submissions);

        $submissionsutil->populate_data_with_attachments($submissions, $context);

        return array_values($submissions);
    }

    public function add_sections_access_info($sections, $context) {
        foreach ($sections as $key => $section) {
            $sections[$key]->hasaccess = true;

            if ($section->dependentsections) {
                $dependentsections = explode(",", $section->dependentsections);

                if (!$this->has_section_access($dependentsections, $context)) {
                    $sections[$key]->hasaccess = false;
                }
            }
        }

        return $sections;
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
}