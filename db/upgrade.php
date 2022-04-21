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

    if ($oldversion < 2021092200) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_comments');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('text', XMLDB_TYPE_TEXT);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);
        $table->add_key('fk_submissionid', XMLDB_KEY_FOREIGN, array('submissionid'), 'evokeportfolio_submissions', array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('evokeportfolio_reactions');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('reaction', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);
        $table->add_key('fk_submissionid', XMLDB_KEY_FOREIGN, array('submissionid'), 'evokeportfolio_submissions', array('id'));
        $table->add_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('evokeportfolio_submissions');
        $field = new xmldb_field('role');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('evokeportfolio_sections');
        $field = new xmldb_field('backurl', XMLDB_TYPE_CHAR, '255');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('description', XMLDB_TYPE_TEXT);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2021092200, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2021102300) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_sections');
        $field = new xmldb_field('backurl');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2021102300, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2021112500) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio');
        if ($dbman->table_exists($table)) {
            $completionfield = new xmldb_field('completionrequiresubmit', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'groupgradingmode');

            $dbman->add_field($table, $completionfield);
        }

        upgrade_plugin_savepoint(true, 2021112500, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2021121400) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio');
        if ($dbman->table_exists($table)) {
            $completionfield = new xmldb_field('submissionsuccessmessage', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'introformat');

            $dbman->add_field($table, $completionfield);
        }

        upgrade_plugin_savepoint(true, 2021121400, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2022030500) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio');
        if ($dbman->table_exists($table)) {
            $datestartfield = new xmldb_field('datestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'grade');
            $evokationfield = new xmldb_field('evokation', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'datelimit');

            $dbman->add_field($table, $datestartfield);

            $dbman->add_field($table, $evokationfield);
        }

        upgrade_plugin_savepoint(true, 2022030500, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2022031800) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio');
        $groupactivityfield = new xmldb_field('groupactivity');
        $groupgradingmodefield = new xmldb_field('groupgradingmode');

        if ($dbman->field_exists($table, $groupactivityfield)) {
            $dbman->drop_field($table, $groupactivityfield);
        }

        if ($dbman->field_exists($table, $groupgradingmodefield)) {
            $dbman->drop_field($table, $groupgradingmodefield);
        }

        $submissions = $DB->get_records_sql('SELECT * FROM {evokeportfolio_submissions} WHERE groupid IS NOT null');
        foreach ($submissions as $submission) {
            $submission->userid = $submission->postedby;

            $DB->update_record('evokeportfolio_submissions', $submission);
        }

        $submissionstable = new xmldb_table('evokeportfolio_submissions');
        $groupidfield = new xmldb_field('groupid');
        $groupidfkkey = new xmldb_key('fk_groupid', XMLDB_KEY_FOREIGN, ['groupid'], 'groups', 'id');
        $postedbyfield = new xmldb_field('postedby');

        if ($dbman->field_exists($submissionstable, $groupidfield)) {
            $dbman->drop_key($submissionstable, $groupidfkkey);
            $dbman->drop_field($submissionstable, $groupidfield);
        }

        if ($dbman->field_exists($submissionstable, $postedbyfield)) {
            $dbman->drop_field($submissionstable, $postedbyfield);
        }

        upgrade_plugin_savepoint(true, 2022031800, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2022032800) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('evokeportfolio_submissions');
        if ($dbman->table_exists($table)) {
            $portfolioidfield = new xmldb_field('portfolioid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'id');
            $dbman->add_field($table, $portfolioidfield);

            $portfolioidfkkey = new xmldb_key('fk_portfolioid', XMLDB_KEY_FOREIGN, ['portfolioid'], 'portfolio', 'id');
            $dbman->add_key($table, $portfolioidfkkey);

            $submissions = $DB->get_records('evokeportfolio_submissions');
            foreach ($submissions as $submission) {
                $section = $DB->get_record('evokeportfolio_sections', ['id' => $submission->sectionid]);

                $submission->portfolioid = $section->portfolioid;

                $DB->update_record('evokeportfolio_submissions', $submission);
            }
        }

        $sectionidfield = new xmldb_field('sectionid');
        $sectionidfkkey = new xmldb_key('fk_sectionid', XMLDB_KEY_FOREIGN, ['sectionid'], 'evokeportfolio_sections', 'id');

        if ($dbman->field_exists($table, $sectionidfield)) {
            $dbman->drop_key($table, $sectionidfkkey);
            $dbman->drop_field($table, $sectionidfield);
        }

        $table = new xmldb_table('evokeportfolio_sections');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_plugin_savepoint(true, 2022032800, 'mod', 'evokeportfolio');
    }

    if ($oldversion < 2022042000) {
        $submissions = $DB->get_records('evokeportfolio_submissions');

        $submissionutil = new \mod_evokeportfolio\util\submission();

        foreach ($submissions as $submission) {
            $cm = get_coursemodule_from_instance('evokeportfolio', $submission->portfolioid);

            $context = context_module::instance($cm->id);

            $submissionutil->create_submission_thumbs($submission, $context);
        }

        upgrade_plugin_savepoint(true, 2022042000, 'mod', 'evokeportfolio');
    }

    return true;
}