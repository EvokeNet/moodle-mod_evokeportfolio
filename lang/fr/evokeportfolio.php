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

$string['evokeportfolio:addinstance'] = 'Ajouter un portfolio';
$string['evokeportfolio:view'] = 'Voir le portfolio';
$string['evokeportfolio:grade'] = 'Permission de noter les utilisateurs';
$string['evokeportfolio:submit'] = 'Permission d\'envoyer une soumission de portfolio';
$string['missingidandcmid'] = 'ID ou ID du module de cours manquant';
$string['modulename'] = 'Portfolio';
$string['modulename_help'] = 'Le module portfolio peut être utilisé pour gérer des collections de travaux d\'agents'; // agent = étudiant
$string['modulenameplural'] = 'Portfolios';
$string['noportfolioinstances'] = 'Aucun portefeuille n\'a été trouvé';
$string['pluginadministration'] = 'Administration du portfolio';
$string['pluginname'] = 'Portfolio';
$string['fieldset'] = 'Portfolio';
$string['name'] = 'Nom';
$string['view'] = 'Voir';
$string['eventsubmissionsent'] = 'Soumission envoyée';
$string['eventsubmissionupdated'] = "Soumission mise à jour";
$string['eventcommentadded'] = 'Commentaire ajouté';
$string['eventlikesent'] = 'Like envoyé';
$string['gradinglocked'] = 'L\'évaluation de cette mission est actuellement bloquée';
$string['viewsubmission'] = 'Voir la soumission';
$string['notsubmitted'] = 'Non soumis';
$string['evaluated'] = 'Évalué';
$string['actions'] = 'Actions';

$string['submissionsuccessmessage'] = 'Message de réussite de la soumission';
$string['evokation'] = 'Est une evokation';
$string['datestart'] = 'Date de début de la soumission';
$string['datestart_help'] = 'Les étudiants ne peuvent remettre leur travail qu\'après cette date';
$string['datelimit'] = 'Date limite de soumission';
$string['datelimit_help'] = 'Après cette date, les agents ne peuvent plus soumettre de réponses.';
$string['groupactivity'] = 'Activité d\'équipe'; // équipe = groupe;mission = activité
$string['section'] = 'Section';
$string['illegalaccess'] = 'Accès non autorisé';
$string['attachmentfile'] = 'Attachment file';
$string['downloadfile'] = 'Télécharger le fichier';

$string['page_view_gradingsummary'] = 'Résumé des notes';
$string['page_view_hidden'] = 'Caché aux étudiants';
$string['page_view_participants'] = 'Participants';
$string['page_view_timeremaining'] = 'Temps restant';
$string['page_view_viewallsubmissions'] = 'Voir toutes les soumissions';

$string['page_view_submission_status'] = 'Statut de la soumission';
$string['page_view_addsubmission'] = 'Ajouter une soumission';
$string['page_view_editsubmission'] = 'Modifier la soumission';
$string['page_view_youhavenotsent'] = 'Vous n\'avez encore rien soumis';
$string['page_view_submissions'] = 'Voir les soumissions';
$string['page_view_usernotingroup_title'] = 'Désolé!';
$string['page_view_usernotingroup_text'] = 'Vous devez faire partie d\'une équipe pour accomplir cette mission';

$string['page_submit_comment'] = 'Votre post';
$string['page_submit_attachments'] = 'Pièces jointes';

$string['page_submissions_portfoliocomment'] = 'Commentaire';
$string['page_submissions_portfoliomentorcomment'] = 'Commentaires du mentor';
$string['page_submissions_portfolioattachment'] = 'Pièce jointe';

$string['page_viewsubmission_addcomment'] = 'Ajouter un commentaire';
$string['page_viewsubmission_addgrade'] = 'Ajouter une note';
$string['page_viewsubmission_editgrade'] = 'Éditer la note';
$string['page_entries_title'] = 'Soumissions du portfolio';

$string['save_comment_success'] = 'Commentaire ajouté avec succès.';
$string['save_grade_success'] = 'Note sauvegardée avec succès.';
$string['save_submission_success'] = 'Soumission ajoutée avec succès';
$string['update_submission_success'] = 'Soumission mise à jour avec succès.';

$string['grade'] = 'Note';
$string['grade_help'] = '<p><b>Done : </b>L\'étudiant répond aux exigences requises pour cette preuve<br><b>Non réalisé : </b>L\'étudiant ne répond pas aux exigences requises pour cette preuve</p>';
$string['onlynumbers'] = 'Seuls les nombres sont acceptés';
$string['gradefor'] = 'Note pour : {$a}';

$string['validation:commentrequired'] = 'Le texte du message est obligatoire';
$string['validation:commentlen'] = 'Le texte du message doit comporter au moins 10 caractères';
$string['validation:graderequired'] = "La note est obligatoire";
$string['validation:commentattachmentsrequired'] = 'Vous devez ajouter du texte à votre message ou soumettre un fichier.';
$string['validation:namelen'] = 'Le nom doit contenir au moins 3 caractères';
$string['deleteitem_confirm_title'] = 'Êtes-vous sûr?';
$string['deleteitem_confirm_msg'] = 'Une fois supprimé, cet élément ne peut être récupéré';
$string['deleteitem_confirm_yes'] = 'Oui, supprimez-le';
$string['deleteitem_confirm_no'] = 'Annuler';

$string['privacy:metadata:evokeportfolio_submissions'] = 'Informations sur les soumissions de l\'utilisateur pour une activité de portfolio donnée';
$string['privacy:metadata:evokeportfolio_submissions:sectionid'] = 'L\'identifiant de la section du portfolio';
$string['privacy:metadata:evokeportfolio_submissions:userid'] = 'L\'identifiant de l\'utilisateur lié à cette activité de portefeuille';
$string['privacy:metadata:evokeportfolio_submissions:groupid'] = 'L\'identifiant du groupe lié à cette activité de portefeuille';
$string['privacy:metadata:evokeportfolio_submissions:postedby'] = 'L\'identifiant de l\'utilisateur qui a envoyé une soumission à cette activité de portefeuille';
$string['privacy:metadata:evokeportfolio_submissions:role'] = 'Le rôle de l\'utilisateur qui a posté une soumission';
$string['privacy:metadata:evokeportfolio_submissions:comment'] = 'Le commentaire de la soumission';
$string['privacy:metadata:evokeportfolio_submissions:commentformat'] = 'Le format de commentaire de la soumission';
$string['privacy:metadata:evokeportfolio_submissions:timecreated'] = 'L\'horodatage indiquant quand la soumission a été postée par l\'utilisateur';
$string['privacy:metadata:evokeportfolio_submissions:timemodified'] = 'L\'horodatage indiquant quand la soumission a été modifiée par l\'utilisateur';

$string['indicator:cognitivedepth'] = 'Porfolio Evoke cognitif';
$string['indicator:cognitivedepth_help'] = 'Cet indicateur est basé sur la profondeur cognitive atteinte par l\'étudiant dans une activité du portfolio Evoke';
$string['indicator:cognitivedepthdef'] = 'Porfolio Evoke cognitif';
$string['indicator:cognitivedepthdef_help'] = 'Le participant a atteint ce pourcentage de l\'engagement cognitif offert par les activités du portefeuille Evoke au cours de cet intervalle d\'analyse (Niveaux = Pas de vue, Vue, Soumettre, Voir le retour d\'information)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Portfolio Evoke social';
$string['indicator:socialbreadth_help'] = 'Cet indicateur est basé sur l\'étendue sociale atteinte par l\'étudiant dans une activité du portfolio Evoke';
$string['indicator:socialbreadthdef'] = 'Evoke portfolio social';
$string['indicator:socialbreadthdef_help'] = 'Le participant a atteint ce pourcentage d\'engagement social offert par les activités du portefeuille Evoke pendant cet intervalle d\'analyse (Niveaux = Pas de participation, Participant seul, Participant avec d\'autres)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';

$string['status'] = 'Statut';
$string['portfoliochapters'] = 'Chapitres du portfolio';
$string['portfoliograding'] = 'Notation du portfolio';
$string['chaptersportfolios'] = 'Portfolios dans le chapitre';
$string['createchapter'] = 'Créer un chapitre';
$string['courseportfolios'] = 'Portfolios dans le cours';
$string['deletechapter_success'] = 'Chapitre supprimé avec succès.';
$string['createchapter_success'] = 'Chapitre créé avec succès.';
$string['editchapter_success'] = 'Chapitre édité avec succès';
$string['editchapter'] = 'Éditer le chapitre';
$string['grading_success'] = 'Notation terminée avec succès';
$string['graded'] = 'Noté';
$string['nosubmissions'] = 'Aucune soumission';
$string['nosubmissions_desc'] = 'Il n\'y a pas encore de soumissions pour ce portfolio';

$string['myportfolios'] = 'Mes portfolios';
$string['gototheportfolio'] = 'Aller au portfolio';
$string['comment'] = 'Commentaire';
$string['writeacomment'] = '+ Partagez vos pensées...';
$string['description'] = 'Description';
$string['editcomment'] = 'Éditer le commentaire';
$string['editcomment_success'] = 'Commentaire édité avec succès.';
$string['edited'] = 'Édité';

$string['message_mentioned'] = 'Vous avez été mentionné dans un commentaire';
$string['message_mentionedinaportfolio'] = 'Vous avez été mentionné dans un commentaire dans le portfolio <b>{$a}</b>';
$string['message_mentioncontextname'] = 'Mentionné dans un commentaire';
$string['message_clicktoaccessportfolio'] = 'Cliquer ici pour accéder au portefeuille';

$string['coursenoportfolio'] = 'Ce cours n\'a pas de portfolio';
$string['view'] = 'Voir';
$string['page_portfolio_title'] = 'Mes soumissions dans un portfolio';

$string['completionrequiresubmit'] = 'Exiger une soumission';
$string['completionrequiresubmit_help'] = 'L\'utilisateur doit soumettre pour terminer cette activité';

$string['messageprovider:commentmention'] = 'Notifier l\'utilisateur lorsqu\'il est mentionné dans un commentaire';

$string['nochapters'] = 'Il n\'y a pas de chapitres disponibles dans cette mission';
$string['backtomission'] = 'Revenir à la mission';
$string['assignments'] = 'Devoirs';

$string['myportfolio'] = 'Mon portfolio';
$string['teamportfolio'] = 'Portfolio de l\'équipe';
$string['networkportfolio'] = 'Portfolio du réseau';

$string['myevokation'] = 'Mon Evokation';
$string['teamevokation'] = 'Evokation de l\'équipe';
$string['networkevokation'] = 'Evokation du réseau';
$string['assessed'] = 'Évalué';
$string['notassessed'] = 'Non évalué';
$string['evokation'] = 'Evokation';
$string['allactivities'] = 'Toutes les activités';
$string['allgroups'] = 'Tous les groupes';
$string['selectagroup'] = 'Sélectionner un groupe';

$string['evaluatechapter'] = 'Évaluer le chapitre';
$string['chaptergrading'] = 'Notation du chapitre';

$string['assessment'] = 'Devoir';
$string['notyetassessed'] = 'Pas encore évalué';

$string['myteam'] = 'Mon équipe';
$string['activitytype'] = 'Type d\'activité';

$string['lastupdated'] = 'Dernière mise à jour il y a {$a}';
$string['totalsubmissions'] = 'Total des soumissions : {$a}';
$string['viewevokation'] = 'Voir evokation';
$string['noevokation'] = 'Il n\'y a pas encore d\'evokation disponible';

$string['page_evokation_title'] = 'Le projet final de votre équipe';
$string['page_evokation_view_button'] = 'Voir le projet final';
