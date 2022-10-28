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

$string['evokeportfolio:addinstance'] = 'Añadir un portafolio';
$string['evokeportfolio:view'] = 'Ver el portafolio';
$string['missingidandcmid'] = 'ID del curso o del módulo faltante';
$string['modulename'] = 'Portafolio';
$string['modulename_help'] = 'El módulo del portfolio puede ser usado para administrar los trabajos de los agentes';
$string['modulenameplural'] = 'Portafolios';
$string['noportfolioinstances'] = 'No se encontró ningún portafolio';
$string['pluginadministration'] = 'Administración del portafolio';
$string['pluginname'] = 'Portafolio';
$string['fieldset'] = 'Portafolio';
$string['name'] = 'Nombre';
$string['view'] = 'Ver';
$string['eventsubmissionsent'] = 'Enviado';
$string['eventsubmissionupdated'] = 'Envío actualizado';
$string['eventcommentadded'] = 'Comentario actualizado'; //CHECK
$string['eventlikesent'] = 'Te gustó'; //CHECK
$string['gradinglocked'] = 'La valoración de esta misión está actualmente bloqueada.';
$string['viewsubmission'] = 'Ver envío';
$string['notsubmitted'] = 'No enviado';
$string['evaluated'] = 'Valorado';

$string['submissionsuccessmessage'] = 'Submission success message';
$string['evokation'] = 'Is an evokation';
$string['datestart'] = 'Submission start date';
$string['datestart_help'] = 'Students can only submit their work after this date.';
$string['datelimit'] = 'Fecha límite para el envío';
$string['datelimit_help'] = 'Tras esta fecha, los agentes no pueden enviar respuestas.';
$string['groupactivity'] = 'Misión en grupo';
$string['section'] = 'Sección';
$string['illegalaccess'] = 'Acceso no autorizado';
$string['attachmentfile'] = 'Archivo adjunto';
$string['downloadfile'] = 'Descargar archivo';

$string['page_view_gradingsummary'] = 'Resumen de valoraciones';
$string['page_view_hidden'] = 'Oculto para agentes';
$string['page_view_participants'] = 'Participantes';
$string['page_view_timeremaining'] = 'Tiempo restante';
$string['page_view_viewallsubmissions'] = 'Ver todos los envíos';

$string['page_view_submission_status'] = 'Estado del envío';
$string['page_view_addsubmission'] = 'Añadir envío ';
$string['page_view_editsubmission'] = 'Editar envío';
$string['page_view_youhavenotsent'] = 'Todavía no ha enviado nada';
$string['page_view_submissions'] = 'Ver envíos';
$string['page_view_usernotingroup_title'] = '¡Lo siento!';
$string['page_view_usernotingroup_text'] = 'Necesita ser parte de un equipo para hacer esta misión';

$string['page_submit_comment'] = 'Su comentario';
$string['page_submit_attachments'] = 'Adjuntos';
$string['nohavesectionaccess'] = 'Aún no tienes acceso a esa sección.'; //CHECK

$string['page_submissions_portfoliocomment'] = 'Comentario';
$string['page_submissions_portfoliomentorcomment'] = 'Comentarios del mentor';
$string['page_submissions_portfolioattachment'] = 'Adjuntos';

$string['page_viewsubmission_addcomment'] = 'Añadir comentario';
$string['page_viewsubmission_addgrade'] = 'Añadir valoración';
$string['page_viewsubmission_editgrade'] = 'Editar valoración';
$string['page_entries_title'] = 'Portafolio de envíos';

$string['save_comment_success'] = 'Comentario añadido con éxito';
$string['save_grade_success'] = 'Valoración guardada con éxito';
$string['save_submission_success'] = 'Envío añadido con éxito';
$string['update_submission_success'] = 'Envío actualizado con éxito';

$string['grade'] = 'Calificación';
$string['grade_help'] = ' Calificar el portafolio del estudiante aquí';
$string['onlynumbers'] = 'Solo se aceptan números';
$string['gradefor'] = 'Valoración para: {$a}';

$string['validation:commentrequired'] = 'Se requiere agregar un comentario';
$string['validation:commentlen'] = 'El comentario debe tener al menos 10 caracteres de longitud';
$string['validation:graderequired'] = 'Se requiere una valoración';
$string['validation:commentattachmentsrequired'] = 'Debe añadir un comentario o enviar un archivo';
$string['validation:namelen'] = 'El nombre debe tener al menos 3 caracteres de longitud';
$string['managesections'] = 'Administrar secciones';
$string['createsection'] = 'Crear sección';
$string['editsection'] = 'Editar sección';
$string['actions'] = 'Acciones';
$string['deleteitem_confirm_title'] = '¿Estás seguro?';
$string['deleteitem_confirm_msg'] = 'Una vez eliminado, este item no puede ser recuperado.';
$string['deleteitem_confirm_yes'] = 'Si, eliminar';
$string['deleteitem_confirm_no'] = 'Cancelar';
$string['deletesection_success'] = 'Sección eliminada correctamente';
$string['createsection_success'] = 'Sección creada correctamente';
$string['editsection_success'] = 'Sección editada correctamente';
$string['deletesection_hassubmissions'] = 'No es posible eliminar esta sección porque ya contiene envíos';
$string['evokeportfolio:grade'] = 'Permisos para evaluar a los agentes';
$string['evokeportfolio:submit'] = 'Permisos para enviar una evidencia de misión al portafolio';
$string['dependentcoursesections'] = 'Secciones dependientes de la campaña';
$string['nosectionsavailable'] = 'No hay secciones disponibles';
$string['nosectionsavailable_desc'] = 'Aún no hay secciones disponibles en el portafolio.';

$string['privacy:metadata:evokeportfolio_submissions'] = 'Información sobre las evidencias de misión enviadas por los agentes para una actividad del portafolio determinada';
$string['privacy:metadata:evokeportfolio_submissions:sectionid'] = 'ID de la sección del portafolio';
$string['privacy:metadata:evokeportfolio_submissions:userid'] = 'ID del agente relacionado con esta evidencia de misión enviada al portafolio';
$string['privacy:metadata:evokeportfolio_submissions:groupid'] = 'ID del equipo relacionado con esta evidencia de misión enviada al portafolio';
$string['privacy:metadata:evokeportfolio_submissions:postedby'] = 'ID del agente que envío esta evidencia de misión al portafolio';
$string['privacy:metadata:evokeportfolio_submissions:role'] = 'Rol del agente que realizó el envío';
$string['privacy:metadata:evokeportfolio_submissions:comment'] = 'Envío de comentario';
$string['privacy:metadata:evokeportfolio_submissions:commentformat'] = 'Formato del envío de comentario';
$string['privacy:metadata:evokeportfolio_submissions:timecreated'] = 'Marca de tiempo que indica cuándo se realizó el envío por parte del agente';
$string['privacy:metadata:evokeportfolio_submissions:timemodified'] = 'Marca de tiempo que indica cuándo fue la última vez que se modificó el envío por parte del agente';

$string['indicator:cognitivedepth'] = 'Nivel de participación en el portafolio';
$string['indicator:cognitivedepth_help'] = 'Este indicador se basa en el nivel de profundidad cognitiva lograda por el agente en la actividad de portafolio de Evoke.';
$string['indicator:cognitivedepthdef'] = 'Evoke portfolio engagement';
$string['indicator:cognitivedepthdef_help'] = 'El agente ha alcanzado este porcentaje de compromiso cognitivo disponible en el portafolio de Evoke durante este intervalo de análisis (Niveles = No mostrar, Mostrar , Enviar, Mostrar realimentación';
$string['indicator:cognitivedepthdef_link'] = 'Indicadores de analíticas de aprendizaje #Engagement_depth';
$string['indicator:socialbreadth'] = 'Compromiso social del portafolio de Evoke';
$string['indicator:socialbreadth_help'] = 'Este indicador se basa en la amplitud social alcanzada por el agente en una evidencia de misión enviada al portafolio de Evoke';
$string['indicator:socialbreadthdef'] = 'Compromiso social del portafolio de Evoke';
$string['indicator:socialbreadthdef_help'] = 'El agente ha alcanzado este porcentaje de compromiso social disponible en el portfolio de Evoke durante este intervalo de análisis (Niveles = No participó, Participó individualmente, Participó de forma grupal)';
$string['indicator:socialbreadthdef_link'] = 'Indicadores de analíticas de aprendizaje #Social_breadth';

$string['status'] = 'Estado';  // need to double-check language from here on
$string['portfoliochapters'] = 'Capítulos del portafolio';
$string['portfoliograding'] = 'Calificación de portafolio';
$string['chaptersportfolios'] = 'Portafolios de los capítulos';
$string['createchapter'] = 'Crear capítulo';
$string['courseportfolios'] = 'Portafolios del curso';
$string['deletechapter_success'] = 'Capítulo eliminado correctamente.';
$string['createchapter_success'] = 'Capítulo creado con éxito.';
$string['editchapter_success'] = 'Capítulo editado exitosamente.';
$string['grading_success'] = 'Calificación finalizada con éxito.';
$string['editchapter'] = 'Editar capítulo';
$string['graded'] = 'Calificada';
$string['nosubmissions'] = 'No hay envíos';
$string['nosubmissions_desc'] = 'Este usuario aún no ha realizado un envío para este portafolio.';

$string['myportfolios'] = 'Mis portafolios';
$string['gototheportfolio'] = 'Ir al portafolio';
$string['comment'] = 'Comentario';
$string['writeacomment'] = 'Escribir un comentario';
$string['description'] = 'Descripción';

$string['message_mentioned'] = 'Fuiste mencionado en un comentario';
$string['message_mentionedinaportfolio'] = 'Se te mencionó en un comentario en el portafolio <b>{$a}</b>';
$string['message_mentioncontextname'] = 'Mencionado en un comentario.';
$string['message_clicktoaccessportfolio'] = 'Haga clic aquí para acceder al portafolio';

$string['coursenoportfolio'] = 'This course does not have a portfolio';
// $string['view'] = 'Mirar';   // DUPLICATE
$string['page_portfolio_title'] = 'Mis envíos en un portafolio';

$string['completionrequiresubmit'] = 'Require submission';
$string['completionrequiresubmit_help'] = 'The user needs to submit to complete this activity';

$string['messageprovider:commentmention'] = 'Notify user and he/she is mentioned in a comment';

$string['page_evokation_title'] = 'Your Team’s Final Project';
$string['page_evokation_view_button'] = 'View final project';