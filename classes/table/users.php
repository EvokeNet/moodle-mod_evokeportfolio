<?php

namespace mod_evokeportfolio\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use mod_evokeportfolio\util\grade;
use mod_evokeportfolio\util\group;
use table_sql;
use moodle_url;
use html_writer;

/**
 * Entries table class
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class users extends table_sql {

    protected $context;
    protected $chapter;

    public function __construct($uniqueid, $context, $chapter) {
        parent::__construct($uniqueid);

        $this->context = $context;
        $this->chapter = $chapter;

        $this->define_columns($this->get_columns());

        $this->define_headers($this->get_headers());

        $this->no_sorting('status');

        $this->no_sorting('group');

        $this->define_baseurl(new moodle_url('/mod/evokeportfolio/gradingusers.php', ['id' => $chapter->id]));

        $this->base_sql();

        $this->set_attribute('class', 'table table-bordered table-entries');
    }

    public function base_sql() {
        $fields = 'DISTINCT u.id, u.firstname, u.lastname, u.email';

        $capjoin = get_enrolled_with_capabilities_join($this->context, '', 'mod/evokeportfolio:submit');

        $from = ' {user} u ' . $capjoin->joins;

        $this->set_sql($fields, $from, $capjoin->wheres, $capjoin->params);
    }

    public function col_fullname($user) {
        return $user->firstname . ' ' . $user->lastname;
    }

    public function col_group($data) {
        $grouputil = new group();

        return $grouputil->get_user_groups_names($this->context->instanceid, $data->id);
    }

    public function col_status($data) {
        $url = new moodle_url('/mod/evokeportfolio/gradingusersubmissions.php', ['id' => $this->chapter->id, 'userid' => $data->id]);

        $statuscontent = html_writer::link($url, get_string('page_view_submissions', 'mod_evokeportfolio'), ['class' => 'btn btn-primary btn-sm']);

        $gradeutil = new grade();
        if ($gradeutil->user_has_chapter_grade($data->id, $this->chapter->id)) {
            $statuscontent .= html_writer::span(get_string('graded', 'mod_evokeportfolio'), 'badge badge-success ml-2 p-2');
        }

        return $statuscontent;
    }

    private function get_columns() {
        return ['id', 'fullname', 'email', 'group', 'status'];
    }

    private function get_headers() {
        return ['ID', get_string('fullname'), 'E-mail', get_string('group'), get_string('status', 'mod_evokeportfolio')];
    }
}