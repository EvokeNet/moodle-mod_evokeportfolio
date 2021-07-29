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
        $gradeutil = new \mod_evokeportfolio\util\grade();

        $data = [
            'id' => $this->evokeportfolio->id,
            'name' => $this->evokeportfolio->name,
            'cmid' => $this->context->instanceid,
            'course' => $this->evokeportfolio->course,
            'groupactivity' => $this->evokeportfolio->groupactivity,
            'isgradinglocked' => $gradeutil->is_gradeitem_locked($this->evokeportfolio->id, $this->evokeportfolio->course),
        ];

        $evokeportfolioutil = new evokeportfolio();

        if ($this->evokeportfolio->groupactivity) {
            $groupsutil = new groups();

            $data['hassubmission'] = false;
            $data['submissions'] = false;

            $data['groupid'] = $this->group->id;
            $data['groupname'] = $this->group->name;
            $data['groupmembers'] = $groupsutil->get_group_members($this->group->id);

            $data['hassubmission'] = $evokeportfolioutil->has_submission($this->context->instanceid, null, $this->group->id);
            $data['submissions'] = $evokeportfolioutil->get_submissions($this->context, null, $this->group->id);

            $data['grouphasgrade'] = $gradeutil->group_has_grade($this->evokeportfolio, $this->group->id);

            return $data;
        }

        $data['userid'] = $this->user->id;
        $data['userfullname'] = fullname($this->user);
        $data['userhasgrade'] = $gradeutil->student_has_grade($this->evokeportfolio, $this->user->id);

        $data['submissions'] = $evokeportfolioutil->get_submissions($this->context, $this->user->id);

        return $data;
    }
}
