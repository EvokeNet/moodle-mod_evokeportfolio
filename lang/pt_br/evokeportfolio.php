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

$string['evokeportfolio:addinstance'] = 'Adicionar um portfolio';
$string['evokeportfolio:view'] = 'Visualizar portfolio';
$string['missingidandcmid'] = 'Faltando o ID do módulo do curso';
$string['modulename'] = 'Portfolio';
$string['modulename_help'] = 'Use o módulo portfolio para gerenciar as criações dos usuários...';
$string['modulenameplural'] = 'Portfolios';
$string['noportfolioinstances'] = 'Não foram encontradas instâncias de portfolios';
$string['pluginadministration'] = 'Administração do portfolio';
$string['pluginname'] = 'Portfolio';
$string['fieldset'] = 'Portfolio';
$string['name'] = 'Nome';
$string['view'] = 'Visualizar';
$string['eventsubmissionsent'] = 'Envio realizado';
$string['eventsubmissionupdated'] = 'Envio atualizado';
$string['gradinglocked'] = 'A avaliação está atualimente bloqueada para esta atividade.';
$string['viewsubmission'] = 'Visualizar envio';
$string['notsubmitted'] = 'Não enviado';
$string['evaluated'] = 'Avaliado';

$string['datelimit'] = 'Data limite';
$string['datelimit_help'] = 'Após esta data, estudantes não podemo mais realizar envios.';
$string['groupactivity'] = 'Atividade em grupo';
$string['individualctivity'] = 'Atividade individual';
$string['groupgradingmode'] = 'Modo de avaliação do grupo';
$string['groupgradingmode_help'] = '<b>Avaliação do grupo: </b> todos os usuários do grupo receberão a mesma nota.<br><b>Avaliação individual:</b> os usuários receberão notas individualmente.';
$string['groupgrading'] = 'Avaliação do grupo';
$string['individualgrading'] = 'Avaliação individual';
$string['section'] = 'Seção';
$string['illegalaccess'] = 'Acesso ilegal';
$string['attachmentfile'] = 'Arquivo de anexo';
$string['downloadfile'] = 'Baixar arquivo';

$string['page_view_gradingsummary'] = 'Resumo da avaliação';
$string['page_view_hidden'] = 'Oculto para estudantes';
$string['page_view_participants'] = 'Participantes';
$string['page_view_timeremaining'] = 'Tempo restante';
$string['page_view_viewallsubmissions'] = 'Visualizar todos os envios';

$string['page_view_submission_status'] = 'Statuso do envio';
$string['page_view_addsubmission'] = 'Adicionar envio';
$string['page_view_editsubmission'] = 'Editar envio';
$string['page_view_youhavenotsent'] = 'Você ainda não fez um envio.';
$string['page_view_submissions'] = 'Visualizar envios';
$string['page_view_usernotingroup_title'] = 'Desculpe!';
$string['page_view_usernotingroup_text'] = 'Você precisa fazer parte de um grupo para para realizar esta atividade.';

$string['page_submit_comment'] = 'Seu comentário';
$string['page_submit_attachments'] = 'Anexos';

$string['page_submissions_portfoliocomment'] = 'Mensagem';
$string['page_submissions_portfoliomentorcomment'] = 'Mensagem to mentor';
$string['page_submissions_portfolioattachment'] = 'Anexo';

$string['page_viewsubmission_addcomment'] = 'Adicionar comentário';
$string['page_viewsubmission_addgrade'] = 'Adicionar nota';
$string['page_viewsubmission_editgrade'] = 'Editar nota';
$string['page_entries_title'] = 'Envios do portfolio';

$string['save_comment_success'] = 'Comentário adicionado com sucesso.';
$string['save_grade_success'] = 'Nota adicionada com sucesso.';
$string['save_submission_success'] = 'Envio realizado com sucesso.';
$string['update_submission_success'] = 'Envio atualizado com sucesso';

$string['grade'] = 'Nota';
$string['grade_help'] = 'DIgite a nota do portfolio do estudante aqui.';
$string['onlynumbers'] = 'Somente números são aceitos.';
$string['gradefor'] = 'Nota para: {$a}';

$string['validation:commentrequired'] = 'Comentário é obrigatório';
$string['validation:commentlen'] = 'Comentário precisa ter pelo menos 10 caracteres';
$string['validation:graderequired'] = 'Nota é obrigatório';
$string['validation:commentattachmentsrequired'] = 'Você precisa pelo menos adicionar um comentário ou anexar um arquivo.';
$string['validation:namelen'] = 'Nome precisa ter pelo menos 3 caracteres';

$string['managesections'] = 'Gerenciar seções';
$string['createsection'] = 'Criar seção';
$string['editsection'] = 'Editar seção';
$string['actions'] = 'Ações';
$string['deleteitem_confirm_title'] = 'Você tem certeza?';
$string['deleteitem_confirm_msg'] = 'Uma vez deletado, este item não pode mais ser recuperado!';
$string['deleteitem_confirm_yes'] = 'Sim, pode deletar!';
$string['deleteitem_confirm_no'] = 'Cancelar';
$string['deletesection_success'] = 'Seção deletada com sucesso.';
$string['createsection_success'] = 'Seção criada com sucesso.';
$string['editsection_success'] = 'Seção editada com sucesso.';
$string['deletesection_hassubmissions'] = 'Não é possível deletar esta seção porque ela já possui envios feitos nela.';

$string['evokeportfolio:grade'] = 'Permissão para avaliar usuários';
$string['evokeportfolio:submit'] = 'Permissão para fazer um envio no portfólio';

$string['dependentcoursesections'] = 'Seções do curso dependentes';
$string['nosectionsavailable'] = 'Seções não disponíveis';
$string['nosectionsavailable_desc'] = 'Não existem seções disponíveis para você neste portfolio ainda..';

$string['privacy:metadata:evokeportfolio_submissions'] = 'Informações sobre os envios de um usuário em um portfolio';
$string['privacy:metadata:evokeportfolio_submissions:sectionid'] = 'O ID da seção do curso onde o portfolio está';
$string['privacy:metadata:evokeportfolio_submissions:userid'] = 'O ID do usuário relacionado a este portfolio';
$string['privacy:metadata:evokeportfolio_submissions:groupid'] = 'O ID do grupo relacionado a este portfolio';
$string['privacy:metadata:evokeportfolio_submissions:postedby'] = 'O ID do usuário que fez um envio neste portfolio';
$string['privacy:metadata:evokeportfolio_submissions:role'] = 'O papel do usuário que fez um envio';
$string['privacy:metadata:evokeportfolio_submissions:comment'] = 'O comentário do envio';
$string['privacy:metadata:evokeportfolio_submissions:commentformat'] = 'O formato do comentário do envio';
$string['privacy:metadata:evokeportfolio_submissions:timecreated'] = 'O timestamp indicando quando o envio foi feito pelo usuário';
$string['privacy:metadata:evokeportfolio_submissions:timemodified'] = 'O timestamp indicando quando o envio foi modificado pelo usuário';

$string['indicator:cognitivedepth'] = 'Evoke portfolio cognitivo';
$string['indicator:cognitivedepth_help'] = 'Este indicador baseia-se na profundidade cognitiva alcançada pelo estudante em uma atividade Portfolio.';
$string['indicator:cognitivedepthdef'] = 'Evoke portfolio cognitivo';
$string['indicator:cognitivedepthdef_help'] = 'O participante alcançou essa porcentagem do engajamento cognitivo oferecido pelas atividades Portfolio durante esse intervalo de análise (Níveis = Sem visualização, Visualizar, Enviar)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Evoke portfolio social';
$string['indicator:socialbreadth_help'] = 'Este indicador baseia-se na amplitude social alcançada pelo estudante em uma atividade Portfolio.';
$string['indicator:socialbreadthdef'] = 'Evoke portfolio social';
$string['indicator:socialbreadthdef_help'] = 'O participante alcançou essa porcentagem do engajamento social oferecido pelos recursos Portfolio durante esse intervalo de análise (Níveis = Sem participação, Participante único)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';

$string['completionrequiresubmit'] = 'Requer envio';
$string['completionrequiresubmit_help'] = 'O usuário precisa enviar a tarefa para concluir esta atividade';
