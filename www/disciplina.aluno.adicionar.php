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

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Disciplinas";
$sub_titulo = "Cursos e Disciplinas";

#include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.'.php'));


$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$linha = 0;
	$cursos = $sessionFacade->recuperarCursoTodosDAO();
	for($i= 0; $i < sizeof($cursos); $i++) { 
		$model->assign_block_vars('curso', array(	'CURSO'			=>	$cursos[$i]->getId(),
													'CURSO_NOME'	=>	$cursos[$i]->getNome(),
													'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
		$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO($cursos[$i]->getId());
		for($j= 0; $j < sizeof($disciplinas); $j++) { 
			$model->assign_block_vars('curso.disciplina', array(	'DISCIPLINA'	=>	$disciplinas[$j]->getId(),
																	'NOME'			=>	$disciplinas[$j]->getNome(),
																	'CURSO'			=>	$cursos[$i]->getId(),
																	'CURSO_NOME'	=>	$cursos[$i]->getNome(),
																	'I'				=>	$linha,
																	'CLASSE'		=>	$j%2==0?"class='odd'":""
			));
			$linha++;
		}
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa??es salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

?>
