<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Evoke timeline class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class timeline {
    public $courseid;
    private $portfoliocontexts = [];

    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    public function loadmy($portfolioid) {
        global $DB, $USER;

        $portfolio = $DB->get_record('evokeportfolio', ['id' => $portfolioid], '*', MUST_EXIST);

        $chapterutil = new chapter();
        $submissionutil = new submission();

        // Chapters data.
        $chapters = $chapterutil->get_course_chapters($this->courseid);

        if (!$chapters) {
            return [
                'courseid' => $this->courseid
            ];
        }

        $mysubmissions = $submissionutil->get_portfolio_submissions($portfolio, $this->get_portfolio_context($portfolio->id), $USER->id);

        $userpicture = user::get_user_image_or_avatar($USER);

        return [
            'userpicture' => $userpicture,
            'userfullname' => fullname($USER),
            'submissions' => $mysubmissions,
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
