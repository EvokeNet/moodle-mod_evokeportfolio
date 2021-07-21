<?php
// This file is part of BBCalendar block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\groups;
use renderable;
use templatable;
use renderer_base;

/**
 * Competency Self Assessment renderable class.
 *
 * @copyright  2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        global $USER;

        $timeremaining = $this->evokeportfolio->datelimit - time();

        $isdelayed = true;
        if ($timeremaining > 0) {
            $isdelayed = false;
        }

        $groupgradingmodetext = get_string('groupgrading', 'mod_evokeportfolio');
        if ($this->evokeportfolio->groupgradingmode == 2) {
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

        // Student.
        $evokeportfolioutil = new evokeportfolio();

        if ($this->evokeportfolio->groupactivity) {
            $usercoursegroup = $groupsutil->get_user_group($this->evokeportfolio->course);

            $data['hasgroup'] = !empty($usercoursegroup);
            $data['hassubmission'] = false;
            if ($usercoursegroup) {
                $data['groupname'] = $usercoursegroup->name;
                $data['groupmembers'] = $groupsutil->get_group_members($usercoursegroup->id);

                $data['hassubmission'] = $evokeportfolioutil->has_submission($this->context->instanceid, $USER->id, $usercoursegroup->id);
            }

            return $data;
        }

        $data['hassubmission'] = $evokeportfolioutil->has_submission($this->context->instanceid, $USER->id);

        return $data;
    }
}
