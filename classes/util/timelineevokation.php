<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Evoke timeline class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class timelineevokation {
    public $courseid;
    private $portfoliocontexts = [];

    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    public function loadmy($offset = 0) {
        global $USER;

        $portfolioutil = new evokeportfolio();
        $submissionutil = new submission();

        $portfolios = $portfolioutil->get_course_portfolio_instances($this->courseid, 1);

        $userpicture = user::get_user_image_or_avatar($USER);

        if (!$portfolios) {
            return [
                'userpicture' => $userpicture,
                'userfullname' => fullname($USER),
                'submissions' => [],
                'hasmoreitems' => false
            ];
        }

        foreach ($portfolios as $key => $portfolio) {
            $portfolio->context = $this->get_portfolio_context($portfolio->id);
            $portfolio->isevaluated = $portfolio->grade != 0;


            $portfolios[$portfolio->id] = $portfolio;
            unset($portfolios[$key]);
        }

        $submissions = $submissionutil->get_evokation_submissions($portfolios, $USER->id, null, 10, $offset);

        return [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $submissions,
            'hasmoreitems' => !empty($submissions)
        ];
    }

    public function loadteam($offset = 0) {
        global $USER;

        $portfolioutil = new evokeportfolio();
        $submissionutil = new submission();

        $portfolios = $portfolioutil->get_course_portfolio_instances($this->courseid, 1);

        $userpicture = user::get_user_image_or_avatar($USER);

        if (!$portfolios) {
            return [
                'userpicture' => $userpicture,
                'userfullname' => fullname($USER),
                'submissions' => [],
                'hasmoreitems' => false
            ];
        }

        foreach ($portfolios as $key => $portfolio) {
            $portfolio->context = $this->get_portfolio_context($portfolio->id);
            $portfolio->isevaluated = $portfolio->grade != 0;


            $portfolios[$portfolio->id] = $portfolio;
            unset($portfolios[$key]);
        }

        $groupsutil = new group();

        $usercoursegroups = $groupsutil->get_user_groups($this->courseid);

        $submissions = $submissionutil->get_evokation_submissions($portfolios, null, $usercoursegroups, 10, $offset);

        return [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $submissions,
            'hasmoreitems' => !empty($submissions)
        ];
    }

    public function loadnetwork($offset = 0) {
        global $USER;

        $portfolioutil = new evokeportfolio();
        $submissionutil = new submission();

        $portfolios = $portfolioutil->get_course_portfolio_instances($this->courseid, 1);

        $userpicture = user::get_user_image_or_avatar($USER);

        if (!$portfolios) {
            return [
                'userpicture' => $userpicture,
                'userfullname' => fullname($USER),
                'submissions' => [],
                'hasmoreitems' => false
            ];
        }

        foreach ($portfolios as $key => $portfolio) {
            $portfolio->context = $this->get_portfolio_context($portfolio->id);
            $portfolio->isevaluated = $portfolio->grade != 0;


            $portfolios[$portfolio->id] = $portfolio;
            unset($portfolios[$key]);
        }

        $submissions = $submissionutil->get_evokation_submissions($portfolios, null, null, 10, $offset);

        return [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $submissions,
            'hasmoreitems' => !empty($submissions)
        ];
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
