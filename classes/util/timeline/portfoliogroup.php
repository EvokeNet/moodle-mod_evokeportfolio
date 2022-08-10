<?php

namespace mod_evokeportfolio\util\timeline;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\group;
use mod_evokeportfolio\util\submission;
use mod_evokeportfolio\util\user;

/**
 * Evoke portfolio timeline class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class portfoliogroup {
    public $courseid;
    private $portfoliocontexts = [];

    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    public function loadteam($portfolioid, $offset = 0) {
        global $DB, $USER, $PAGE;

        $portfolio = $DB->get_record('evokeportfolio', ['id' => $portfolioid], '*', MUST_EXIST);

        $submissionutil = new submission();

        $groupsutil = new group();

        $usercoursegroups = $groupsutil->get_user_groups($this->courseid);

        $groupsmembers = $groupsutil->get_groups_members($usercoursegroups, true, $PAGE->context);

        $teamsubmissions = [];
        if ($usercoursegroups) {
            $teamsubmissions = $submissionutil->get_user_groups_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), $groupsmembers, 10, $offset);

            if ($teamsubmissions) {
                foreach ($teamsubmissions as $teamsubmission) {
                    $teamsubmission->group = current($usercoursegroups);
                    $teamsubmission->groupmembers = $groupsmembers;
                }
            }
        }

        $userpicture = user::get_user_image_or_avatar($USER);

        $data = [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $teamsubmissions,
            'hasmoreitems' => !empty($teamsubmissions),
            'cangrade' => has_capability('mod/evokeportfolio:grade', $this->get_portfolio_context($portfolioid)),
            'isevaluated' => $portfolio->grade != 0,
        ];

        return $data;
    }

    public function loadnetwork($portfolioid, $offset = 0) {
        global $DB, $USER;

        $portfolio = $DB->get_record('evokeportfolio', ['id' => $portfolioid], '*', MUST_EXIST);

        $submissionutil = new submission();

        $networksubmissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), null, null, 10, $offset);

        $userpicture = user::get_user_image_or_avatar($USER);

        if ($networksubmissions) {
            $groupsutil = new group();

            foreach ($networksubmissions as $submission) {
                $usercoursegroups = $groupsutil->get_user_groups($this->courseid, $submission->userid);

                $groupsmembers = $groupsutil->get_groups_members($usercoursegroups);

                $submission->group = current($usercoursegroups);
                $submission->groupmembers = $groupsmembers;
            }
        }

        $data = [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $networksubmissions,
            'hasmoreitems' => !empty($networksubmissions)
        ];

        return $data;
    }

    private function get_portfolio_context($portfolioid) {
        if (isset($this->portfoliocontexts[$portfolioid])) {
            return $this->portfoliocontexts[$portfolioid];
        }

        $coursemodule = get_coursemodule_from_instance('evokeportfolio', $portfolioid);

        $this->portfoliocontexts[$portfolioid] = \context_module::instance($coursemodule->id);

        return $this->portfoliocontexts[$portfolioid];
    }
}
