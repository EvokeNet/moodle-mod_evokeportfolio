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
 * Submits an portfolio comment.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', '', PARAM_INT);
$groupid = optional_param('groupid', '', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);

if ($userid) {
    $evaluateduser = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
}

if ($groupid) {
    $evaluatedgroup = $DB->get_record('groups', ['id' => $groupid], '*', MUST_EXIST);
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$urlparams = ['id' => $cm->id];
$url = new moodle_url('/mod/evokeportfolio/submit.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$formdata = [
    'cmid' => $cm->id
];

if ($evokeportfolio->groupactivity) {
    $formdata['groupid'] = $groupid;
} else {
    $formdata['userid'] = $userid;
}

$form = new \mod_evokeportfolio\forms\comment_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/view.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        unset($formdata->submitbutton);

        $data = new \stdClass();
        $data->cmid = $cm->id;
        $data->postedby = $USER->id;
        $data->role = 1;
        $data->timecreated = time();
        $data->timemodified = time();

        if (isset($formdata->groupid)) {
            $data->groupid = $formdata->groupid;
        }

        if (isset($formdata->userid)) {
            $data->userid = $formdata->userid;
        }

        if (isset($formdata->comment['text'])) {
            $data->comment = $formdata->comment['text'];
            $data->commentformat = $formdata->comment['format'];
        }

        $DB->insert_record('evokeportfolio_entries', $data);

        $url = new moodle_url('/mod/evokeportfolio/submissions.php', $urlparams);

        redirect($url, 'Comentário enviada com sucesso.', null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $form->display();

    echo $OUTPUT->footer();
}
