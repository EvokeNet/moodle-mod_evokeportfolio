<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Base class for the table.
 *
 * @copyright  2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_evokeportfolio\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use mod_evokeportfolio\util\evokeportfolio;
use table_sql;
use moodle_url;
use html_writer;

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
        if ($this->evokeportfolio->groupactivity) {
            if ($evokeportfolioutil->has_submission($this->coursemodule->id, null, $data->id)) {
                $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', ['id' => $this->coursemodule->id, 'groupid' => $data->id]);

                return html_writer::link($url, 'Ver envio', ['class' => 'btn btn-primary btn-sm']);
            }
        }

        if (!$this->evokeportfolio->groupactivity) {
            if ($evokeportfolioutil->has_submission($this->coursemodule->id, $data->id)) {
                $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', ['id' => $this->coursemodule->id, 'userid' => $data->id]);

                return html_writer::link($url, 'Ver envio', ['class' => 'btn btn-primary btn-sm']);
            }
        }

        return html_writer::span('Não enviado', 'badge badge-dark');
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