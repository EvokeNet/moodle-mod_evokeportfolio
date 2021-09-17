<?php

/**
* Upgrade file.
*
* @package    mod_evokeportfolio
* @copyright   2021 World Bank Group <https://worldbank.org>
* @author      Willian Mano <willianmanoaraujo@gmail.com>
*/

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade code for the eMailTest local plugin.
 *
 * @param int $oldversion - the version we are upgrading from.
 *
 * @return bool result
 *
 * @throws ddl_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_evokeportfolio_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2021082500) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_sections');
        if ($dbman->table_exists($table)) {
            $completionfield = new xmldb_field('dependentsections', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name');

            $dbman->add_field($table, $completionfield);
        }

        upgrade_plugin_savepoint(true, 2021082500, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2021091500) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_chapters');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, 'default name');
        $table->add_field('portfolios', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, 'default name');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);
        $table->add_key('fk_course', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2021091500, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2021091700) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_chaptergrades');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('chapterid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);
        $table->add_key('fk_chapterid', XMLDB_KEY_FOREIGN, array('chapterid'), 'evokeportfolio_chapters', array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2021091700, 'mod', 'evokeportfolio');
    }

    return true;
}