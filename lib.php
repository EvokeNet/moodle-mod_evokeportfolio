<?php

/**
 * Library of interface functions and constants.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

DEFINE('MOD_EVOKEPORTFOLIO_ROLE_STUDENT', 1);
DEFINE('MOD_EVOKEPORTFOLIO_ROLE_TEACHER', 2);
DEFINE('MOD_EVOKEPORTFOLIO_GRADING_GROUP', 1);
DEFINE('MOD_EVOKEPORTFOLIO_GRADING_INDIVIDUAL', 2);

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function evokeportfolio_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_ASSIGNMENT;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Saves a new instance of the mod_evokeportfolio into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_evokeportfolio_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function evokeportfolio_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();
    $moduleinstance->timemodified = time();

    if ($moduleinstance->groupactivity == 0) {
        $moduleinstance->groupgradingmode = 0;
    }

    $id = $DB->insert_record('evokeportfolio', $moduleinstance);

    $moduleinstance->id = $id;

    evokeportfolio_grade_item_update($moduleinstance);

    $DB->insert_record('evokeportfolio_sections', [
        'portfolioid' => $id,
        'name' => get_string('section', 'mod_evokeportfolio') . ' ' . 1,
        'timecreated' => time(),
        'timemodified' => time()
    ]);

    return $id;
}

/**
 * Updates an instance of the mod_evokeportfolio in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_evokeportfolio_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function evokeportfolio_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    evokeportfolio_grade_item_update($moduleinstance);

    return $DB->update_record('evokeportfolio', $moduleinstance);
}

/**
 * Removes an instance of the mod_evokeportfolio from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function evokeportfolio_delete_instance($id) {
    global $DB;

    $evokeportfolio = $DB->get_record('evokeportfolio', ['id' => $id]);
    if (!$evokeportfolio) {
        return false;
    }

    $DB->delete_records('evokeportfolio', ['id' => $id]);

    $sections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $id]);

    if ($sections) {
        foreach ($sections as $section) {
            $DB->delete_records('evokeportfolio_submissions', ['sectionid' => $section->id]);
        }
    }

    $DB->delete_records('evokeportfolio_sections', ['portfolioid' => $id]);

    $DB->delete_records('evokeportfolio_grades', ['portfolioid' => $id]);

    evokeportfolio_grade_item_delete($evokeportfolio);

    return true;
}

/**
 * Is a given scale used by the instance of mod_evokeportfolio?
 *
 * This function returns if a scale is being used by one mod_evokeportfolio
 * if it has support for grading and scales.
 *
 * @param int $moduleinstanceid ID of an instance of this module.
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by the given mod_evokeportfolio instance.
 */
function evokeportfolio_scale_used($moduleinstanceid, $scaleid) {
    global $DB;

    if ($scaleid && $DB->record_exists('evokeportfolio', array('id' => $moduleinstanceid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of mod_evokeportfolio.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by any mod_evokeportfolio instance.
 */
function evokeportfolio_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('evokeportfolio', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given mod_evokeportfolio instance.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param bool $reset Reset grades in the gradebook.
 * @return void.
 */
function evokeportfolio_grade_item_update($moduleinstance, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($moduleinstance->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($moduleinstance->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $moduleinstance->grade;
        $item['grademin']  = 0;
    } else if ($moduleinstance->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$moduleinstance->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('/mod/evokeportfolio', $moduleinstance->course, 'mod', 'evokeportfolio', $moduleinstance->id, 0, null, $item);
}

/**
 * Delete grade item for given mod_evokeportfolio instance.
 *
 * @param stdClass $moduleinstance Instance object.
 * @return grade_item.
 */
function evokeportfolio_grade_item_delete($moduleinstance) {
    global $CFG;

    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('/mod/evokeportfolio', $moduleinstance->course, 'mod', 'evokeportfolio',
                        $moduleinstance->id, 0, null, ['deleted' => 1]);
}

/**
 * Update mod_evokeportfolio grades in the gradebook.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param int $userid Update grade of specific user only, 0 means all participants.
 */
function evokeportfolio_update_grades($moduleinstance, $userid = 0) {
    global $CFG;

    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = [];

    grade_update('/mod/evokeportfolio', $moduleinstance->course, 'mod', 'evokeportfolio', $moduleinstance->id, 0, $grades);
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@see file_browser::get_file_info_context_module()}.
 *
 * @package     mod_evokeportfolio
 * @category    files
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return string[].
 */
function evokeportfolio_get_file_areas($course, $cm, $context) {
    return [];
}

/**
 * File browsing support for mod_evokeportfolio file areas.
 *
 * @package     mod_evokeportfolio
 * @category    files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info Instance or null if not found.
 */
function evokeportfolio_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the mod_evokeportfolio file areas.
 *
 * @package     mod_evokeportfolio
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The mod_evokeportfolio's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function evokeportfolio_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, false, $cm);

    $itemid = (int)array_shift($args);
    if ($itemid == 0) {
        return false;
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/mod_evokeportfolio/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

function mod_evokeportfolio_output_fragment_section_form($args) {
    $args = (object) $args;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    $mform = new \mod_evokeportfolio\forms\section_form($formdata, [
        'id' => $serialiseddata->id,
        'portfolioid' => $serialiseddata->portfolioid,
        'name' => $serialiseddata->name,
        'dependentsections' => $serialiseddata->dependentsections,
    ]);

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();

    return $o;
}

function mod_evokeportfolio_output_fragment_chapter_form($args) {
    $args = (object) $args;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    $mform = new \mod_evokeportfolio\forms\chapter_form($formdata, [
        'id' => $serialiseddata->id,
        'course' => $serialiseddata->course,
        'name' => $serialiseddata->name,
        'portfolios' => $serialiseddata->portfolios,
    ]);

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();

    return $o;
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $modnode
 * @return void
 */
function evokeportfolio_extend_settings_navigation($settings, $modnode) {
    global $PAGE;

    if (!has_capability('mod/evokeportfolio:addinstance', $PAGE->cm->context)) {
        return false;
    }

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $modnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    $node = navigation_node::create(get_string('managesections', 'mod_evokeportfolio'),
        new moodle_url('/mod/evokeportfolio/managesections.php', array('id' => $PAGE->cm->id)),
        navigation_node::TYPE_SETTING, null, 'mod_evokeportfolio_managesections',
        new pix_icon('t/edit', ''));
    $modnode->add_node($node, $beforekey);
}

function mod_evokeportfolio_extend_navigation_course($navigation, $course, $context) {
    $url = new moodle_url('/mod/evokeportfolio/managechapters.php',['id' => $course->id]);

    $node = navigation_node::create(
        get_string('portfoliochapters', 'mod_evokeportfolio'),
        $url,
        navigation_node::NODETYPE_LEAF,
        null,
        null,
        new pix_icon('t/edit', '')
    );

    $navigation->add_node($node);

    $url = new moodle_url('/mod/evokeportfolio/index.php', array('id' => $course->id));

    $node = navigation_node::create(
        get_string('portfoliograding', 'mod_evokeportfolio'),
        $url,
        navigation_node::NODETYPE_LEAF,
        null,
        null,
        new pix_icon('t/edit', '')
    );

    $navigation->add_node($node);
}
