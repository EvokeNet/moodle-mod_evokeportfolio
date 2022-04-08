<?php

/**
 * Display information about all the mod_evokeportfolio modules in the requested course.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);
$portfolioid = required_param('portfolio', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$portfolio = $DB->get_record('evokeportfolio', array('id' => $portfolioid, 'evokation' => 0), '*', MUST_EXIST);

require_course_login($course);

$context = context_course::instance($course->id);

$event = \mod_evokeportfolio\event\course_module_instance_list_viewed::create(array(
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->trigger();

$urlparams = ['id' => $id, 'portfolio' => $portfolioid];

$url = new moodle_url('/mod/evokeportfolio/index.php', $urlparams);

$PAGE->set_url($url);

$pagetitle = format_string($course->fullname);
$heading = format_string($course->fullname);
if (!has_capability('mod/evokeportfolio:grade', $context)) {
    $pagetitle = $course->fullname . ' : ' . get_string('myportfolios', 'mod_evokeportfolio');
    $heading = get_string('myportfolios', 'mod_evokeportfolio');
}

$PAGE->set_title($pagetitle);
$PAGE->set_heading($heading);
$PAGE->set_context($context);

$PAGE->navbar->add($pagetitle);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\indexportfolio($course, $portfolio);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
