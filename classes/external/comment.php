<?php

namespace mod_evokeportfolio\external;

use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use mod_evokeportfolio\notification\commentmention;
use mod_evokeportfolio\util\user;
use moodle_url;
use html_writer;
use context_course;

/**
 * Section external api class.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class comment extends external_api {
    /**
     * Create comment parameters
     *
     * @return external_function_parameters
     */
    public static function add_parameters() {
        return new external_function_parameters([
            'comment' => new external_single_structure([
                'submissionid' => new external_value(PARAM_INT, 'The submission id', VALUE_REQUIRED),
                'message' => new external_value(PARAM_RAW, 'The post message', VALUE_REQUIRED)
            ])
        ]);
    }

    /**
     * Create comment method
     *
     * @param array $comment
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function add($comment) {
        global $DB, $USER;

        self::validate_parameters(self::add_parameters(), ['comment' => $comment]);

        $comment = (object)$comment;

        $sql = 'SELECT su.id, su.userid, p.id as portfolioid, p.course, p.name as portfolioname, se.id as sectionid, se.name as sectionname
                FROM {evokeportfolio_submissions} su
                INNER JOIN {evokeportfolio_sections} se ON se.id = su.sectionid
                INNER JOIN {evokeportfolio} p ON p.id = se.portfolioid
                WHERE su.id = :submissionid';

        $utildata = $DB->get_record_sql($sql, ['submissionid' => $comment->submissionid], MUST_EXIST);
        $cm = get_coursemodule_from_instance('evokeportfolio', $utildata->portfolioid);

        $context = context_course::instance($utildata->course);

        $usercomment = new \stdClass();
        $usercomment->submissionid = $comment->submissionid;
        $usercomment->userid = $USER->id;
        $usercomment->timecreated = time();
        $usercomment->timemodified = time();

        // Handle the mentions.
        $matches = [];
        preg_match_all('/<span(.*?)<\/span>/s', $comment->message, $matches);
        $replaces = [];
        $userstonotifymention = [];
        if (!empty($matches[0])) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $mention = $matches[0][$i];

                $useridmatches = null;
                preg_match( '@data-uid="([^"]+)"@' , $mention, $useridmatches);
                $userid = array_pop($useridmatches);

                if (!$userid) {
                    continue;
                }

                $user = user::get_by_id($userid, $context);

                if (!$user) {
                    continue;
                }

                $userprofilelink = new moodle_url('/user/view.php',  ['id' => $user->id, 'course' => $utildata->course]);
                $userprofilelink = html_writer::link($userprofilelink->out(false), fullname($user));

                $comment->message = str_replace($mention, "[replace{$i}]", $comment->message);

                $replaces['replace' . $i] = $userprofilelink;

                $userstonotifymention[] = $user->id;
            }
        }

        $usercomment->text = strip_tags($comment->message);

        foreach ($replaces as $key => $replace) {
            $usercomment->text = str_replace("[$key]", $replace, $usercomment->text);
        }

        $DB->insert_record('evokeportfolio_comments', $usercomment);

        $notification = new commentmention($context, $cm->id, $utildata->course, $utildata->sectionid, $utildata->portfolioname, $utildata->userid);

        if (!empty($userstonotifymention)) {
            $notification->send_mentions_notifications($userstonotifymention);
        }

        return [
            'status' => 'ok',
            'message' => $usercomment->text
        ];
    }

    /**
     * Create comment return fields
     *
     * @return external_single_structure
     */
    public static function add_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message')
            )
        );
    }
}