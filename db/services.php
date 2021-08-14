<?php
// This file is part of Timeline course format for moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Timeline Social course format services
 *
 * @package    mod_evokeportfolio
 * @copyright  2021 onwards Willian Mano {@link https://conecti.me}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
