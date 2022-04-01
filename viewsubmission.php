<?php

/**
 * View mod_evokeportfolio submissions.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$urlparams = ['id' => $cm->id, 'userid' => $userid];

$PAGE->set_url('/mod/evokeportfolio/viewsubmission.php', $urlparams);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\viewsubmission($evokeportfolio, $context, $user);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
