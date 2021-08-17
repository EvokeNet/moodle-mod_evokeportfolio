<?php

/**
 * Submits an portfolio comment.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$userid = optional_param('userid', null, PARAM_INT);
$groupid = optional_param('groupid', null, PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);
$section = $DB->get_record('evokeportfolio_sections', ['id' => $sectionid], '*', MUST_EXIST);

if (!$userid && !$groupid) {
    $url = new moodle_url('/mod/evokeportfolio/view.php', ['id' => $id]);

    redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$urlparams = ['id' => $cm->id, 'sectionid' => $section->id];

if ($userid) {
    $evaluateduser = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

    $urlparams['userid'] = $userid;
}

if ($groupid) {
    $evaluatedgroup = $DB->get_record('groups', ['id' => $groupid], '*', MUST_EXIST);

    $urlparams['groupid'] = $groupid;
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$url = new moodle_url('/mod/evokeportfolio/comment.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$formdata = [
    'evokeportfolio' => $evokeportfolio->id,
    'sectionid' => $section->id
];

if ($evokeportfolio->groupactivity) {
    $formdata['groupid'] = $groupid;
} else {
    $formdata['userid'] = $userid;
}

$form = new \mod_evokeportfolio\forms\comment_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/viewsubmission.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        unset($formdata->submitbutton);

        $data = new \stdClass();
        $data->sectionid = $section->id;
        $data->postedby = $USER->id;
        $data->role = MOD_EVOKEPORTFOLIO_ROLE_TEACHER;
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

        $DB->insert_record('evokeportfolio_submissions', $data);

        $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', $urlparams);

        redirect($url, get_string('save_comment_success', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $form->display();

    echo $OUTPUT->footer();
}
