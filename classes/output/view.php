<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\groups;
use renderable;
use templatable;
use renderer_base;

/**
 * View renderable class.
 *
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class view implements renderable, templatable {

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
        $timeremaining = $this->evokeportfolio->datelimit - time();

        $isdelayed = true;
        if ($timeremaining > 0) {
            $isdelayed = false;
        }

        $groupgradingmodetext = get_string('groupgrading', 'mod_evokeportfolio');
        if ($this->evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
            $groupgradingmodetext = get_string('individualgrading', 'mod_evokeportfolio');
        }

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'intro' => format_module_intro('evokeportfolio', $this->evokeportfolio, $this->context->instanceid),
            'datelimit' => userdate($this->evokeportfolio->datelimit),
            'timeremaining' => format_time($timeremaining),
            'cmid' => $this->context->instanceid,
            'course' => $this->evokeportfolio->course,
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'groupgradingmodetext' => $groupgradingmodetext,
            'isdelayed' => $isdelayed
        ];

        $groupsutil = new groups();

        // Teacher.
        if (has_capability('mod/evokeportfolio:grade', $this->context)) {
            $coursemodule = get_coursemodule_from_instance('evokeportfolio', $this->evokeportfolio->id);
            $data['hide'] = $coursemodule->visible;

            $participants = count_enrolled_users($this->context, 'mod/evokeportfolio:submit');
            $data['participants'] = $participants;

            if ($this->evokeportfolio->groupactivity) {
                $data['groupscount'] = $groupsutil->get_total_groups_in_course($this->evokeportfolio->course);
            }

            return $data;
        }

        if ($this->evokeportfolio->groupactivity) {
            $usercoursegroup = $groupsutil->get_user_group($this->evokeportfolio->course);

            $data['hasgroup'] = !empty($usercoursegroup);
            $data['hassubmission'] = false;
            if ($usercoursegroup) {
                $data['groupname'] = $usercoursegroup->name;
                $data['groupmembers'] = $groupsutil->get_group_members($usercoursegroup->id);
            }

            return $data;
        }

        return $data;
    }
}
