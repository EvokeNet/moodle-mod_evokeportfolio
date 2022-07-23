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
$string['evokeportfolio:grade'] = 'Permissão para avaliar usuários';
$string['evokeportfolio:submit'] = 'Permissão para fazer um envio no portfólio';
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
$string['eventcommentadded'] = 'Comentário adicionado';
$string['eventlikesent'] = 'Like enviado';
$string['gradinglocked'] = 'A avaliação está atualimente bloqueada para esta atividade.';
$string['viewsubmission'] = 'Visualizar envio';
$string['notsubmitted'] = 'Não enviado';
$string['evaluated'] = 'Avaliado';

$string['submissionsuccessmessage'] = 'Mensagem de envio com sucesso';
$string['evokation'] = 'É uma evokation?';
$string['datestart'] = 'Data de início dos envios';
$string['datestart_help'] = 'Estudentes só podem enviar seus trabalhos depois desta data.';
$string['datelimit'] = 'Data limite';
$string['datelimit_help'] = 'Após esta data, estudantes não podemo mais realizar envios.';
$string['groupactivity'] = 'Atividade em grupo';
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
$string['grade_help'] = 'Digite a nota do portfolio do estudante aqui.';
$string['onlynumbers'] = 'Somente números são aceitos.';
$string['gradefor'] = 'Nota para: {$a}';

$string['validation:commentrequired'] = 'Comentário é obrigatório';
$string['validation:commentlen'] = 'Comentário precisa ter pelo menos 10 caracteres';
$string['validation:graderequired'] = 'Nota é obrigatório';
$string['validation:commentattachmentsrequired'] = 'Você precisa pelo menos adicionar um comentário ou anexar um arquivo.';
$string['validation:namelen'] = 'Nome precisa ter pelo menos 3 caracteres';
$string['deleteitem_confirm_title'] = 'Você tem certeza?';
$string['deleteitem_confirm_msg'] = 'Uma vez deletado, este item não pode mais ser recuperado!';
$string['deleteitem_confirm_yes'] = 'Sim, pode deletar!';
$string['deleteitem_confirm_no'] = 'Cancelar';

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

$string['status'] = 'Status';
$string['portfoliochapters'] = 'Capítulos do Portfolio';
$string['portfoliograding'] = 'Avaliação do Portfolio';
$string['chaptersportfolios'] = 'Portfolios no capítulo';
$string['createchapter'] = 'Criar capítulo';
$string['courseportfolios'] = 'Portfolios no curso ';
$string['deletechapter_success'] = 'Capítulo excluído com sucesso.';
$string['createchapter_success'] = 'Capítulo criado com sucesso.';
$string['editchapter_success'] = 'Capítulo editado com sucesso.';
$string['editchapter'] = 'Editar capítulo';
$string['grading_success'] = 'Avaliação finalizada com sucesso.';
$string['graded'] = 'Avaliado';
$string['nosubmissions'] = 'Sem envios';
$string['nosubmissions_desc'] = 'Não há envios para este portfolio ainda.';

$string['myportfolios'] = 'Meus Portfolios';
$string['gototheportfolio'] = 'Ir para o portfolio';
$string['comment'] = 'Comentar';
$string['writeacomment'] = '+ Compartilhe seus pensamentos...';
$string['description'] = 'Descrição';

$string['message_mentioned'] = 'Você foi mencionado em um comentário';
$string['message_mentionedinaportfolio'] = 'Você foi mencionado em um comentário no portfolio <b>{$a}</b>';
$string['message_mentioncontextname'] = 'Mencionado em um comentário.';
$string['message_clicktoaccessportfolio'] = 'Clique aqui para acessar o portfolio';

$string['coursenoportfolio'] = 'Este curso não possui atividades do tipo portfolio';
$string['view'] = 'Visualizar';
$string['page_portfolio_title'] = 'Meus envios em um portfolio';

$string['completionrequiresubmit'] = 'Requer envio';
$string['completionrequiresubmit_help'] = 'O estudante precisa fazer um envio para completar esta atividade';

$string['messageprovider:commentmention'] = 'Notifica o usuário quando ele é mencionado em um comentário';

$string['nochapters'] = 'Não há capítulos disponíveis nesta missão.';
$string['backtomission'] = 'Voltar para a missão';
$string['assignments'] = 'Atividades';

$string['myportfolio'] = 'Meu Portfolio';
$string['teamportfolio'] = 'Portfolio do Time';
$string['networkportfolio'] = 'Portfolio da Rede';

$string['myevokation'] = 'Meu Evokation';
$string['teamevokation'] = 'Evokation do Time';
$string['networkevokation'] = 'Evokation da Rede';
$string['assessed'] = 'Avaliado';
$string['notassessed'] = 'Não avaliado';
$string['evokation'] = 'Evokation';
$string['allactivities'] = 'Todas as atividades';
$string['allgroups'] = 'Todos os grupos';

$string['evaluatechapter'] = 'Avaliar capítulo';
$string['chaptergrading'] = 'Avaliação de capítulo';

$string['assessment'] = 'Avaliação';
$string['notyetassessed'] = 'Não avaliado ainda';
