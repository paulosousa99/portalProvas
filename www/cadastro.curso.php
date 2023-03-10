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
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$curso		= addslashes(trim($_POST['curso']));
	$nome		= addslashes(trim($_POST['nome']));

	try {
		$banco->iniciarTransacao();

		$obj_instituicao = $sessionFacade->recuperarInstituicao($_login_instituicao);

		$instit = new Curso();
		$instit->setId($curso);
		$instit->setNome($nome);
		$instit->setInstituicao($obj_instituicao);

		$sessionFacade->gravarCurso($instit);
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: cadastro.curso.php?curso=".$instit->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Curso";
$sub_titulo = "Curso: Cadastrar";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['curso']) AND strlen(trim($_GET['curso']))>0){

	$curso = trim($_GET['curso']);
	try {
		$inst = $sessionFacade->recuperarCurso($curso); 

		if ( is_object($inst)){
			$curso		= $inst->getId();
			$nome		= $inst->getNome();
		}else{
			array_push($msg_erro,"Curso n?o encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informa??es salvas com sucesso!");
	}
}
if (strlen($curso)==0){
	array_push($msg_ok,"Cadastre um novo Curso! Preencha com o nome e clique em 'Gravar'");
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->assign_vars(array(	'CURSO'		=>	$curso,
							'NOME'		=>	$nome,
							'BTN_NOME'	=>  (strlen($curso)>0)?"Confirmar Altera??es":"Gravar"
));	


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>