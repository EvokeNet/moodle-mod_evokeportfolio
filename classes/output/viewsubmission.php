<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\section;
use mod_evokeportfolio\util\user;
use renderable;
use templatable;
use renderer_base;

/**
 * View submission renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class viewsubmission implements renderable, templatable {

    public $evokeportfolio;
    public $context;
    public $user;

    public function __construct($evokeportfolio, $context, $user) {
        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;

        $this->user = $user;
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
        global $PAGE, $USER;

        $gradeutil = new \mod_evokeportfolio\util\grade();

        $isgradinglocked = false;
        if ($this->evokeportfolio->grade == 0 || $gradeutil->is_gradeitem_locked($this->evokeportfolio->id, $this->evokeportfolio->course)) {
            $isgradinglocked = true;
        }

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'course' => $this->evokeportfolio->course,
            'isgradinglocked' => $isgradinglocked,
        ];

        $sectionutil = new section();

        $userpicture = new \user_picture($USER);
        $userpicture->size = 1;

        $data['userfullname'] = fullname($USER);
        $data['userpicture'] = $userpicture->get_url($PAGE)->out();

        $userpicture = new \user_picture($this->user);
        $userpicture->size = 1;

        $data['userid'] = $this->user->id;
        $data['targetuserfullname'] = fullname($this->user);
        $data['targetuserpicture'] = $userpicture->get_url($PAGE)->out();
        $data['userhasgrade'] = $gradeutil->user_has_grade($this->evokeportfolio, $this->user->id);

        $sectionssubmissions = $sectionutil->get_sections_submissions($this->context, $this->evokeportfolio->id, $this->user->id);

        $data['sectionssubmissions'] = $sectionssubmissions;
        $data['issinglesection'] = count($sectionssubmissions) == 1;

        $userutil = new user();
        $usergroup = $userutil->get_user_group($this->user->id, $this->evokeportfolio->course);
        $data['usergroup'] = $usergroup ? $usergroup->name : false;

        return $data;
    }
}
