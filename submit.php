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
$id = required_param('id', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$submissionid = optional_param('submissionid', null, PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'evokeportfolio');
$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance], '*', MUST_EXIST);
$section = $DB->get_record('evokeportfolio_sections', ['id' => $sectionid], '*', MUST_EXIST);

if ($submissionid) {
    $submission = $DB->get_record('evokeportfolio_submissions', ['id' => $submissionid, 'postedby' => $USER->id], '*');

    if (!$submission) {
        $url = new moodle_url('/course/view', ['id' => $course->id]);

        redirect($url, get_string('illegalaccess', 'mod_evokeportfolio'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

$urlparams = ['id' => $cm->id, 'sectionid' => $section->id];
$url = new moodle_url('/mod/evokeportfolio/submit.php', $urlparams);

$PAGE->set_url($url);
$PAGE->set_title(format_string($evokeportfolio->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$formdata = [
    'portfolioid' => $evokeportfolio->id,
    'sectionid' => $section->id
];

if ($evokeportfolio->groupactivity) {
    $groupsutil = new \mod_evokeportfolio\util\group();
    $usercoursegroup = $groupsutil->get_user_group($course->id);

    $formdata['groupid'] = $usercoursegroup->id;
} else {
    $formdata['userid'] = $USER->id;
}

if ($submissionid) {
    $formdata['submissionid'] = $submissionid;
}

$form = new \mod_evokeportfolio\forms\submit_form($url, $formdata);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/evokeportfolio/submissions.php', $urlparams));
} else if ($formdata = $form->get_data()) {
    try {
        unset($formdata->submitbutton);

        if (isset($formdata->submissionid)) {
            $submission = $DB->get_record('evokeportfolio_submissions', ['id' => $formdata->submissionid, 'postedby' => $USER->id], '*', MUST_EXIST);

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
                'relateduserid' => $submission->postedby
            );
            $event = \mod_evokeportfolio\event\submission_updated::create($params);
            $event->add_record_snapshot('evokeportfolio_submissions', $submission);
            $event->trigger();

            $redirectstring = get_string('update_submission_success', 'mod_evokeportfolio');
        } else {
            $submission = new \stdClass();
            $submission->sectionid = $section->id;
            $submission->postedby = $USER->id;
            $submission->role = MOD_EVOKEPORTFOLIO_ROLE_STUDENT;
            $submission->timecreated = time();
            $submission->timemodified = time();
            $submission->groupid = null;
            $submission->userid = null;
            $submission->comment = null;
            $submission->commentformat = null;

            if (isset($formdata->groupid)) {
                $submission->groupid = $formdata->groupid;
            }

            if (isset($formdata->userid)) {
                $submission->userid = $formdata->userid;
            }

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
                'relateduserid' => $submission->postedby
            );
            $event = \mod_evokeportfolio\event\submission_sent::create($params);
            $event->add_record_snapshot('evokeportfolio_submissions', $submission);
            $event->trigger();

            $redirectstring = get_string('save_submission_success', 'mod_evokeportfolio');
        }

        // Process attachments.
        $draftitemid = file_get_submitted_draft_itemid('attachments');

        file_save_draft_area_files($draftitemid, $context->id, 'mod_evokeportfolio', 'attachments', $submission->id, ['subdirs' => 0, 'maxfiles' => 1]);

        $url = new moodle_url('/mod/evokeportfolio/submissions.php', $urlparams);

        redirect($url, $redirectstring, null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
} else {
    echo $OUTPUT->header();

    $form->display();

    echo $OUTPUT->footer();
}