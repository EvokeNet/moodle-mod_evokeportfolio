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
            'type' => new external_value(PARAM_ALPHAEXT, 'The offset value'),
            'offset' => new external_value(PARAM_INT, 'The offset value'),
            'portfolioid' => new external_value(PARAM_INT, 'The portfolio id value', VALUE_OPTIONAL)
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $courseid
     * @param int $type
     * @param int $offset
     * @param int $portfolioid
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function load($courseid, $type, $offset, $portfolioid = null) {
        global $PAGE;

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::load_parameters(), [
            'courseid' => $courseid,
            'type' => $type,
            'offset' => $offset,
            'portfolioid' => $portfolioid
        ]);

        $context = \context_course::instance($courseid);
        $PAGE->set_context($context);

        $timelineutil = new \mod_evokeportfolio\util\timeline($courseid);

        if ($type == 'my') {
            $returndata = $timelineutil->loadmy($portfolioid, $offset);
        }

        if ($type == 'team') {
            $returndata = $timelineutil->loadteam($portfolioid, $offset);
        }

        if ($type == 'network') {
            $returndata = $timelineutil->loadnetwork($portfolioid, $offset);
        }

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