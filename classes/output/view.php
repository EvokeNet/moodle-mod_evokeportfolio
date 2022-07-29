<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\group;
use renderable;
use templatable;
use renderer_base;

/**
 * View renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class view implements renderable, templatable {

    public $evokeportfolio;
    public $context;
    public $embed;

    public function __construct($evokeportfolio, $context, $embed = 0) {
        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;
        $this->embed = $embed;
    }

    /**
     * Export the data
     *
     * @param renderer_base $output
     *
     * @return array|\stdClass
     *
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $timeremaining = $this->evokeportfolio->datelimit - time();

        $isdelayed = true;
        if ($timeremaining > 0) {
            $isdelayed = false;
        }

        $chapterutil = new chapter();

        // Chapters data.
        $chapters = $chapterutil->get_course_chapters($this->evokeportfolio->course);

        if (!$chapters) {
            return [
                'courseid' => $this->evokeportfolio->course
            ];
        }

        $groupsutil = new group();

        $usercoursegroups = $groupsutil->get_user_groups($this->evokeportfolio->course);

        $groupsmembers = [];
        if ($usercoursegroups) {
            $groupsmembers = $groupsutil->get_groups_members($usercoursegroups, true, $this->context);
        }

        return [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'intro' => format_module_intro('evokeportfolio', $this->evokeportfolio, $this->context->instanceid),
            'datelimit' => userdate($this->evokeportfolio->datelimit),
            'timeremaining' => format_time($timeremaining),
            'groupactivity' => (int)$this->evokeportfolio->groupactivity,
            'cmid' => $this->context->instanceid,
            'courseid' => $this->evokeportfolio->course,
            'isdelayed' => $isdelayed,
            'embed' => $this->embed,
            'portfolioid' => $this->evokeportfolio->id,
            'evokation' => $this->evokeportfolio->evokation,
            'contextid' => $this->context->id,
            'groupsmembers' => $groupsmembers,
            'hasgroupsmembers' => (int) !empty($groupsmembers),
            'hasgroup' => (int) !empty($usercoursegroups)
        ];
    }
}
