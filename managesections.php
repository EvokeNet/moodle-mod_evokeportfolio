<?php

/**
 * Manage sections of mod_evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

if (!has_capability('mod/evokeportfolio:addinstance', $context)) {
    $url = new moodle_url('/mod/evokeportfolio/view.php', ['id' => $id]);

    redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->set_url('/mod/evokeportfolio/managesections.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\managesections($evokeportfolio, $context);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
