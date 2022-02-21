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
                $userpicture = theme_evoke_get_user_avatar_or_image($comment);

                $commentsdata[] = [
                    'text' => $comment->text,
                    'commentuserpicture' => $userpicture,
                    'commentuserfullname' => fullname($comment)
                ];
            }

            $submission->comments = $commentsdata;
        }

        return $submissions;
    }

    public function populate_data_with_user_info($data) {
        global $USER;

        foreach ($data as $key => $entry) {
            $user = clone($entry);
            $user->id = $entry->uid;

            $userimage = theme_evoke_get_user_avatar_or_image($user);

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

    public function populate_data_with_reactions($submissions) {
        $reactionutil = new reaction();

        foreach ($submissions as $submission) {
            $submission->reactionstring = $reactionutil->get_reactions_string($submission->id, reaction::LIKE);
            $submission->userreacted = $reactionutil->user_reacted($submission->id, reaction::LIKE);
        }

        return $submissions;
    }

    public function populate_data_with_evaluation($submissions, $portfolio) {
        $gradeutil = new grade();

        foreach ($submissions as $submission) {
            $usergrade = $gradeutil->get_user_grade_string($portfolio, $submission->uid);
            $submission->hasevaluation = !$usergrade == false;
            $submission->grade = $usergrade;
        }

        return $submissions;
    }
}