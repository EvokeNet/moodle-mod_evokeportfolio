<?php

/**
 * Prints mod_evokeportfolio submissions.
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

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

if (!empty($userid) && $userid != $USER->id && !has_capability('mod/evokeportfolio:grade', $context)) {
    $url = new moodle_url('/course/view', ['id' => $course->id]);

    redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

if ($groupid) {
    $grouputil = new \mod_evokeportfolio\util\groups();

    if (!$grouputil->is_group_member($groupid, $USER->id) && !has_capability('mod/evokeportfolio:grade', $context)) {
        $url = new moodle_url('/course/view', ['id' => $course->id]);

        redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

$PAGE->set_url('/mod/evokeportfolio/submissions.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\submissions($evokeportfolio, $context);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
