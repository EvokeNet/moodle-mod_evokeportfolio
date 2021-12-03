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
            return $this->render_from_template('mod_evokeportfolio/view_admin', $data);
        }

        return $this->render_from_template('mod_evokeportfolio/view', $data);
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
    public function render_section(renderable $page) {
        $data = $page->export_for_template($this);

        if ($data['groupactivity']) {
            return $this->render_from_template('mod_evokeportfolio/section_group', $data);
        }

        return $this->render_from_template('mod_evokeportfolio/section_individual', $data);
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
            return $this->render_from_template('mod_evokeportfolio/viewsubmission_group', $data);
        }

        return $this->render_from_template('mod_evokeportfolio/viewsubmission_individual', $data);
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
    public function render_indexadminfilters(renderable $page) {
        $data = $page->export_for_template($this);

        return $this->render_from_template('mod_evokeportfolio/indexadminfilters', $data);
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
    public function render_indexfilters(renderable $page) {
        $data = $page->export_for_template($this);

        return $this->render_from_template('mod_evokeportfolio/indexfilters', $data);
    }
}
