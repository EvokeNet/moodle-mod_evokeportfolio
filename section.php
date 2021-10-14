<?php

/**
 * Prints mod_evokeportfolio submissions.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
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

$user = null;
if (!empty($userid)) {
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
}

$group = null;
if (!empty($group)) {
    $user = $DB->get_record('group', ['id' => $groupid], '*', MUST_EXIST);
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/evokeportfolio/submissions.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\section($context, $evokeportfolio, $section, $user, $group);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
