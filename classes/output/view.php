<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\section;
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
        global $USER, $PAGE;

        $timeremaining = $this->evokeportfolio->datelimit - time();

        $isdelayed = true;
        if ($timeremaining > 0) {
            $isdelayed = false;
        }

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'intro' => format_module_intro('evokeportfolio', $this->evokeportfolio, $this->context->instanceid),
            'datelimit' => userdate($this->evokeportfolio->datelimit),
            'timeremaining' => format_time($timeremaining),
            'cmid' => $this->context->instanceid,
            'courseid' => $this->evokeportfolio->course,
            'isdelayed' => $isdelayed,
            'itsme' => true,
            'embed' => $this->embed
        ];

        // Teacher.
        if (has_capability('mod/evokeportfolio:grade', $this->context)) {
            $coursemodule = get_coursemodule_from_instance('evokeportfolio', $this->evokeportfolio->id);
            $data['hide'] = $coursemodule->visible;

            $participants = count_enrolled_users($this->context, 'mod/evokeportfolio:submit');
            $data['participants'] = $participants;

            return $data;
        }

        $sectionutil = new section();

        $sections = $sectionutil->get_portfolio_sections($this->evokeportfolio->id);
        $sections = $sectionutil->add_sections_access_info($sections, $this->context);

        // Single section.
        $issinglesection = count($sections) == 1;
        $submissions = null;
        if ($issinglesection) {
            $section = current($sections);
            $data['issinglesection'] = true;
            $data['sectionid'] = $section->id;

            $userpicture = new \user_picture($USER);
            $userpicture->size = 1;

            $data['userpicture'] = $userpicture->get_url($PAGE)->out();
            $data['userfullname'] = fullname($USER);

            $submissions = $sectionutil->get_section_submissions($this->context, $section->id, $USER->id);
        }

        if (!$issinglesection) {
            $data['sections'] = $sections;
        }

        $data['submissions'] = $submissions;

        return $data;
    }
}
