<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\submission;
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
        global $USER;

        $gradeutil = new \mod_evokeportfolio\util\grade();

        $isgradinglocked = false;
        if ($this->evokeportfolio->grade == 0 || $gradeutil->is_gradeitem_locked($this->evokeportfolio->id, $this->evokeportfolio->course)) {
            $isgradinglocked = true;
        }

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'contextid' => $this->context->id,
            'courseid' => $this->evokeportfolio->course,
            'isgradinglocked' => $isgradinglocked,
            'cangrade' => has_capability('mod/evokeportfolio:grade', $this->context),
            'isevaluated' => $this->evokeportfolio->grade != 0
        ];

        $data['userfullname'] = fullname($USER);
        $data['userpicture'] = user::get_user_image_or_avatar($USER);

        $data['userid'] = $this->user->id;
        $data['targetuserfullname'] = fullname($this->user);
        $data['targetuserpicture'] = user::get_user_image_or_avatar($this->user);
        $data['userhasgrade'] = $gradeutil->user_has_grade($this->evokeportfolio, $this->user->id);

        $submissionutil = new submission();
        $data['submissions'] = $submissionutil->get_user_submissions($this->context, $this->evokeportfolio, $this->user->id);

        $grouputil = new group();
        $data['usergroup'] = $grouputil->get_user_groups_names($this->evokeportfolio->course, $this->user->id);

        return $data;
    }
}
