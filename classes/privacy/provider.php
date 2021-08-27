<?php

/**
 * Privacy Subsystem implementation for mod_evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @category    privacy
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace mod_evokeportfolio\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the evokeportfolio activity module.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {
    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'evokeportfolio_submissions',
            [
                'sectionid' => 'privacy:metadata:evokeportfolio_submissions:sectionid',
                'userid' => 'privacy:metadata:evokeportfolio_submissions:userid',
                'groupid' => 'privacy:metadata:evokeportfolio_submissions:groupid',
                'postedby' => 'privacy:metadata:evokeportfolio_submissions:postedby',
                'role' => 'privacy:metadata:evokeportfolio_submissions:role',
                'comment' => 'privacy:metadata:evokeportfolio_submissions:comment',
                'commentformat' => 'privacy:metadata:evokeportfolio_submissions:commentformat',
                'timecreated' => 'privacy:metadata:evokeportfolio_submissions:timecreated',
                'timemodified' => 'privacy:metadata:evokeportfolio_submissions:timemodified',
            ],
            'privacy:metadata:evokeportfolio_submissions'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // Fetch all portfolio submissions.

        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {evokeportfolio} ep ON ep.id = cm.instance
            INNER JOIN {evokeportfolio_sections} es ON es.portfolioid = ep.id
            INNER JOIN {evokeportfolio_submissions} esub ON esub.sectionid = es.id
                 WHERE esub.postedby = :userid";

        $params = [
            'modname'       => 'evokeportfolio',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Fetch all evokeportfolio answers.
        $sql = "SELECT ca.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  INNER JOIN {evokeportfolio} ep ON ep.id = cm.instance
                  INNER JOIN {evokeportfolio_sections} es ON es.portfolioid = ep.id
                  INNER JOIN {evokeportfolio_submissions} esub ON esub.sectionid = es.id
                 WHERE cm.id = :cmid";

        $params = [
            'cmid'      => $context->instanceid,
            'modname'   => 'evokeportfolio',
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       es.name,
                       esub.comment,
                       esub.timemodified
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {evokeportfolio} ep ON ep.id = cm.instance
            INNER JOIN {evokeportfolio_sections} es ON es.portfolioid = ep.id
            INNER JOIN {evokeportfolio_submissions} esub ON esub.sectionid = es.id
                 WHERE c.id {$contextsql}
                       AND ca.userid = :userid
              ORDER BY cm.id";

        $params = ['modname' => 'evokeportfolio', 'contextlevel' => CONTEXT_MODULE, 'userid' => $user->id] + $contextparams;

        // Reference to the evokeportfolio activity seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the answers for a new evokeportfolio activity and therefore
        // when we can export the complete data for the last activity.
        $lastcmid = null;

        $evokesubmissions = $DB->get_recordset_sql($sql, $params);
        foreach ($evokesubmissions as $submission) {
            // If we've moved to a new evokeportfolio, then write the last evokeportfolio data and reinit the evokeportfolio data array.
            if ($lastcmid != $submission->cmid) {
                if (!empty($submissiondata)) {
                    $context = \context_module::instance($lastcmid);
                    self::export_evokeportfolio_data_for_user($submissiondata, $context, $user);
                }
                $submissiondata = [
                    'comment' => [],
                    'timemodified' => \core_privacy\local\request\transform::datetime($submission->timemodified),
                ];
            }
            $submissiondata['comment'][] = $submission->comment;
            $lastcmid = $submission->cmid;
        }
        $evokesubmissions->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($submissiondata)) {
            $context = \context_module::instance($lastcmid);
            self::export_evokeportfolio_data_for_user($submissiondata, $context, $user);
        }
    }

    /**
     * Export the supplied personal data for a single evokeportfolio activity, along with any generic data or area files.
     *
     * @param array $submissiondata the personal data to export for the evokeportfolio.
     * @param \context_module $context the context of the evokeportfolio.
     * @param \stdClass $user the user record
     */
    protected static function export_evokeportfolio_data_for_user(array $submissiondata, \context_module $context, \stdClass $user) {
        // Fetch the generic module data for the evokeportfolio.
        $contextdata = helper::get_context_data($context, $user);

        // Merge with evokeportfolio data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $submissiondata);
        writer::with_context($context)->export_data([], $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        if ($cm = get_coursemodule_from_id('evokeportfolio', $context->instanceid)) {
            $portfoliosections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $cm->instance]);

            if ($portfoliosections) {
                foreach ($portfoliosections as $section) {
                    $DB->delete_records('evokeportfolio_submissions', ['sectionid' => $section->id]);
                }
            }
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {

            if (!$context instanceof \context_module) {
                continue;
            }

            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid]);
            if (!$instanceid) {
                continue;
            }

            $portfoliosections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $instanceid]);

            if ($portfoliosections) {
                foreach ($portfoliosections as $section) {
                    $DB->delete_records('evokeportfolio_submissions', ['sectionid' => $section->id, 'postedby' => $userid]);
                }
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('evokeportfolio', $context->instanceid);

        if (!$cm) {
            // Only evokeportfolio module will be handled.
            return;
        }

        $userids = $userlist->get_userids();
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $portfoliosections = $DB->get_records('evokeportfolio_sections', ['portfolioid' => $cm->instance]);

        if ($portfoliosections) {
            foreach ($portfoliosections as $section) {
                $select = "sectionid = :sectionid AND postedby $usersql";
                $params = ['sectionid' => $section->id] + $userparams;
                $DB->delete_records_select('evokeportfolio_submissions', $select, $params);
            }
        }
    }
}
