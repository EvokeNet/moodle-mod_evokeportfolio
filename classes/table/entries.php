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
class entries extends table_sql {

    protected $evokeportfolio;
    protected $context;
    protected $coursemodule;

    public function __construct($uniqueid, $context, $evokeportfolio, $coursemodule) {
        parent::__construct($uniqueid);

        $this->evokeportfolio = $evokeportfolio;
        $this->context = $context;
        $this->coursemodule = $coursemodule;

        $this->define_columns($this->get_evokeportfolio_columns());

        $this->define_headers($this->get_evokeportfolio_headers());

        $this->no_sorting('status');

        $this->define_baseurl(new moodle_url('/mod/evokeportfolio/entries.php', ['id' => $coursemodule->id]));

        $this->base_sql();

        $this->set_attribute('class', 'table table-bordered table-entries');
    }

    public function base_sql() {
        if ($this->evokeportfolio->groupactivity) {
            $fields = 'DISTINCT g.id, g.name';

            $from = ' {groups} g ';

            $where = ' g.courseid = :courseid';

            $params['courseid'] = $this->evokeportfolio->course;

            $this->set_sql($fields, $from, $where, $params);

            return;
        }

        $fields = 'DISTINCT u.id, u.firstname, u.lastname, u.email';

        $capjoin = get_enrolled_with_capabilities_join($this->context, '', 'mod/evokeportfolio:submit');

        $from = ' {user} u ' . $capjoin->joins;

        $this->set_sql($fields, $from, $capjoin->wheres, $capjoin->params);
    }

    public function col_status($data) {
        $evokeportfolioutil = new evokeportfolio();
        $gradeutil = new grade();

        if ($this->evokeportfolio->groupactivity) {
            if ($evokeportfolioutil->has_submission($this->evokeportfolio->id, null, $data->id)) {
                $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', ['id' => $this->coursemodule->id, 'groupid' => $data->id]);

                $statuscontent = html_writer::link($url, get_string('viewsubmission', 'mod_evokeportfolio'), ['class' => 'btn btn-primary btn-sm']);

                if ($gradeutil->group_has_grade($this->evokeportfolio, $data->id)) {
                    $statuscontent .= html_writer::span(get_string('evaluated', 'mod_evokeportfolio'), 'badge badge-success ml-2 p-2');
                }

                return $statuscontent;
            }
        }

        if (!$this->evokeportfolio->groupactivity) {
            if ($evokeportfolioutil->has_submission($this->evokeportfolio->id, $data->id)) {
                $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', ['id' => $this->coursemodule->id, 'userid' => $data->id]);

                $statuscontent = html_writer::link($url, get_string('viewsubmission', 'mod_evokeportfolio'), ['class' => 'btn btn-primary btn-sm']);

                if ($gradeutil->user_has_grade($this->evokeportfolio, $data->id)) {
                    $statuscontent .= html_writer::span(get_string('evaluated', 'mod_evokeportfolio'), 'badge badge-success ml-2 p-2');
                }

                return $statuscontent;
            }
        }

        return html_writer::span(get_string('notsubmitted', 'mod_evokeportfolio'), 'badge badge-dark');
    }

    private function get_evokeportfolio_columns() {
        if ($this->evokeportfolio->groupactivity) {
            return ['id', 'name', 'status'];
        }

        return ['id', 'firstname', 'lastname', 'email', 'status'];
    }

    private function get_evokeportfolio_headers() {
        if ($this->evokeportfolio->groupactivity) {
            return ['ID', get_string('group'), 'Status'];
        }

        return ['ID', get_string('firstname'), get_string('lastname'), 'E-mail', 'Status'];
    }
}