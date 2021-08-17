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

    public function get_sections($portfolioid) {
        global $DB;

        $sections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $portfolioid]);

        if (!$sections) {
            return false;
        }

        $evokeportfolioutil = new evokeportfolio();
        foreach ($sections as $key => $section) {
            $sections[$key]->hassubmission = $evokeportfolioutil->section_has_submissions($section->id);
        }

        return array_values($sections);
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

    public function get_sections_submissions($context, $portfolioid, $userid = null, $groupid = null) {
        $sections = $this->get_sections($portfolioid);

        if (!$sections) {
            return false;
        }

        foreach ($sections as $key => $section) {
            $submissions = $this->get_section_submissions($section->id, $userid, $groupid);

            if (!$submissions) {
                $sections[$key]->submissions = [];

                continue;
            }

            $this->populate_data_with_user_info($submissions);

            $this->populate_data_with_attachments($submissions, $context);

            $sections[$key]->submissions = $submissions;
        }

        return $sections;
    }

    private function populate_data_with_user_info($data) {
        global $PAGE, $USER;

        foreach ($data as $key => $entry) {
            $user = clone($entry);
            $user->id = $entry->uid;

            $userpicture = new \user_picture($user);

            $data[$key]->userpicture = $userpicture->get_url($PAGE)->out();

            $data[$key]->fullname = fullname($user);

            $data[$key]->isteacher = false;
            if ($entry->role == MOD_EVOKEPORTFOLIO_ROLE_TEACHER) {
                $data[$key]->isteacher = true;
            }

            $data[$key]->isowner = $user->id == $USER->id;
        }

        return $data;
    }

    private function populate_data_with_attachments($data, $context) {

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
}
