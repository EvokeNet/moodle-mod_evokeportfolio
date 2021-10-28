<?php

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

/**
 * Reaction utility class helper
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class reaction {
    const LIKE = 1;

    public function toggle_reaction($submissionid, $reactionid) {
        global $USER, $DB;

        $params = [
            'submissionid' => $submissionid,
            'userid' => $USER->id,
            'reaction' => $reactionid
        ];

        $reaction = $DB->get_record('evokeportfolio_reactions', $params);

        if ($reaction) {
            $DB->delete_records('evokeportfolio_reactions', ['id' => $reaction->id]);
        } else {
            $params['timecreated'] = time();
            $params['timemodified'] = time();

            $DB->insert_record('evokeportfolio_reactions', $params);
        }

        return $this->get_reactions_string($submissionid, $reactionid);
    }

    public function get_reactions_string($submissionid, $reactionid) {
        $totalreactions = $this->get_total_reactions($submissionid, $reactionid);

        if (!$totalreactions) {
            return false;
        }

        $userreacted = $this->user_reacted($submissionid, $reactionid);

        if ($userreacted && $totalreactions == 1) {
            return get_string('reaction_youreacted', 'mod_evokeportfolio');
        }

        if ($userreacted && $totalreactions > 1) {
            $totalreactions--;

            return get_string('reaction_youandreacted', 'mod_evokeportfolio', $totalreactions);
        }

        return get_string('reaction_peoplereacted', 'mod_evokeportfolio', $totalreactions);
    }

    public function get_total_reactions($submissionid, $reactionid) {
        global $DB;

        return $DB->count_records('evokeportfolio_reactions', [
            'submissionid' => $submissionid,
            'reaction' => $reactionid
        ]);
    }

    public function user_reacted($submissionid, $reactionid) {
        global $USER, $DB;

        $params = [
            'submissionid' => $submissionid,
            'userid' => $USER->id,
            'reaction' => $reactionid
        ];

        $reaction = $DB->get_record('evokeportfolio_reactions', $params);

        if ($reaction) {
            return true;
        }

        return false;
    }
}