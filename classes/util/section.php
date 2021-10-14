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

    public function get_section_submissions($context, $sectionid, $userid = null, $groupid = null) {
        global $DB;

        if (!$userid && !$groupid) {
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
            $entries = $DB->get_records_sql($sql, ['sectionid' => $sectionid, 'groupid' => $groupid]);

            if (!$entries) {
                return false;
            }

            foreach ($entries as $key => $entry) {
                $entries[$key]->humantimecreated = userdate($entry->timecreated);
            }

            $this->populate_data_with_attachments($entries, $context);

            return array_values($entries);
        }

        if ($userid) {
            $sql .= ' AND es.userid = :userid ORDER BY es.id desc';
            $entries = $DB->get_records_sql($sql, ['sectionid' => $sectionid, 'userid' => $userid]);

            if (!$entries) {
                return false;
            }

            foreach ($entries as $key => $entry) {
                $entries[$key]->humantimecreated = userdate($entry->timecreated);
            }

            $this->populate_data_with_attachments($entries, $context);

            return array_values($entries);
        }
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