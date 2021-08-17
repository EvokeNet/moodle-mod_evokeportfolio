<?php

namespace mod_evokeportfolio\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

/**
 * Main portfolio's renderer.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer the instance in course to template.
     *
     * @param renderable $page
     *
     * @return bool|string
     *
     * @throws \moodle_exception
     */
    public function render_view(renderable $page) {
        $data = $page->export_for_template($this);

        if (has_capability('mod/evokeportfolio:grade', $page->context)) {
            return parent::render_from_template('mod_evokeportfolio/view_admin', $data);
        }

        return parent::render_from_template('mod_evokeportfolio/view', $data);
    }

    /**
     * Defer the instance in course to template.
     *
     * @param renderable $page
     *
     * @return bool|string
     *
     * @throws \moodle_exception
     */
    public function render_submissions(renderable $page) {
        $data = $page->export_for_template($this);

        if ($data['groupactivity']) {
            return parent::render_from_template('mod_evokeportfolio/submission_group', $data);
        }

        return parent::render_from_template('mod_evokeportfolio/submission_individual', $data);
    }

    /**
     * Defer the instance in course to template.
     *
     * @param renderable $page
     *
     * @return bool|string
     *
     * @throws \moodle_exception
     */
    public function render_viewsubmission(renderable $page) {
        $data = $page->export_for_template($this);

        if ($data['groupactivity']) {
            return parent::render_from_template('mod_evokeportfolio/viewsubmission_group', $data);
        }

        return parent::render_from_template('mod_evokeportfolio/viewsubmission_individual', $data);
    }
}
