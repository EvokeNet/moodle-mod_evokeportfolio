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

    public function get_sections_submissions($context, $portfolioid, $userid = null, $groupid = null) {
        $sections = $this->get_sections($portfolioid);

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

            $submissions = $this->get_section_submissions($section->id, $userid, $groupid);
            $sections[$key]->nosubmissions = false;

            if (!$submissions) {
                $sections[$key]->submissions = [];
                $sections[$key]->nosubmissions = true;

                continue;
            }

            $this->populate_data_with_user_info($submissions);

            $this->populate_data_with_attachments($submissions, $context);

            $sections[$key]->submissions = $submissions;
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

    public function get_used_course_portfolios_instances($courseid) {
        global $DB;

        $sql = 'SELECT id, portfolios
                FROM {evokeportfolio_chapters}
                WHERE course = :courseid';

        $chapters = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (!$chapters) {
            return false;
        }

        $portfolios = [];
        foreach ($chapters as $chapter) {
            $portfoliosarr = explode(',', $chapter->portfolios);

            $portfolios = array_merge($portfolios, $portfoliosarr);
        }

        return $portfolios;
    }

    public function get_unused_course_portfolio_instances_select($courseid, $chapterid = null) {
        $portfolios = $this->get_course_portfolio_instances($courseid);

        if (!$portfolios) {
            return [];
        }

//        $usedcoureportfolios = $this->get_used_course_portfolios_instances($courseid);
//
//        foreach ($portfolios as $key => $portfolio) {
//            foreach ($usedcoureportfolios as $usedcoureportfolio) {
//                if ($portfolio->id == $usedcoureportfolio) {
//                    unset($portfolios[$key]);
//
//                    continue 2;
//                }
//            }
//        }
//
//        if (!$portfolios) {
//            return [];
//        }

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
                INNER JOIN {grade_items} gi ON gi.iteminstance = e.id AND gi.itemmodule = "evokeportfolio"
                WHERE e.course = :courseid';

        return $DB->get_records_sql($sql, ['courseid' => $courseid]);
    }
}
