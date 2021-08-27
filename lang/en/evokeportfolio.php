<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_evokeportfolio
 * @category    string
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.  <--NEED TO CHANGE THIS
 */

defined('MOODLE_INTERNAL') || die();

$string['evokeportfolio:addinstance'] = 'Add a portfolio';
$string['evokeportfolio:view'] = 'View portfolio';
$string['missingidandcmid'] = 'Missing ID or Course Module ID';
$string['modulename'] = 'Portfolio';
$string['modulename_help'] = 'The portfolio module may be used to manage collections of agent work'; // agent = student
$string['modulenameplural'] = 'Portfolios';
$string['noportfolioinstances'] = 'No porfolios were found';
$string['pluginadministration'] = 'Portfolio administration';
$string['pluginname'] = 'Portfolio';
$string['fieldset'] = 'Portfolio';
$string['name'] = 'Name';
$string['view'] = 'View';
$string['eventsubmissionsent'] = 'Submission sent';
$string['eventsubmissionupdated'] = 'Submission updated';
$string['gradinglocked'] = 'Grading for this mission is currently locked.';
$string['viewsubmission'] = 'View submission';
$string['notsubmitted'] = 'Not submitted';
$string['evaluated'] = 'Evaluated';

$string['datelimit'] = 'Submission deadline';
$string['datelimit_help'] = 'After this date, agents cannot submit responses.';
$string['groupactivity'] = 'Team mission'; // team = group; mission = activity
$string['individualctivity'] = 'Individual mission';
$string['groupgradingmode'] = 'Team grading mode';
$string['groupgradingmode_help'] = '<b>Team grading: </b> all users in the team will receive the same grade.<br><b>Individual grading:</b> users will receive their grades individually.';
$string['groupgrading'] = 'Team grading';
$string['individualgrading'] = 'Individual grading';
$string['section'] = 'Section';
$string['illegalaccess'] = 'Unauthorized access';
$string['attachmentfile'] = 'Attachment file';
$string['downloadfile'] = 'Download file';

$string['page_view_gradingsummary'] = 'Grading summary';
$string['page_view_hidden'] = 'Hidden from agents';
$string['page_view_participants'] = 'Participants';
$string['page_view_timeremaining'] = 'Time remaining';
$string['page_view_viewallsubmissions'] = 'View all submissions';

$string['page_view_submission_status'] = 'Submission status';
$string['page_view_addsubmission'] = 'Add submission';
$string['page_view_editsubmission'] = 'Edit submission';
$string['page_view_youhavenotsent'] = 'You have not submitted anything yet.';
$string['page_view_submissions'] = 'View submissions';
$string['page_view_usernotingroup_title'] = 'Sorry!';
$string['page_view_usernotingroup_text'] = 'You need to be part of a team to do this mission.';

$string['page_submit_comment'] = 'Your comment';
$string['page_submit_attachments'] = 'Attachments';

$string['page_submissions_portfoliocomment'] = 'Comment';
$string['page_submissions_portfoliomentorcomment'] = 'Mentor Comments';
$string['page_submissions_portfolioattachment'] = 'Attachment';

$string['page_viewsubmission_addcomment'] = 'Add comment';
$string['page_viewsubmission_addgrade'] = 'Add grade';
$string['page_viewsubmission_editgrade'] = 'Edit grade';
$string['page_entries_title'] = 'Portfolio submissions';

$string['save_comment_success'] = 'Comment successfully added.';
$string['save_grade_success'] = 'Grede successfully saved.';
$string['save_submission_success'] = 'Submission successfully added.';
$string['update_submission_success'] = 'Submission successfully updated.';

$string['grade'] = 'Grade';
$string['grade_help'] = 'Enter the grade for the agent\'s portfolio here.';
$string['onlynumbers'] = 'Only numbers are accepted.';
$string['gradefor'] = 'Grade for: {$a}';

$string['validation:commentrequired'] = 'A comment is required';
$string['validation:commentlen'] = 'Comment needs to be at least 10 characters long';
$string['validation:graderequired'] = 'Grade is required';
$string['validation:commentattachmentsrequired'] = 'You must add a comment or submit a file.';
$string['validation:namelen'] = 'Name needs to contain at least 3 characters';

$string['managesections'] = 'Manage sections';
$string['createsection'] = 'Create section';
$string['editsection'] = 'Edit section';
$string['actions'] = 'Actions';
$string['deleteitem_confirm_title'] = 'Are you sure?';
$string['deleteitem_confirm_msg'] = 'Once deleted, this item cannot be recovered!';
$string['deleteitem_confirm_yes'] = 'Yes, delete it!';
$string['deleteitem_confirm_no'] = 'Cancel';
$string['deletesection_success'] = 'Section successfully deleted.';
$string['createsection_success'] = 'Section successfully created.';
$string['editsection_success'] = 'Section successfully edited.';
$string['deletesection_hassubmissions'] = 'It is not possible to delete this section because it contains submissions.';

$string['evokeportfolio:grade'] = 'Permission to grade users';
$string['evokeportfolio:submit'] = 'Permission to send a portfolio submission';

$string['dependentcoursesections'] = 'Dependent course sections';
$string['nosectionsavailable'] = 'No sections available';
$string['nosectionsavailable_desc'] = 'There is no sections available for you in this portfolio yet.';