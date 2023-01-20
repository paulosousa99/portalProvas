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

if (isset($_GET['excluir'])){
	$excluir = trim($_GET['excluir']);
	if (strlen($excluir)>0){
		try {
			$banco->iniciarTransacao();
			$disc = $sessionFacade->recuperarDisciplina($excluir); 
			$sessionFacade->excluirDisciplina($disc);
			$banco->efetivarTransacao();
			$banco->desconecta(); 
			header("Location: cadastro.disciplina.lista.php?msg_codigo=2");
			exit;
		} catch(Exception $e) { 
			$banco->desfazerTransacao();
			array_push($msg_erro,$e->getMessage());
		}
	}
}


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

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa��es salvas com sucesso!");
	}
	if ($msg_codigo == 2){
		array_push($msg_ok,"Curso exclu�do com sucesso!");
	}
}

try {
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO('','opcional');
	for($i= 0; $i < count($disciplinas); $i++) { 
		$model->assign_block_vars('disciplina', array(	'DISCIPLINA'	=>	$disciplinas[$i]->getId(),
														'NOME'			=>	$disciplinas[$i]->getNome(),
														'PROFESSOR'		=>	is_object($disciplinas[$i]->getProfessor())?$disciplinas[$i]->getProfessor()->getNome():"",
														'CURSO'			=>	$disciplinas[$i]->getCurso()->getNome(),
														'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
	if (count($disciplinas)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma disciplina cadastrada!'));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de disciplinas cadastradas: ".$i));
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