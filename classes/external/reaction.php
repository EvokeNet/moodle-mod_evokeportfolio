<?php

namespace mod_evokeportfolio\external;

use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use mod_evokeportfolio\util\reaction as reactionutil;

/**
 * Reaction external api class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class reaction extends external_api {
    /**
     * Create chapter parameters
     *
     * @return external_function_parameters
     */
    public static function toggle_parameters() {
        return new external_function_parameters([
            'submissionid' => new external_value(PARAM_INT, 'The submission id'),
            'reactionid' => new external_value(PARAM_INT, 'The reaction id')
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $contextid
     * @param int $course
     * @param string $jsonformdata
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function toggle($submissionid, $reactionid) {
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::toggle_parameters(),
            ['submissionid' => $submissionid, 'reactionid' => $reactionid]);

        $reactionutil = new reactionutil();

        $message = $reactionutil->toggle_reaction($submissionid, $reactionid);

        return [
            'status' => 'ok',
            'message' => $message
        ];
    }

    /**
     * Create chapter return fields
     *
     * @return external_single_structure
     */
    public static function toggle_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message')
            )
        );
    }
}
