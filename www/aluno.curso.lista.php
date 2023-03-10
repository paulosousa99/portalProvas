<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/

##############################################################################
## INCLUDES E CONEX?ES BANCO
##############################################################################

session_start();
require_once "funcoes.php";
require_once "class/class.Template.inc.php";
require_once "class/class.SessionFacade.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";

$msg_erro	= array();
$msg		= "";


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Disciplinas Cadastrados";
$sub_titulo="Lista das Disciplinas Cadastradas";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa??es salvas com sucesso!");
	}
}

$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
	for($i= 0; $i < sizeof($disciplinas); $i++) { 
		$model->assign_block_vars('disciplina', array(	'DISCIPLINA'	=>	$disciplinas[$i]->getId(),
														'NOME'			=>	$disciplinas[$i]->getNome(),
														'PROFESSOR'		=>	is_object($disciplinas[$i]->getProfessor())?$disciplinas[$i]->getProfessor()->getNome():"",
														'CURSO'			=>	$disciplinas[$i]->getCurso()->getNome(),
														'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
