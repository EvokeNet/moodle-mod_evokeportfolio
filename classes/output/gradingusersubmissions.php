<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use mod_evokeportfolio\util\chapter;
use mod_evokeportfolio\util\group;
use renderable;
use templatable;
use renderer_base;

/**
 * Manage grading users submission renderable class.
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

        $userpicture = new \user_picture($this->user);
        $userpicture->size = 1;

        $grouputil = new group();
        $usergroups = $grouputil->get_user_groups_names($this->course->id, $this->user->id);

        $chapterutil = new chapter();

        $portfolioswithusersubmissions = $chapterutil->get_portfolios_with_user_submissions($this->chapter, $this->user);

        $data = [
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'chapterid' => $this->chapter->id,
            'chaptername' => $this->chapter->name,
            'userid' => $this->user->id,
            'userfullname' => fullname($this->user),
            'userpicture' => $userpicture->get_url($PAGE)->out(),
            'usergroup' => $usergroups,
            'portfolioswithusersubmissions' => $portfolioswithusersubmissions
        ];

        return $data;
    }
}
