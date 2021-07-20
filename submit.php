<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Submits an portfolio entry.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$urlparams = ['id' => $cm->id];
$url = new moodle_url('/mod/evokeportfolio/submit.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

require_once('submit_form.php');

$formdata = [
    'cmid' => $cm->id
];

$form = new mod_evokeportfolio_submit_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/view.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        $data = clone $formdata;

        unset($data->submitbutton);

        foreach ($data as $key => $grade) {
            $gradeitemid = substr(strrchr($key, "gradeitem-"), 10);

            $data = new \stdClass();
            $data->itemid = $gradeitemid;
            $data->userid = $USER->id;
            $data->grade = $grade;
            $data->timecreated = time();
            $data->timemodified = time();

            $DB->insert_record('proa_grade_grades', $data);
        }

        $url = new moodle_url('/mod/competencyself/view.php', $urlparams);

        redirect($url, 'Avaliação enviada com sucesso.', null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $form->display();

    echo $OUTPUT->footer();
}