<?php

namespace mod_evokeportfolio\external;

use context;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use mod_evokeportfolio\forms\section_form;
use mod_evokeportfolio\util\evokeportfolio;

/**
 * Section external api class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class section extends external_api {
    /**
     * Create section parameters
     *
     * @return external_function_parameters
     */
    public static function create_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
            'portfolioid' => new external_value(PARAM_INT, 'The portfolio id'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data from the section form, encoded as a json array')
        ]);
    }

    /**
     * Create section method
     *
     * @param int $contextid
     * @param int $portfolioid
     * @param string $jsonformdata
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function create($contextid, $portfolioid, $jsonformdata) {
        global $DB;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::create_parameters(),
            ['contextid' => $contextid, 'portfolioid' => $portfolioid, 'jsonformdata' => $jsonformdata]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        $mform = new section_form($data);

        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $data = new \stdClass();
        $data->portfolioid = $portfolioid;
        $data->name = $validateddata->name;
        $data->timecreated = time();
        $data->timemodified = time();

        $sectionid = $DB->insert_record('evokeportfolio_sections', $data);

        $data->id = $sectionid;

        return [
            'status' => 'ok',
            'message' => get_string('createsection_success', 'mod_evokeportfolio'),
            'data' => json_encode($data)
        ];
    }

    /**
     * Create section return fields
     *
     * @return external_single_structure
     */
    public static function create_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }

    /**
     * Create section parameters
     *
     * @return external_function_parameters
     */
    public static function edit_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data from the section form, encoded as a json array')
        ]);
    }

    /**
     * Create section method
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

        $mform = new section_form($data);

        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $data = new \stdClass();
        $data->id = $validateddata->id;
        $data->name = $validateddata->name;
        $data->timemodified = time();

        $DB->update_record('evokeportfolio_sections', $data);

        return [
            'status' => 'ok',
            'message' => get_string('editsection_success', 'mod_evokeportfolio'),
            'data' => json_encode($data)
        ];
    }

    /**
     * Create section return fields
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
     * Delete section parameters
     *
     * @return external_function_parameters
     */
    public static function delete_parameters() {
        return new external_function_parameters([
            'section' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'The section id', VALUE_REQUIRED)
            ])
        ]);
    }

    /**
     * Delete section method
     *
     * @param array $section
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function delete($section) {
        global $DB;

        self::validate_parameters(self::delete_parameters(), ['section' => $section]);

        $section = (object)$section;

        $evokeportfolioutil = new evokeportfolio();
        $sectionhassubmission = $evokeportfolioutil->section_has_submissions($section->id);

        if ($sectionhassubmission) {
            throw new \moodle_exception(get_string('deletesection_hassubmissions', 'mod_evokeportfolio'));
        }

        $DB->delete_records('evokeportfolio_sections', ['id' => $section->id]);

        return [
            'status' => 'ok',
            'message' => get_string('deletesection_success', 'mod_evokeportfolio')
        ];
    }

    /**
     * Delete section return fields
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