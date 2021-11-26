<?php

declare(strict_types=1);

namespace mod_evokeportfolio\completion;

use core_completion\activity_custom_completion;

/**
 * Activity custom completion subclass for the Assign Tutor activity.
 *
 * Class for defining mod_evokeportfolio's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given peerreview instance and a user.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $userid = $this->userid;
        $evokeportfolioid = $this->cm->instance;

        if (!$evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $evokeportfolioid])) {
            throw new \moodle_exception('Unable to find evokeportfolio with id ' . $evokeportfolioid);
        }

        if ($rule == 'completionrequiresubmit') {
            $evokeportfolioutil = new \mod_evokeportfolio\util\evokeportfolio();

            $hassubmission = $evokeportfolioutil->has_submission($evokeportfolio->id, $userid);

            if ($hassubmission) {
                return COMPLETION_COMPLETE;
            }
        }

        return COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completionrequiresubmit'];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        return [
            'completionrequiresubmit' => get_string('completionrequiresubmit', 'mod_evokeportfolio')
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionrequiresubmit',
            'completionusegrade'
        ];
    }
}
