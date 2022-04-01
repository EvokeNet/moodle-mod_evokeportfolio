<?php

/**
 * Grading students of mod_evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = required_param('id', PARAM_INT);

$chapter = $DB->get_record('evokeportfolio_chapters', ['id' => $id], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $chapter->course], '*', MUST_EXIST);

require_course_login($course, true);

$context = context_course::instance($course->id);

if (!has_capability('mod/evokeportfolio:grade', $context)) {
    $url = new moodle_url('/mod/course/view.php', ['id' => $id]);

    redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
}

$title = $course->shortname . ': ' . get_string('chaptersportfolios', 'mod_evokeportfolio');
$PAGE->set_url('/mod/evokeportfolio/gradingusers.php', ['id' => $id]);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_evokeportfolio');

$contentrenderable = new \mod_evokeportfolio\output\gradingusers($course, $context, $chapter);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
