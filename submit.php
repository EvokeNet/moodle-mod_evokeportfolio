<?php

/**
 * Submits an portfolio entry.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

// Course module id.
$id = optional_param('id', null, PARAM_INT);
$portfolioid = optional_param('portfolioid', null, PARAM_INT);
$submissionid = optional_param('submissionid', null, PARAM_INT);
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

if ($submissionid) {
    $submission = $DB->get_record('evokeportfolio_submissions', ['id' => $submissionid, 'userid' => $USER->id], '*');

    if (!$submission) {
        $url = new moodle_url('/course/view', ['id' => $course->id]);

        redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$urlparams = ['id' => $cm->id];
if ($embed) {
    $PAGE->set_pagelayout('embedded');
    $urlparams['embed'] = $embed;
}

$url = new moodle_url('/mod/evokeportfolio/submit.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$formdata = [
    'portfolioid' => $evokeportfolio->id
];

$formdata['userid'] = $USER->id;

if ($submissionid) {
    $formdata['submissionid'] = $submissionid;
}

$form = new \mod_evokeportfolio\forms\submit_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/view.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        unset($formdata->submitbutton);

        if (isset($formdata->submissionid)) {
            $submission = $DB->get_record('evokeportfolio_submissions', ['id' => $formdata->submissionid, 'userid' => $USER->id], '*', MUST_EXIST);

            $submission->comment = null;
            $submission->commentformat = null;
            $submission->timemodified = time();

            if (isset($formdata->comment['text'])) {
                $submission->comment = $formdata->comment['text'];
                $submission->commentformat = $formdata->comment['format'];
            }

            $DB->update_record('evokeportfolio_submissions', $submission);

            // Process event.
            $params = array(
                'context' => $context,
                'objectid' => $submission->id,
                'relateduserid' => $submission->userid
            );
            $event = \mod_evokeportfolio\event\submission_updated::create($params);
            $event->add_record_snapshot('evokeportfolio_submissions', $submission);
            $event->trigger();

            $redirectstring = get_string('update_submission_success', 'mod_evokeportfolio');
        } else {
            $submission = new \stdClass();
            $submission->portfolioid = $evokeportfolio->id;
            $submission->userid = $USER->id;
            $submission->timecreated = time();
            $submission->timemodified = time();
            $submission->comment = null;
            $submission->commentformat = null;

            if (isset($formdata->comment['text'])) {
                $submission->comment = $formdata->comment['text'];
                $submission->commentformat = $formdata->comment['format'];
            }

            $submissionid = $DB->insert_record('evokeportfolio_submissions', $submission);
            $submission->id = $submissionid;

            // Processe event.
            $params = array(
                'context' => $context,
                'objectid' => $submissionid,
                'relateduserid' => $submission->userid
            );
            $event = \mod_evokeportfolio\event\submission_sent::create($params);
            $event->add_record_snapshot('evokeportfolio_submissions', $submission);
            $event->trigger();

            // Completion progress
            $completion = new completion_info($course);
            $completion->update_state($cm, COMPLETION_COMPLETE);

            $redirectstring = get_string('save_submission_success', 'mod_evokeportfolio');
            if (isset($evokeportfolio->submissionsuccessmessage) && $evokeportfolio->submissionsuccessmessage != "") {
                $redirectstring = $evokeportfolio->submissionsuccessmessage;
            }
        }

        // Process attachments.
        $draftitemid = file_get_submitted_draft_itemid('attachments');

        file_save_draft_area_files($draftitemid, $context->id, 'mod_evokeportfolio', 'attachments', $submission->id, ['subdirs' => 0, 'maxfiles' => 10]);

       $submissionutil = new \mod_evokeportfolio\util\submission();
       $submissionutil->create_submission_thumbs($submission, $context);

        $url = new moodle_url('/course/view.php', ['id' => $evokeportfolio->course]);

        redirect($url, $redirectstring, null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $form->display();

    echo $OUTPUT->footer();
}