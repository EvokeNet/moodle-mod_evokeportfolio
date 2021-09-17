<?php

namespace mod_evokeportfolio\external;

use context;
use core\notification;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use mod_evokeportfolio\forms\gradeuserchapter_form;

/**
 * Grade external api class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class grade extends external_api {
    /**
     * Create chapter parameters
     *
     * @return external_function_parameters
     */
    public static function grade_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
            'chapterid' => new external_value(PARAM_INT, 'The chapter id'),
            'userid' => new external_value(PARAM_INT, 'The user id'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data from the chapter form, encoded as a json array')
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $contextid
     * @param int $chapterid
     * @param int $userid
     * @param string $jsonformdata
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function grade($contextid, $chapterid, $userid, $jsonformdata) {
        global $DB, $SESSION;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::grade_parameters(), [
            'contextid' => $contextid,
            'chapterid' => $chapterid,
            'userid' => $userid,
            'jsonformdata' => $jsonformdata
        ]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        $customdata = [
            'chapterid' => $chapterid,
            'userid' => $userid
        ];
        $mform = new gradeuserchapter_form($data, $customdata);

        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $gradeutil = new \mod_evokeportfolio\util\grade();

        $grade = $gradeutil->process_user_chapter_grade($userid, $chapterid, $data['grade']);

        return [
            'status' => 'ok',
            'message' => get_string('grading_success', 'mod_evokeportfolio'),
            'data' => json_encode($grade)
        ];
    }

    /**
     * Create chapter return fields
     *
     * @return external_single_structure
     */
    public static function grade_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }

    /**
     * Create chapter parameters
     *
     * @return external_function_parameters
     */
    public static function edit_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data from the chapter form, encoded as a json array')
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $contextid
     * @param string $jsonformdata
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function edit($contextid, $jsonformdata) {
        global $DB;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::edit_parameters(),
            ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        $mform = new chapter_form($data);

        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $portfolios = null;
        if ($data['portfolios']) {
            foreach ($data['portfolios'] as $portfolio) {
                $portfolios[] = $portfolio;
            }

            $portfolios = implode(",", $portfolios);
        }

        $chapter = new \stdClass();
        $chapter->id = $validateddata->id;
        $chapter->name = $validateddata->name;
        $chapter->portfolios = $portfolios;
        $chapter->timemodified = time();

        $DB->update_record('evokeportfolio_chapters', $chapter);

        return [
            'status' => 'ok',
            'message' => get_string('editchapter_success', 'mod_evokeportfolio'),
            'data' => json_encode($chapter)
        ];
    }

    /**
     * Create chapter return fields
     *
     * @return external_single_structure
     */
    public static function edit_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }

    /**
     * Delete chapter parameters
     *
     * @return external_function_parameters
     */
    public static function delete_parameters() {
        return new external_function_parameters([
            'chapter' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'The chapter id', VALUE_REQUIRED)
            ])
        ]);
    }

    /**
     * Delete chapter method
     *
     * @param array $chapter
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function delete($chapter) {
        global $DB;

        self::validate_parameters(self::delete_parameters(), ['chapter' => $chapter]);

        $chapter = (object)$chapter;

        $DB->delete_records('evokeportfolio_chapters', ['id' => $chapter->id]);

        return [
            'status' => 'ok',
            'message' => get_string('deletechapter_success', 'mod_evokeportfolio')
        ];
    }

    /**
     * Delete chapter return fields
     *
     * @return external_single_structure
     */
    public static function delete_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_TEXT, 'Return message')
            )
        );
    }
}