<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\user;
use renderable;
use templatable;
use renderer_base;

/**
 * Manage sections renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class gradingusersubmissions implements renderable, templatable {

    protected $course;
    protected $context;
    protected $chapter;
    protected $user;

    public function __construct($course, $context, $chapter, $user) {
        $this->course = $course;
        $this->context = $context;
        $this->chapter = $chapter;
        $this->user = $user;
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
        global $PAGE;

        $util = new evokeportfolio();

        $userpicture = new \user_picture($this->user);
        $userpicture->size = 1;

        $userutil = new user();
        $usergroup = $userutil->get_user_group($this->user->id, $this->course->id);

        $chapterutil = new chapter();

        $portfolioswithusersubmissions = $chapterutil->get_portfolios_with_user_submissions($this->context, $this->chapter, $this->user);

        $data = [
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'chapterid' => $this->chapter->id,
            'chaptername' => $this->chapter->name,
            'userid' => $this->user->id,
            'userfullname' => fullname($this->user),
            'userpicture' => $userpicture->get_url($PAGE)->out(),
            'usergroup' => $usergroup ? $usergroup->name : false,
            'portfolioswithusersubmissions' => $portfolioswithusersubmissions
        ];

        return $data;
    }
}
