<?php

namespace mod_evokeportfolio\external;

use context;
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
class comment extends external_api {
    /**
     * Create comment parameters
     *
     * @return external_function_parameters
     */
    public static function add_parameters() {
        return new external_function_parameters([
            'comment' => new external_single_structure([
                'submissionid' => new external_value(PARAM_INT, 'The submission id', VALUE_REQUIRED),
                'message' => new external_value(PARAM_RAW, 'The post message', VALUE_REQUIRED)
            ])
        ]);
    }

    /**
     * Create comment method
     *
     * @param array $comment
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function add($comment) {
        global $DB, $USER;

        self::validate_parameters(self::add_parameters(), ['comment' => $comment]);

        $comment = (object)$comment;

        $usercomment = new \stdClass();
        $usercomment->submissionid = $comment->submissionid;
        $usercomment->userid = $USER->id;
        $usercomment->text = trim($comment->message);
        $usercomment->timecreated = time();
        $usercomment->timemodified = time();

        $DB->insert_record('evokeportfolio_comments', $usercomment);

        return [
            'status' => 'ok',
            'message' => $usercomment->text
        ];
    }

    /**
     * Create comment return fields
     *
     * @return external_single_structure
     */
    public static function add_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message')
            )
        );
    }
}