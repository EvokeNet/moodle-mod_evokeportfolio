<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\groups;
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
    public $group;

    public function __construct($evokeportfolio, $context, $userid = null, $groupid = null) {
        global $DB;

        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;

        if ($userid) {
            $this->user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
        }

        if ($groupid) {
            $this->group = $DB->get_record('groups', ['id' => $groupid], '*', MUST_EXIST);
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
        global $PAGE;

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
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'isgradinglocked' => $isgradinglocked,
        ];

        $evokeportfolioutil = new evokeportfolio();

        if ($this->evokeportfolio->groupactivity) {
            $groupsutil = new groups();

            $data['hassubmission'] = false;
            $data['submissions'] = false;

            $data['groupid'] = $this->group->id;
            $data['groupname'] = $this->group->name;
            $data['groupmembers'] = $groupsutil->get_group_members($this->group->id);

            $data['hassubmission'] = $evokeportfolioutil->has_submission($this->evokeportfolio->id, null, $this->group->id);

            $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($this->context, $this->evokeportfolio->id, null, $this->group->id);

            $data['sectionssubmissions'] = $sectionssubmissions;
            $data['issinglesection'] = count($sectionssubmissions) == 1;

            $data['grouphasgrade'] = $gradeutil->group_has_grade($this->evokeportfolio, $this->group->id);

            return $data;
        }

        $userpicture = new \user_picture($this->user);
        $userpicture->size = 1;

        $data['userid'] = $this->user->id;
        $data['userfullname'] = fullname($this->user);
        $data['userpicture'] = $userpicture->get_url($PAGE)->out();
        $data['userhasgrade'] = $gradeutil->user_has_grade($this->evokeportfolio, $this->user->id);

        $sectionssubmissions = $evokeportfolioutil->get_sections_submissions($this->context, $this->evokeportfolio->id, $this->user->id);

        $data['sectionssubmissions'] = $sectionssubmissions;
        $data['issinglesection'] = count($sectionssubmissions) == 1;

        $userutil = new user();
        $usergroup = $userutil->get_user_group($this->user->id, $this->evokeportfolio->course);
        $data['usergroup'] = $usergroup ? $usergroup->name : false;

        return $data;
    }
}
