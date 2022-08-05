<?php

/**
 * Plugin strings are defined here.
 *
 * @package     mod_evokeportfolio
 * @category    string
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$string['evokeportfolio:addinstance'] = 'Add a portflio';
$string['evokeportfolio:view'] = 'View portfolio';
$string['evokeportfolio:grade'] = 'Permission to grade users';
$string['evokeportfolio:submit'] = 'Permission to send a portfolio submission';
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
$string['eventcommentadded'] = 'Comment added';
$string['eventlikesent'] = 'Like sent';
$string['gradinglocked'] = 'Grading for this mission is currently locked.';
$string['viewsubmission'] = 'View submission';
$string['notsubmitted'] = 'Not submitted';
$string['evaluated'] = 'Evaluated';

$string['submissionsuccessmessage'] = 'Submission success message';
$string['evokation'] = 'Is an evokation';
$string['datestart'] = 'Submission start date';
$string['datestart_help'] = 'Students can only submit their work after this date.';
$string['datelimit'] = 'Submission deadline';
$string['datelimit_help'] = 'After this date, agents cannot submit responses.';
$string['groupactivity'] = 'Team activity'; // team = group; mission = activity
$string['section'] = 'Section';
$string['illegalaccess'] = 'Unauthorized access';
$string['attachmentfile'] = 'Attachment file';
$string['downloadfile'] = 'Download file';

$string['page_view_gradingsummary'] = 'Grading summary';
$string['page_view_hidden'] = 'Hidden from students';
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

$string['page_submit_comment'] = 'Your post';
$string['page_submit_attachments'] = 'Attachments';

$string['page_submissions_portfoliocomment'] = 'Comment';
$string['page_submissions_portfoliomentorcomment'] = 'Mentor comments';
$string['page_submissions_portfolioattachment'] = 'Attachment';

$string['page_viewsubmission_addcomment'] = 'Add comment';
$string['page_viewsubmission_addgrade'] = 'Add grade';
$string['page_viewsubmission_editgrade'] = 'Edit grade';
$string['page_entries_title'] = 'Portfolio submissions';

$string['save_comment_success'] = 'Comment successfully added.';
$string['save_grade_success'] = 'Grade successfully saved.';
$string['save_submission_success'] = 'Submission successfully added.';
$string['update_submission_success'] = 'Submission successfully updated.';

$string['grade'] = 'Grade';
$string['grade_help'] = '<p><b>Done: </b>Student meet the required standards for this evidence<br><b>Not done: </b>Student didn\'t meet the required standards for this evidence</p>';
$string['onlynumbers'] = 'Only numbers are accepted.';
$string['gradefor'] = 'Grade for: {$a}';

$string['validation:commentrequired'] = 'Post text is required';
$string['validation:commentlen'] = 'Post text needs to be at least 10 characters long';
$string['validation:graderequired'] = 'Grade is required';
$string['validation:commentattachmentsrequired'] = 'You must add text to your post or submit a file.';
$string['validation:namelen'] = 'Name needs to contain at least 3 characters';
$string['deleteitem_confirm_title'] = 'Are you sure?';
$string['deleteitem_confirm_msg'] = 'Once deleted, this item cannot be recovered.';
$string['deleteitem_confirm_yes'] = 'Yes, delete it.';
$string['deleteitem_confirm_no'] = 'Cancel';

$string['privacy:metadata:evokeportfolio_submissions'] = 'Information about the user\'s submissions for a given portfolio activity';
$string['privacy:metadata:evokeportfolio_submissions:sectionid'] = 'The ID of the potfolio\'s section';
$string['privacy:metadata:evokeportfolio_submissions:userid'] = 'The ID of the user related to this portfolio activity';
$string['privacy:metadata:evokeportfolio_submissions:groupid'] = 'The ID of the group related to this portfolio activity';
$string['privacy:metadata:evokeportfolio_submissions:postedby'] = 'The ID of the user who posted a submission to this portfolio activity';
$string['privacy:metadata:evokeportfolio_submissions:role'] = 'The role of the user who posted a submission';
$string['privacy:metadata:evokeportfolio_submissions:comment'] = 'The submission comment';
$string['privacy:metadata:evokeportfolio_submissions:commentformat'] = 'The submission comment format';
$string['privacy:metadata:evokeportfolio_submissions:timecreated'] = 'The timestamp indicating when the submission was posted by the user';
$string['privacy:metadata:evokeportfolio_submissions:timemodified'] = 'The timestamp indicating when the submission was modified by the user';

$string['indicator:cognitivedepth'] = 'Evoke portfolio cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a Evoke portfolio activity.';
$string['indicator:cognitivedepthdef'] = 'Evoke portfolio cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the Evoke portfolio activities during this analysis interval (Levels = No view, View, Submit, View feedback)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Evoke portfolio social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a Evoke portfolio activity.';
$string['indicator:socialbreadthdef'] = 'Evoke portfolio social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the Evoke portfolio activities during this analysis interval (Levels = No participation, Participant alone, Participant with others)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';

$string['status'] = 'Status';
$string['portfoliochapters'] = 'Portfolio chapters';
$string['portfoliograding'] = 'Portfolio grading';
$string['chaptersportfolios'] = 'Portfolios in chapter';
$string['createchapter'] = 'Create chapter';
$string['courseportfolios'] = 'Portfolios in course';
$string['deletechapter_success'] = 'Chapter successfully deleted.';
$string['createchapter_success'] = 'Chapter successfully created.';
$string['editchapter_success'] = 'Chapter successfully edited.';
$string['editchapter'] = 'Edit chapter';
$string['grading_success'] = 'Grading successfully finished.';
$string['graded'] = 'Graded';
$string['nosubmissions'] = 'No submissions';
$string['nosubmissions_desc'] = 'There are no submissions for this portfolio yet.';

$string['myportfolios'] = 'My Portfolios';
$string['gototheportfolio'] = 'Go to the portfolio';
$string['comment'] = 'Comment';
$string['writeacomment'] = '+ Share your thoughts...';
$string['description'] = 'Description';
$string['editcomment'] = 'Edit comment';
$string['editcomment_success'] = 'Comment successfully edited.';
$string['edited'] = 'Edited';

$string['message_mentioned'] = 'You were mentioned in a comment';
$string['message_mentionedinaportfolio'] = 'You were mentioned in a comment in the portfolio <b>{$a}</b>';
$string['message_mentioncontextname'] = 'Mentioned in a comment.';
$string['message_clicktoaccessportfolio'] = 'Click here to access the portfolio';

$string['coursenoportfolio'] = 'This course does not have a portfolio assignment';
$string['view'] = 'View';
$string['page_portfolio_title'] = 'My submissions in a portfolio';

$string['completionrequiresubmit'] = 'Require submission';
$string['completionrequiresubmit_help'] = 'The user needs to submit to complete this activity';

$string['messageprovider:commentmention'] = 'Notify user and he/she is mentioned in a comment';

$string['nochapters'] = 'There are no chapters available in this mission.';
$string['backtomission'] = 'Back to mission';
$string['assignments'] = 'Assignments';

$string['myportfolio'] = 'My Portfolio';
$string['teamportfolio'] = 'Team Portfolio';
$string['networkportfolio'] = 'Network Portfolio';

$string['myevokation'] = 'My Evokation';
$string['teamevokation'] = 'Team Evokation';
$string['networkevokation'] = 'Network Evokation';
$string['assessed'] = 'Assessed';
$string['notassessed'] = 'Not Assessed';
$string['evokation'] = 'Evokation';
$string['allactivities'] = 'All activities';
$string['allgroups'] = 'All groups';

$string['evaluatechapter'] = 'Evaluate chapter';
$string['chaptergrading'] = 'Chapter grading';

$string['assessment'] = 'Assessment';
$string['notyetassessed'] = 'Not yet assessed';

$string['myteam'] = 'My team';
$string['activitytype'] = 'Activity type';