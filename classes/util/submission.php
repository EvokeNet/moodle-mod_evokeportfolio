<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Portfolio submission utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

class submission {
    public function user_has_submission($portfolioid, $userid) {
        global $DB;

        $sql = 'SELECT COUNT(*)
                FROM {evokeportfolio_submissions}
                WHERE portfolioid = :portfolioid AND userid = :userid';

        $entries = $DB->count_records_sql($sql, ['portfolioid' => $portfolioid, 'userid' => $userid]);

        if ($entries) {
            return true;
        }

        return false;
    }

    public function get_user_submissions($context, $portfolioid, $userid) {
        global $DB;

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.userid
                WHERE portfolioid = :portfolioid AND userid = :userid';

        $submissions = $DB->get_records_sql($sql, ['portfolioid' => $portfolioid, 'userid' => $userid]);

        if (!$submissions) {
            return false;
        }

        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $this->populate_data_with_comments($submissions);

        $this->populate_data_with_user_info($submissions);

        $this->populate_data_with_attachments($submissions, $context);

        $this->populate_data_with_reactions($submissions);

        return array_values($submissions);
    }

    public function get_portfolio_submissions($portfolio, $context, $userid = false, $groupsorgroupid = null, $limit = 20, $offset = 0) {
        global $DB;

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.userid';

        if ($groupsorgroupid) {
            $sql .= ' INNER JOIN {groups_members} gm ON gm.userid = u.id';
        }

        $sql .= ' WHERE es.portfolioid = :portfolioid';

        $params['portfolioid'] = $portfolio->id;

        if ($userid) {
            $sql .= ' AND u.id = :userid';
            $params['userid'] = $userid;
        }

        if ($groupsorgroupid) {
            list($groupsids, $groupsparams) = $DB->get_in_or_equal($groupsorgroupid, SQL_PARAMS_NAMED, 'group');

            $sql .= ' AND gm.groupid ' . $groupsids;

            $params = array_merge($params, $groupsparams);
        }

        $sql .= ' ORDER BY es.id DESC LIMIT ' . $limit;

        if ($offset) {
            $offset = $offset * $limit;

            $sql .= ' OFFSET ' . $offset;
        }

        $submissions = $DB->get_records_sql($sql, $params);

        if (!$submissions) {
            return false;
        }

        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $this->populate_data_with_comments($submissions);

        $this->populate_data_with_user_info($submissions);

        $this->populate_data_with_attachments($submissions, $context);

        $this->populate_data_with_reactions($submissions);

        $this->populate_data_with_evaluation($submissions, $portfolio, $context);

        return array_values($submissions);
    }

    public function get_evokation_submissions($portfolios, $userid = false, $groupsorgroupid = null, $limit = 20, $offset = 0) {
        global $DB;

        $arrportfolios = array_map(function($item) { return $item->id; }, $portfolios);
        list($evokationids, $evokationparams) = $DB->get_in_or_equal($arrportfolios, SQL_PARAMS_NAMED, 'evk');

        $sql = 'SELECT
                    es.*,
                    u.id as uid, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt, u.email
                FROM {evokeportfolio_submissions} es
                INNER JOIN {user} u ON u.id = es.userid';

        if ($groupsorgroupid) {
            $sql .= ' INNER JOIN {groups_members} gm ON gm.userid = u.id';
        }

        $sql .= " WHERE es.portfolioid $evokationids";

        $params = $evokationparams;

        if ($userid) {
            $sql .= ' AND u.id = :userid';
            $params['userid'] = $userid;
        }

        if ($groupsorgroupid) {
            list($groupsids, $groupsparams) = $DB->get_in_or_equal($groupsorgroupid, SQL_PARAMS_NAMED, 'group');

            $sql .= ' AND gm.groupid ' . $groupsids;

            $params = array_merge($params, $groupsparams);
        }

        $sql .= ' ORDER BY es.id DESC LIMIT ' . $limit;

        if ($offset) {
            $offset = $offset * $limit;

            $sql .= ' OFFSET ' . $offset;
        }

        $submissions = $DB->get_records_sql($sql, $params);

        if (!$submissions) {
            return false;
        }

        foreach ($submissions as $submission) {
            $submission->humantimecreated = userdate($submission->timecreated);
        }

        $this->populate_data_with_comments($submissions);

        $this->populate_data_with_user_info($submissions);

        $this->populate_data_with_reactions($submissions);

        foreach ($submissions as $submission) {
            $this->populate_submission_with_attachments($submission, $portfolios[$submission->portfolioid]->context);

            $this->populate_data_with_evaluation($submission, $portfolios[$submission->portfolioid], $portfolios[$submission->portfolioid]->context);
        }

        return array_values($submissions);
    }

    public function populate_data_with_comments($submissions) {
        global $DB;

        foreach ($submissions as $submission) {
            $sql = 'SELECT c.id as commentid, c.text, u.*
                FROM {evokeportfolio_comments} c
                INNER JOIN {user} u ON u.id = c.userid
                WHERE c.submissionid = :submissionid';

            $comments = $DB->get_records_sql($sql, ['submissionid' => $submission->id]);

            if (!$comments) {
                $submission->comments = false;

                continue;
            }

            $commentsdata = [];
            foreach ($comments as $comment) {
                $userpicture = user::get_user_image_or_avatar($comment);

                $commentsdata[] = [
                    'text' => $comment->text,
                    'commentuserpicture' => $userpicture,
                    'commentuserfullname' => fullname($comment)
                ];
            }

            $submission->comments = $commentsdata;
            $submission->totalcomments = count($commentsdata);
        }

        return $submissions;
    }

    public function populate_data_with_user_info($data) {
        global $USER;

        foreach ($data as $key => $entry) {
            $user = clone($entry);
            $user->id = $entry->uid;

            $userimage = user::get_user_image_or_avatar($user);

            $data[$key]->usersubmissionpicture = $userimage;

            $data[$key]->usersubmissionfullname = fullname($user);

            $data[$key]->isowner = $user->id == $USER->id;
        }

        return $data;
    }

    public function populate_data_with_attachments($data, $context) {
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
                        $entry->id . $file->get_filepath() . $file->get_filename()
                    ];

                    $fileurl = \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);

                    if (!$file->is_valid_image() || $file->get_filepath() == '/thumb/') {
                        $entryfiles[] = [
                            'filename' => $file->get_filename(),
                            'isimage' => $file->is_valid_image(),
                            'fileurl' => $fileurl->out()
                        ];
                    }
                }

                $data[$key]->attachments = $entryfiles;
                $data[$key]->hasattachments = true;
            }
        }
    }

    public function populate_submission_with_attachments($submission, $context) {
        $fs = get_file_storage();

        $files = $fs->get_area_files($context->id,
            'mod_evokeportfolio',
            'attachments',
            $submission->id,
            'timemodified',
            false);

        $submission->hasattachments = false;

        if ($files) {
            $entryfiles = [];

            foreach ($files as $file) {
                $path = [
                    '',
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $submission->id . $file->get_filepath() . $file->get_filename()
                ];

                $fileurl = \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);

                if (!$file->is_valid_image() || $file->get_filepath() == '/thumb/') {
                    $entryfiles[] = [
                        'filename' => $file->get_filename(),
                        'isimage' => $file->is_valid_image(),
                        'fileurl' => $fileurl->out()
                    ];
                }
            }

            $submission->attachments = $entryfiles;
            $submission->hasattachments = true;
        }
    }

    public function populate_data_with_reactions($submissions) {
        $reactionutil = new reaction();

        foreach ($submissions as $submission) {
            $submission->totalreactions = $reactionutil->get_total_reactions($submission->id, reaction::LIKE);
            $submission->userreacted = $reactionutil->user_reacted($submission->id, reaction::LIKE);
        }

        return $submissions;
    }

    public function populate_data_with_evaluation($submissions, $portfolio, $context) {
        $gradeutil = new grade();

        foreach ($submissions as $submission) {
            $usergrade = $gradeutil->get_user_grade_string($portfolio, $submission->uid);
            $submission->hasevaluation = !$usergrade == false;
            $submission->grade = $usergrade;
        }

        return $submissions;
    }

    public function create_submission_thumbs($submission, $context) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id,
            'mod_evokeportfolio',
            'attachments',
            $submission->id,
            'timemodified',
            false);

        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                $filerecord = [
                    'userid' => $submission->userid,
                    'filename' => $file->get_filename(),
                    'contextid' => $file->get_contextid(),
                    'component' => $file->get_component(),
                    'filearea' => $file->get_filearea(),
                    'itemid' => $submission->id,
                    'filepath' => '/thumb/'
                ];

                $fileinfo = $file->get_imageinfo();

                $width = $fileinfo['width'];
                if ($width > 600) {
                    $width = 600;
                }

                $fs->convert_image($filerecord, $file, $width);
            }
        }
    }
}