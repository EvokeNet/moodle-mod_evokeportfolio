<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Grade utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class grade {
    public function process_grade_form($evokeportfolio, $formdata) {
        global $CFG;

        $grades = $this->get_grades_from_form($evokeportfolio, $formdata);

        if (!$grades) {
            return;
        }

        $this->update_evokeportfolio_grades($evokeportfolio->id, $grades);

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

    private function update_evokeportfolio_grades($portfolioid, $grades) {
        global $DB, $USER;

        foreach ($grades as $grade) {
            $usergrade = $this->get_evokeportfolio_grade($portfolioid, $grade->userid);

            if ($usergrade) {
                $usergrade->grader = $USER->id;
                $usergrade->grade = $grade->rawgrade;
                $usergrade->timemodified = time();

                $DB->update_record('evokeportfolio_grades', $usergrade);

                continue;
            }

            $usergrade = new \stdClass();
            $usergrade->portfolioid = $portfolioid;
            $usergrade->userid = $grade->userid;
            $usergrade->grader = $USER->id;
            $usergrade->grade = $grade->rawgrade;
            $usergrade->timecreated = time();
            $usergrade->timemodified = time();

            $DB->insert_record('evokeportfolio_grades', $usergrade);
        }
    }

    private function get_evokeportfolio_grade($portfolioid, $userid) {
        global $DB;

        $grade = $DB->get_record('evokeportfolio_grades', [
            'portfolioid' => $portfolioid,
            'userid' => $userid
        ]);

        if ($grade) {
            return $grade;
        }

        return false;
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

    public function user_has_grade($evokeportfolio, $userid) {
        $usergrade = $this->get_user_grade($evokeportfolio, $userid);

        if ($usergrade) {
            return true;
        }

        return false;
    }

    public function get_user_grade($evokeportfolio, $userid) {
        global $DB;

        if ($evokeportfolio->grade == 0) {
            return false;
        }

        $usergrade = $DB->get_record('evokeportfolio_grades',
            [
                'portfolioid' => $evokeportfolio->id,
                'userid' => $userid
            ]
        );

        if (!$usergrade) {
            return false;
        }

        return $usergrade->grade;
    }

    public function group_has_grade($evokeportfolio, $groupid) {
        $groupsutil = new groups();
        $groupmembers = $groupsutil->get_group_members($groupid, false);

        if (!$groupmembers) {
            return false;
        }

        foreach ($groupmembers as $user) {
            // All users from the group need to have a grade.
            if (!$this->user_has_grade($evokeportfolio, $user->id)) {
                return false;
            }
        }

        return true;
    }

    public function get_group_grade($evokeportfolio, $groupid) {
        $groupsutil = new groups();
        $groupmembers = $groupsutil->get_group_members($groupid, false);

        if (!$groupmembers) {
            return false;
        }

        $groupgrade = false;
        foreach ($groupmembers as $user) {
            // All users from the group need to have a grade.
            $usergrade = $this->get_user_grade($evokeportfolio, $user->id);
            if (!$usergrade) {
                return false;
            }

            $groupgrade = $usergrade;
        }

        return $groupgrade;
    }
}
