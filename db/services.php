<?php

/**
 * Evokeportfolio services definition
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_evokeportfolio_createsection' => [
        'classname' => 'mod_evokeportfolio\external\section',
        'classpath' => 'mod/evokeportfolio/classes/external/section.php',
        'methodname' => 'create',
        'description' => 'Creates a new section',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_editsection' => [
        'classname' => 'mod_evokeportfolio\external\section',
        'classpath' => 'mod/evokeportfolio/classes/external/section.php',
        'methodname' => 'edit',
        'description' => 'Edits a section',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_deletesection' => [
        'classname' => 'mod_evokeportfolio\external\section',
        'classpath' => 'mod/evokeportfolio/classes/external/section.php',
        'methodname' => 'delete',
        'description' => 'Deletes a section',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_createchapter' => [
        'classname' => 'mod_evokeportfolio\external\chapter',
        'classpath' => 'mod/evokeportfolio/classes/external/chapter.php',
        'methodname' => 'create',
        'description' => 'Creates a new chapter',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_editchapter' => [
        'classname' => 'mod_evokeportfolio\external\chapter',
        'classpath' => 'mod/evokeportfolio/classes/external/chapter.php',
        'methodname' => 'edit',
        'description' => 'Edits a chapter',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_deletechapter' => [
        'classname' => 'mod_evokeportfolio\external\chapter',
        'classpath' => 'mod/evokeportfolio/classes/external/chapter.php',
        'methodname' => 'delete',
        'description' => 'Deletes a chapter',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_gradeuserchapter' => [
        'classname' => 'mod_evokeportfolio\external\grade',
        'classpath' => 'mod/evokeportfolio/classes/external/grade.php',
        'methodname' => 'grade',
        'description' => 'Add a new grade',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_addcomment' => [
        'classname' => 'mod_evokeportfolio\external\comment',
        'classpath' => 'mod/evokeportfolio/classes/external/comment.php',
        'methodname' => 'add',
        'description' => 'Add a new comment',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_enrolledusers' => [
        'classname' => 'mod_evokeportfolio\external\course',
        'classpath' => 'mod/evokeportfolio/classes/external/course.php',
        'methodname' => 'enrolledusers',
        'description' => 'Get the list of users enrolled in a course',
        'type' => 'read',
        'ajax' => true
    ],
    'mod_evokeportfolio_togglereaction' => [
        'classname' => 'mod_evokeportfolio\external\reaction',
        'classpath' => 'mod/evokeportfolio/classes/external/reaction.php',
        'methodname' => 'toggle',
        'description' => 'Toggle a user reaction',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_gradeportfolio' => [
        'classname' => 'mod_evokeportfolio\external\grade',
        'classpath' => 'mod/evokeportfolio/classes/external/grade.php',
        'methodname' => 'gradeportfolio',
        'description' => 'Grade a user portfolio',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_evokeportfolio_loadtimeline' => [
        'classname' => 'mod_evokeportfolio\external\timeline',
        'classpath' => 'mod/evokeportfolio/classes/external/timeline.php',
        'methodname' => 'load',
        'description' => 'Load a timeline',
        'type' => 'read',
        'ajax' => true
    ],
    'mod_evokeportfolio_loadtimelineevokation' => [
        'classname' => 'mod_evokeportfolio\external\timeline',
        'classpath' => 'mod/evokeportfolio/classes/external/timeline.php',
        'methodname' => 'loadevokation',
        'description' => 'Load an evokation timeline',
        'type' => 'read',
        'ajax' => true
    ],
];
