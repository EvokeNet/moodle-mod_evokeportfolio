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

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

class grade {
    public function process_grade_form($evokeportfolio, $formdata) {
        global $CFG;

        $grades = $this->get_grades_from_form($evokeportfolio, $formdata);

        if (!$grades) {
            return;
        }

        require_once($CFG->libdir . '/gradelib.php');

        grade_update('mod/evokeportfolio', $evokeportfolio->course, 'mod', 'evokeportfolio', $evokeportfolio->id, 0, $grades);
    }

    private function get_grades_from_form($evokeportfolio, $formdata) {
        $grades = [];

        if (!$evokeportfolio->groupactivity) {
            $finalgrade = $formdata->grade;
            if ($evokeportfolio->grade > 0 && $finalgrade > $evokeportfolio->grade) {
                $finalgrade = $evokeportfolio->grade;
            }

            $grades[$formdata->userid] = new \stdClass();
            $grades[$formdata->userid]->userid = $formdata->userid;
            $grades[$formdata->userid]->rawgrade = $finalgrade;

            return $grades;
        }

        if ($evokeportfolio->groupactivity) {
            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_GROUP) {
                $groupsutil = new groups();
                $groupmembers = $groupsutil->get_group_members($formdata->groupid);

                if (!$groupmembers) {
                    return false;
                }

                $finalgrade = $formdata->grade;
                if ($evokeportfolio->grade > 0 && $finalgrade > $evokeportfolio->grade) {
                    $finalgrade = $evokeportfolio->grade;
                }

                foreach ($groupmembers as $user) {
                    $grades[$user->id] = new \stdClass();
                    $grades[$user->id]->userid = $user->id;
                    $grades[$user->id]->rawgrade = $finalgrade;
                }

                return $grades;
            }

            if ($evokeportfolio->groupgradingmode == MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL) {
                unset($formdata->groupid);

                foreach ($formdata as $key => $usergrade) {
                    $userid = substr(strrchr($key, "gradeuserid-"), 12);

                    if (!$userid) {
                        continue;
                    }

                    $finalgrade = $usergrade;
                    if ($evokeportfolio->grade > 0 && $finalgrade > $evokeportfolio->grade) {
                        $finalgrade = $evokeportfolio->grade;
                    }

                    $grades[$userid] = new \stdClass();
                    $grades[$userid]->userid = $userid;
                    $grades[$userid]->rawgrade = $finalgrade;
                }

                return $grades;
            }
        }
    }

    public function get_grade_item($iteminstance, $courseid) {
        global $CFG;

        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->libdir . '/grade/grade_item.php');

        $gradeitem = \grade_item::fetch_all([
            'itemtype' => 'mod',
            'itemmodule' => 'evokeportfolio',
            'iteminstance' => $iteminstance,
            'courseid' => $courseid
        ]);

        if (empty($gradeitem)) {
            return false;
        }

        return current($gradeitem);
    }

    public function is_gradeitem_locked($iteminstance, $courseid) {
        $gradeitem = $this->get_grade_item($iteminstance, $courseid);

        if (!$gradeitem) {
            return false;
        }

        return $gradeitem->is_locked();
    }

    public function student_has_grade($evokeportfolio, $userid) {
        $usergrade = $this->get_student_grade($evokeportfolio, $userid);

        if ($usergrade) {
            return true;
        }

        return false;
    }

    public function get_student_grade($evokeportfolio, $userid) {
        global $DB;

        $gradeitem = $this->get_grade_item($evokeportfolio->id, $evokeportfolio->course);

        if (!$gradeitem) {
            return false;
        }

        $gradegrade = $DB->get_record('grade_grades',
            [
                'itemid' => $gradeitem->id,
                'userid' => $userid
            ]
        );

        if (!$gradegrade) {
            return false;
        }

        return $gradegrade->finalgrade;
    }

    public function group_has_grade($evokeportfolio, $groupid) {
        $groupsutil = new groups();
        $groupmembers = $groupsutil->get_group_members($groupid, false);

        if (!$groupmembers) {
            return false;
        }

        foreach ($groupmembers as $user) {
            // All users from the group need to have a grade.
            if (!$this->student_has_grade($evokeportfolio, $user->id)) {
                return false;
            }
        }

        return true;
    }
}
