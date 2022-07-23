<?php

/**
 * Prints an instance of mod_evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

global $DB;

// Course module id.
$id = optional_param('id', null, PARAM_INT);
$portfolioid = optional_param('portfolioid', null, PARAM_INT);
$embed = optional_param('embed', 0, PARAM_INT);

if (!$id && !$portfolioid) {
    throw new Exception('Illegal access');
}

if ($id) {
    list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
    $evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($portfolioid) {
    list ($course, $cm) = get_course_and_cm_from_instance($portfolioid, 'evokeportfolio');
    $evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$event = \mod_evokeportfolio\event\course_module_viewed::create(array(
    'context' => $context,
    'objectid' => $evokeportfolio->id,
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('evokeportfolio', $evokeportfolio);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/evokeportfolio/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if ($embed) {
    $PAGE->set_pagelayout('embedded');
}

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

if ($evokeportfolio->groupactivity) {
    $contentrenderable = new \mod_evokeportfolio\output\view_group($evokeportfolio, $context, $embed);
} else {
    $contentrenderable = new \mod_evokeportfolio\output\view($evokeportfolio, $context, $embed);
}

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
