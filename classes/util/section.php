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