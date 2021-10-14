<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\group;
use renderable;
use templatable;
use renderer_base;

/**
 * Submissions renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class submissions implements renderable, templatable {

    public $evokeportfolio;
    public $context;

    public function __construct($evokeportfolio, $context) {
        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;
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
            'cmid' => $this->context->instanceid,
            'course' => $this->evokeportfolio->course,
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'isdelayed' => $isdelayed,
            'isteacher' => false
        ];

        $evokeportfolioutil = new evokeportfolio();

        if (has_capability('mod/evokeportfolio:grade', $this->context)) {
            $data['isteacher'] = true;

            return $data;
        }

        if ($this->evokeportfolio->groupactivity) {
            $groupsutil = new group();
            $usercoursegroup = $groupsutil->get_user_group($this->evokeportfolio->course);

            $data['hasgroup'] = !empty($usercoursegroup);

            if ($usercoursegroup) {
                $data['groupname'] = $usercoursegroup->name;
                $data['groupmembers'] = $groupsutil->get_group_members($usercoursegroup->id);

                $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($this->context, $this->evokeportfolio->id, null, $usercoursegroup->id);

                $data['sectionssubmissions'] = $sectionssubmissions;
                $data['issinglesection'] = count($sectionssubmissions) == 1;

                $data['hassectionsavailable'] = count($sectionssubmissions) ? true : false;
            }

            return $data;
        }

        $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($this->context, $this->evokeportfolio->id, $USER->id);

        $data['sectionssubmissions'] = $sectionssubmissions;
        $data['issinglesection'] = count($sectionssubmissions) == 1;
        $data['hassectionsavailable'] = count($sectionssubmissions) ? true : false;

        $userpicture = new \user_picture($USER);
        $userpicture->size = 1;

        $data['userpicture'] = $userpicture->get_url($PAGE)->out();
        $data['userfullname'] = fullname($USER);

        return $data;
    }
}
