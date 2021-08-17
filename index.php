<?php

/**
 * Display information about all the mod_evokeportfolio modules in the requested course.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);

$event = \mod_evokeportfolio\event\course_module_instance_list_viewed::create(array(
    'context' => $coursecontext
));
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/evokeportfolio/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

$PAGE->navbar->add(get_string('modulenameplural', 'mod_evokeportfolio'));

echo $OUTPUT->header();

$modulenameplural = get_string('modulenameplural', 'mod_evokeportfolio');
echo $OUTPUT->heading($modulenameplural);

$evokeportfolios = get_all_instances_in_course('evokeportfolio', $course);

if (empty($evokeportfolios)) {
    notice(get_string('noportfolioinstances', 'mod_evokeportfolio'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($course->format == 'weeks') {
    $table->head  = array(get_string('week'), get_string('name'));
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array(get_string('topic'), get_string('name'));
    $table->align = array('center', 'left', 'left', 'left');
} else {
    $table->head  = array(get_string('name'));
    $table->align = array('left', 'left', 'left');
}

foreach ($evokeportfolios as $evokeportfolio) {
    if (!$evokeportfolio->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/evokeportfolio/view.php', array('id' => $evokeportfolio->coursemodule)),
            format_string($evokeportfolio->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/evokeportfolio/view.php', array('id' => $evokeportfolio->coursemodule)),
            format_string($evokeportfolio->name, true));
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($evokeportfolio->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();
