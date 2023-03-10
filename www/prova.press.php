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

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "prova";
$titulo     = "Consulta de Prova";
$sub_titulo = "Prova: Consulta";

#include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['prova']) AND strlen(trim($_GET['prova']))>0){

	$prova = trim($_GET['prova']);
	try {
		$prov = $sessionFacade->recuperarProva($prova); 

		if ( is_object($prov)){
			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getNome();
			$curso				= $prov->getDisciplina()->getCurso()->getNome();
			$professor			= $prov->getProfessor()->getId();
			#$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			#$dificuldade		= $prov->getDificuldade();
		}else{
			throw new Exception('Prova n?o encontrada!');
			#array_push($msg_erro,"Prova n?o encontrada!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}


/*         PROFESSOR         */

try {
	$professores = $sessionFacade->recuperarProfessorTodosDAO();
	$cursos = $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa??es salvas com sucesso!");
	}
}

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	$disciplina,
							'CURSO'				=>	$curso,
							'PROFESSOR'			=>	optionProfessor($professores,$professor)
));	

if (is_object($prov)){
	for ($i=0;$i<$prov->getQtdePerguntas();$i++){
		$model->assign_block_vars('pergunta', array('I'				=>	$i,
													'PERGUNTA'		=>	$prov->getPergunta($i)->getId(),
													'TITULO_PERGUNTA'=>	$prov->getPergunta($i)->getTituloReduzido(25),
													'TOPICO'		=>	$prov->getPergunta($i)->getTopico()->getDescricao(),
													'TIPO'			=>	$prov->getPergunta($i)->getTipoPergunta()->getDescricao(),
													'DIFICULDADE'	=>	$prov->getPergunta($i)->getDificuldade('texto'),
													'CLASSE'		=>	$i%2==0?"class='odd'":""
													));
	}
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

#include "rodape.php";


?>