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

            foreach ($submissions as $submission) {
                $submission->humantimecreated = userdate($submission->timecreated);

                $this->populate_submission_with_comments($submission);
            }

            $this->populate_submission_with_user_info($submissions);

            $this->populate_submission_with_attachments($submissions, $context);

            return array_values($submissions);
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

            foreach ($submissions as $submission) {
                $submission->humantimecreated = userdate($submission->timecreated);

                $this->populate_submission_with_comments($submission);
            }

            $this->populate_submission_with_user_info($submissions);

            $this->populate_submission_with_attachments($submissions, $context);

            return array_values($submissions);
        }
    }

    public function populate_submission_with_comments($submission) {
        global $DB, $PAGE;

        $sql = 'SELECT c.id as commentid, c.text, u.*
                FROM {evokeportfolio_comments} c
                INNER JOIN {user} u ON u.id = c.userid
                WHERE c.submissionid = :submissionid';

        $comments = $DB->get_records_sql($sql, ['submissionid' => $submission->id]);

        if (!$comments) {
            $submission->comments = false;

            return $submission;
        }

        $commentsdata = [];
        foreach ($comments as $comment) {
            $userpicture = new \user_picture($comment);

            $commentsdata[] = [
                'text' => $comment->text,
                'commentuserpicture' => $userpicture->get_url($PAGE)->out(),
                'commentuserfullname' => fullname($comment)
            ];
        }

        $submission->comments = $commentsdata;

        return $submission;
    }

    private function populate_submission_with_user_info($data) {
        global $PAGE, $USER;

        foreach ($data as $key => $entry) {
            $user = clone($entry);
            $user->id = $entry->uid;

            $userpicture = new \user_picture($user);

            $data[$key]->usersubmissionpicture = $userpicture->get_url($PAGE)->out();

            $data[$key]->usersubmissionfullname = fullname($user);

            $data[$key]->isowner = $user->id == $USER->id;
        }

        return $data;
    }

    private function populate_submission_with_attachments($data, $context) {
        $fs = get_file_storage();

        foreach ($data as $key => $entry) {
            $files = $fs->get_area_files($context->id,
                'mod_evokeportfolio',
                'attachments',
                $entry->id,
                'timemodified',
                false);

            $data[$key]->hasattachments = false;

            if ($files) {
                $entryfiles = [];

                foreach ($files as $file) {
                    $path = [
                        '',
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $entry->id .$file->get_filepath() . $file->get_filename()
                    ];

                    $fileurl = \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);

                    $entryfiles[] = [
                        'filename' => $file->get_filename(),
                        'isimage' => $file->is_valid_image(),
                        'fileurl' => $fileurl
                    ];
                }

                $data[$key]->attachments = $entryfiles;
                $data[$key]->hasattachments = true;
            }
        }
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