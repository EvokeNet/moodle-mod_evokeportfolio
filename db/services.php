<?php

/**
 * Evokeportfolio services definition
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 onwards World Bank Group
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
    ]
];
