<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/

##############################################################################
## INCLUDES E CONEX�ES BANCO
##############################################################################

session_start();
require_once "funcoes.php";
require_once "class/class.Template.inc.php";
require_once "class/class.SessionFacade.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";

$msg_erro	= array();
$msg_ok		= array();
$msg		= "";


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Topicos Cadastrados";
$sub_titulo="Lista dos Topicos Cadastradas";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.'.php'));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa��es salvas com sucesso!");
	}
}

try {
	$topicos = $sessionFacade->recuperarTopicoTodosDAO();
	for($i= 0; $i < sizeof($topicos); $i++) { 
		$model->assign_block_vars('topico', array(	'TOPICO'			=>	$topicos[$i]->getId(),
													'DISCIPLINA'		=>	$topicos[$i]->getDisciplina()->getNome(),
													'DESCRICAO'			=>	$topicos[$i]->getDescricao(),
													'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
	if (count($topicos)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhum t�pico cadastrado!'));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de T�picos cadastrados: ".$i));
	}

}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>