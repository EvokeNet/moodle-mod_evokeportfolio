<?php

namespace mod_evokeportfolio\external;

use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;

/**
 * Section external api class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class timeline extends external_api {
    /**
     * Create chapter parameters
     *
     * @return external_function_parameters
     */
    public static function load_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The block course id'),
            'type' => new external_value(PARAM_ALPHAEXT, 'The limit value'),
            'limit' => new external_value(PARAM_INT, 'The limit value'),
            'hasmoreitems' => new external_value(PARAM_BOOL, 'Load more items control')
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $courseid
     * @param int $type
     * @param int $limit
     * @param bool $hasmoreitems
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function load($courseid, $type, $limit, $hasmoreitems) {
        global $PAGE;

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::load_parameters(), [
            'courseid' => $courseid,
            'type' => $type,
            'limit' => $limit,
            'hasmoreitems' => $hasmoreitems
        ]);

        $context = \context_course::instance($courseid);
        $PAGE->set_context($context);

        $timelineutil = new \mod_evokeportfolio\util\timeline($courseid, 1);

        $returndata = $timelineutil->load();

        return [
            'status' => 'ok',
            'data' => json_encode($returndata)
        ];
    }

    /**
     * Create chapter return fields
     *
     * @return external_single_structure
     */
    public static function load_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }
}