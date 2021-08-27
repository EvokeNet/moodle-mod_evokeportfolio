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

    return true;
}