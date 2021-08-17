<?php
// This file is part of Moodle - https://moodle.org/
// Este archivo es parte de Moodle

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//Moodle es un software libre: puede re-distrubuidlo y/o modificarlo
 bajo los mismos términos de la licencia GNU (General Public License) como está publicado por 
la Free Software Foundation, tanto en la versión 3 de la Licencia, como en cualquier versión posterior.

// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// Moodle se distribuye con la esperanza de que sea útil,
pero SIN NINGUNA GARANTÍA; sin siquiera la garantía implícita de
// COMERCIABILIDAD o APTITUD PARA UN PROPÓSITO PARTICULAR. Ver la
// Licencia pública general GNU para más detalles.

// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
// Debería haber recibido una copia de la Licencia Pública General GNU
// junto con Moodle. De lo contrario, consulte <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 * Las cadenas de Plugin se definen a continuación.
 * @package     mod_evokeportfolio
 * @category    string
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.  <--NEED TO CHANGE THIS
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
$string['gradinglocked'] = 'La valoración de esta misión está actualmente bloqueada.'; 
$string['viewsubmission'] = 'Ver envío'; 
$string['notsubmitted'] = 'No enviado'; 
$string['evaluated'] = 'Valorado'; 

$string['datelimit'] = 'Fecha límite para el envío'; 
$string['datelimit_help'] = 'Tras esta fecha, los agentes no pueden enviar respuestas.'; 	
$string['groupactivity'] = 'Misión en grupo';
$string['individualctivity'] = 'Misión individual';
$string['groupgradingmode'] = 'Modo de valoración de equipo';
$string['groupgradingmode_help'] = '<b>Valoración en equipo: </b> todos los agentes del equipo recibirán la misma valoración.<br><b>Valoración individual:</b> los agentes recibirán la valoración individualmente.';
$string['groupgrading'] = 'Valoración en equipo'; 
$string['individualgrading'] = 'Valoración individual'; 
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
