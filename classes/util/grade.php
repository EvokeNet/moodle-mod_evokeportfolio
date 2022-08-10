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

        $finalgrade = $formdata->grade;
        if ($evokeportfolio->grade > 0 && $finalgrade > $evokeportfolio->grade) {
            $finalgrade = $evokeportfolio->grade;
        }

        $grades[$formdata->userid] = new \stdClass();
        $grades[$formdata->userid]->userid = $formdata->userid;
        $grades[$formdata->userid]->rawgrade = $finalgrade;

        return $grades;
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

    public function get_user_grade_string($evokeportfolio, $userid) {
        global $DB;

        $usergrade = $this->get_user_grade($evokeportfolio, $userid);

        if (!$usergrade) {
            return false;
        }

        if ($evokeportfolio->grade > 0) {
            return (int)$usergrade;
        }

        $scale = $DB->get_record('scale', ['id' => abs($evokeportfolio->grade)], '*', MUST_EXIST);

        $scales = explode(',', $scale->scale);

        $scaleindex = (int)$usergrade - 1;

        return $scales[$scaleindex];
    }

    public function group_has_grade($evokeportfolio, $groupid) {
        $groupsutil = new group();
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
        $groupsutil = new group();
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

    public function get_user_chapter_grade($userid, $chapterid) {
        global $DB;

        $grade = $DB->get_record('evokeportfolio_chaptergrades', [
            'userid' => $userid,
            'chapterid' => $chapterid
        ]);

        if (!$grade) {
            return false;
        }

        return $grade;
    }

    public function user_has_chapter_grade($userid, $chapterid) {
        $grade = $this->get_user_chapter_grade($userid, $chapterid);

        if (!$grade) {
            return false;
        }

        return true;
    }

    public function process_user_chapter_grade($userid, $chapterid, $grade) {
        global $DB;

        $chapter = $DB->get_record('evokeportfolio_chapters', ['id' => $chapterid], '*', MUST_EXIST);

        $chapterutil = new chapter();

        $portfolios = $chapterutil->get_chapter_portfolios($chapter);

        if (!$portfolios) {
            return false;
        }

        $dbgrade = $this->add_update_user_chapter_grade($userid, $chapterid, $grade);

        $this->update_chapter_portfolios_moodle_grades($portfolios, $userid, $grade);

        return $dbgrade;
    }

    public function add_update_user_chapter_grade($userid, $chapterid, $grade) {
        global $DB;

        $dbgrade = $this->get_user_chapter_grade($userid, $chapterid);

        if ($dbgrade) {
            $dbgrade->grade = $grade;

            $DB->update_record('evokeportfolio_chaptergrades', $dbgrade);

            return $dbgrade;
        }

        $grade = new \stdClass();
        $grade->chapterid = $chapterid;
        $grade->userid = $userid;
        $grade->grade = $grade;
        $grade->timecreated = time();
        $grade->timemodified = time();

        $gradeid = $DB->insert_record('evokeportfolio_chaptergrades', $grade);

        $grade->id = $gradeid;

        return $grade;
    }

    public function update_chapter_portfolios_moodle_grades($portfolios, $userid, $grade) {
        global $CFG;

        $grades[$userid] = new \stdClass();
        $grades[$userid]->userid = $userid;
        $grades[$userid]->rawgrade = $grade;

        require_once($CFG->libdir . '/gradelib.php');

        foreach ($portfolios as $portfolio) {
            $this->update_evokeportfolio_grades($portfolio->id, $grades);

            grade_update('mod/evokeportfolio', $portfolio->course, 'mod', 'evokeportfolio', $portfolio->id, 0, $grades);
        }
    }

    public function grade_user_portfolio($portfolio, $userid, $grade) {
        global $CFG;

        $grades[$userid] = new \stdClass();
        $grades[$userid]->userid = $userid;
        $grades[$userid]->rawgrade = $grade;

        $this->update_evokeportfolio_grades($portfolio->id, $grades);

        require_once($CFG->libdir . '/gradelib.php');

        grade_update('mod/evokeportfolio', $portfolio->course, 'mod', 'evokeportfolio', $portfolio->id, 0, $grades);
    }

    public function can_user_grade($context, $usercoursegroups = null, $userid = null) {
        global $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        if (is_siteadmin($userid)) {
            return true;
        }

        if (!has_capability('mod/evokeportfolio:grade', $context, $userid)) {
            return false;
        }

        if (!$usercoursegroups) {
            return false;
        }

        $groupsutil = new group();

        $groups = [];
        foreach ($usercoursegroups as $usercoursegroup) {
            $ngroup = new \stdClass();
            $ngroup->id = $usercoursegroup;

            $groups[] = $ngroup;
        }

        $groupsmembers = $groupsutil->get_groups_members($groups, false);

        foreach ($groupsmembers as $groupsmember) {
            if ($groupsmember->id == $userid) {
                return true;
            }
        }

        return false;
    }
}
