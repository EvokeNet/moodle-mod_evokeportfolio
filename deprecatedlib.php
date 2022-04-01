<?php

/**
 * List of deprecated mod_evokeportfolio functions.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

/**
 * Obtains the automatic completion state for this survey based on the condition
 * in feedback settings.
 *
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_evokeportfolio\completion\custom_completion
 * @param stdClass $course Course
 * @param cm_info|stdClass $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function evokeportfolio_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    if (!$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $cm->instance])) {
        throw new \moodle_exception('Unable to find evokeportfolio with id ' . $cm->instance);
    }

    $submissionutil = new \mod_evokeportfolio\util\submission();

    $hassubmission = $submissionutil->user_has_submission($evokeportfolio->id, $userid);

    if ($evokeportfolio->completionrequiresubmit && !$hassubmission) {
        return COMPLETION_INCOMPLETE;
    }

    return COMPLETION_COMPLETE;
}
