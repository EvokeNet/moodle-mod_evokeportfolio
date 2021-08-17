<?php

/**
 * Submits grading.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', null, PARAM_INT);
$groupid = optional_param('groupid', null, PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);

if (!$userid && !$groupid) {
    $url = new moodle_url('/mod/evokeportfolio/view.php', ['id' => $id]);

    redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$urlparams = ['id' => $cm->id];

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

$redirecturl = new moodle_url('/course/view', ['id' => $course->id]);

if ($evokeportfolio->grade == 0) {
    redirect($redirecturl, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

if (!has_capability('mod/evokeportfolio:grade', $context)) {
    redirect($redirecturl, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$gradeutil = new \mod_evokeportfolio\util\grade();
if ($gradeutil->is_gradeitem_locked($evokeportfolio->id, $evokeportfolio->course)) {
    redirect($redirecturl, get_string('gradinglocked', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$url = new moodle_url('/mod/evokeportfolio/grade.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$formdata = [
    'cmid' => $cm->id,
    'groupactivity' => $evokeportfolio->groupactivity,
    'groupgradingmode' => $evokeportfolio->groupgradingmode,
    'instanceid' => $evokeportfolio->id
];

if ($evokeportfolio->groupactivity) {
    $formdata['groupid'] = $groupid;
} else {
    $formdata['userid'] = $userid;
}

$form = new \mod_evokeportfolio\forms\grade_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/viewsubmission.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        unset($formdata->submitbutton);

        $gradeutil = new \mod_evokeportfolio\util\grade();
        $gradeutil->process_grade_form($evokeportfolio, $formdata);

        $url = new moodle_url('/mod/evokeportfolio/viewsubmission.php', $urlparams);

        redirect($url, get_string('save_grade_success', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $renderer = $PAGE->get_renderer('mod_evokeportfolio');

    $contentrenderable = new \mod_evokeportfolio\output\grade($evokeportfolio, $context, $form, $userid, $groupid);

    echo $renderer->render($contentrenderable);

    echo $OUTPUT->footer();
}
