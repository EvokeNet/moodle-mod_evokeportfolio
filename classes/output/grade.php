<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\group;
use renderable;
use templatable;
use renderer_base;

/**
 * Grade renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class grade implements renderable, templatable {

    public $evokeportfolio;
    public $context;
    public $form;
    public $user;
    public $group;

    public function __construct($evokeportfolio, $context, $form, $userid = null, $groupid = null) {
        global $DB;

        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;
        $this->form = $form;

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
        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'course' => $this->evokeportfolio->course,
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'form' => $this->form->render()
        ];

        if ($this->evokeportfolio->groupactivity) {
            $groupsutil = new group();

            $data['groupid'] = $this->group->id;
            $data['groupname'] = $this->group->name;
            $data['groupmembers'] = $groupsutil->get_group_members($this->group->id);

            $groupgradingmodetext = get_string('groupgrading', 'mod_evokeportfolio');
            if ($this->evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
                $groupgradingmodetext = get_string('individualgrading', 'mod_evokeportfolio');
            }
            $data['groupgradingmodetext'] = $groupgradingmodetext;

            return $data;
        }

        $data['userid'] = $this->user->id;
        $data['userfullname'] = fullname($this->user);

        return $data;
    }
}
