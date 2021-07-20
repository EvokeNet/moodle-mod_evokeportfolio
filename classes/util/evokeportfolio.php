<?php
// This file is part of BBCalendar block for Moodle - http://moodle.org/
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

namespace mod_evokeportfolio\util;

defined('MOODLE_INTERNAL') || die();

class evokeportfolio {
    public function has_submission($cmid, $userid, $groupid = null) {
        global $DB;

        if ($groupid) {
            $entries = $DB->count_records('evokeportfolio_entries', ['cmid' => $cmid, 'groupid' => $groupid]);

            if ($entries) {
                return true;
            }

            return false;
        }

        $entries = $DB->count_records('evokeportfolio_entries', ['cmid' => $cmid, 'userid' => $userid]);

        if ($entries) {
            return true;
        }

        return false;
    }
}