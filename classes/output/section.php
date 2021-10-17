<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\section as sectionutil;
use renderable;
use templatable;
use renderer_base;

/**
 * Submissions renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class section implements renderable, templatable {
    public $context;
    public $evokeportfolio;
    public $section;
    public $user;
    public $group;

    public function __construct($context, $evokeportfolio, $section, $user = null, $group = null) {
        global $USER;

        $this->context = $context;
        $this->evokeportfolio = $evokeportfolio;
        $this->section = $section;
        $this->user = $user;
        $this->group = $group;

        if (!$user) {
            $this->user = $USER;
        }
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

        $userpicture = new \user_picture($USER);
        $userpicture->size = 1;

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'courseid' => $this->evokeportfolio->course,
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'isdelayed' => $isdelayed,
            'sectionid' => $this->section->id,
            'sectionname' => $this->section->name,
            'sectiondescription' => $this->section->description,
            'isteacher' => false,
            'userpicture' => $userpicture->get_url($PAGE)->out(),
            'userfullname' => fullname($USER),
            'itsme' => $this->user->id === $USER->id
        ];

        if (has_capability('mod/evokeportfolio:grade', $this->context)) {
            $data['isteacher'] = true;

            return $data;
        }

        $sectionutil = new sectionutil();

        if ($this->evokeportfolio->groupactivity) {
            $groupsutil = new group();
            $usercoursegroup = $groupsutil->get_user_group($this->evokeportfolio->course);

            $data['hasgroup'] = !empty($usercoursegroup);

            if ($usercoursegroup) {
                $data['groupname'] = $usercoursegroup->name;
                $data['groupmembers'] = $groupsutil->get_group_members($usercoursegroup->id);

                $submissions = $sectionutil->get_section_submissions($this->context, $this->section->id, null, $usercoursegroup->id);

                $data['submissions'] = $submissions;
            }

            return $data;
        }

        $data['submissions'] = $sectionutil->get_section_submissions($this->context, $this->section->id, $this->user->id);

        return $data;
    }
}
