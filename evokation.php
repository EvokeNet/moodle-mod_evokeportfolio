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
$portfolioid = optional_param('portfolio', null, PARAM_INT);
$groupid = optional_param('group', null, PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

$portfolio = null;
if ($portfolioid) {
    $portfolio = $DB->get_record('evokeportfolio', array('id' => $portfolioid, 'evokation' => 1), '*', MUST_EXIST);
}

$group = null;
if ($groupid) {
    $group = $DB->get_record('groups', array('id' => $groupid), '*', MUST_EXIST);
}

require_course_login($course);

$context = context_course::instance($course->id);

$event = \mod_evokeportfolio\event\course_module_instance_list_viewed::create(array(
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->trigger();

$urlparams = ['id' => $id];

$url = new moodle_url('/mod/evokeportfolio/evokation.php', $urlparams);

$PAGE->set_url($url);

$pagetitle = format_string($course->fullname);
$heading = format_string($course->fullname);
if (!has_capability('mod/evokeportfolio:grade', $context)) {
    $pagetitle = $course->fullname . ' : ' . get_string('page_evokation_title', 'mod_evokeportfolio');
    $heading = get_string('page_evokation_title', 'mod_evokeportfolio');
}

$PAGE->set_title($pagetitle);
$PAGE->set_heading($heading);
$PAGE->set_context($context);

$PAGE->navbar->add($pagetitle);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

if (!has_capability('mod/evokeportfolio:grade', $context)) {
    $contentrenderable = new \mod_evokeportfolio\output\evokation($course);

    echo $renderer->render($contentrenderable);
} else {
    if (!$groupid) {
        $groupsutil = new \mod_evokeportfolio\util\group();

        $usercoursegroups = $groupsutil->get_user_groups($course->id);

        if ($usercoursegroups) {
            $group = current($usercoursegroups);
        }
    }

    $contentrenderable = new \mod_evokeportfolio\output\evokationadmin($course, $context, null, $portfolio, $group);

    echo $renderer->render($contentrenderable);
}

echo $OUTPUT->footer();
