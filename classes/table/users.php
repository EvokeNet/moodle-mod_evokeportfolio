<?php

namespace mod_evokeportfolio\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use mod_evokeportfolio\util\evokeportfolio;
use mod_evokeportfolio\util\grade;
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
        $groupname = $this->get_user_group($data->id, $this->context->instanceid);

        if (!$groupname) {
            return '';
        }

        return $groupname;
    }

    private function get_user_group($userid, $courseid) {
        global $DB;

        $sql = 'SELECT g.name FROM {groups_members} gm
                INNER JOIN {groups} g ON g.id = gm.groupid
                WHERE gm.userid = :userid AND g.courseid = :courseid';

        $records = $DB->get_records_sql($sql, ['userid' => $userid, 'courseid' => $courseid]);

        if (!$records) {
            return false;
        }

        $firstgroup = current($records);

        return $firstgroup->name;
    }

    public function col_status($data) {
        $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php');

        $statuscontent = html_writer::link($url, get_string('viewsubmission', 'mod_evokeportfolio'), ['class' => 'btn btn-primary btn-sm']);

        return $statuscontent;

        return html_writer::span(get_string('notsubmitted', 'mod_evokeportfolio'), 'badge badge-dark');
    }

    private function get_columns() {
        return ['id', 'fullname', 'email', 'group', 'status'];
    }

    private function get_headers() {
        return ['ID', get_string('fullname'), 'E-mail', get_string('group'), get_string('status', 'mod_evokeportfolio')];
    }
}