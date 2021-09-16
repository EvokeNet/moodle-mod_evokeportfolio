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
    ]
];
