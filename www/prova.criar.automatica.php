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
	
	$prova				= addslashes(trim($_POST['prova']));
	$titulo_prova		= addslashes(trim($_POST['titulo_prova']));
	$curso				= addslashes(trim($_POST['curso']));
	$disciplina			= addslashes(trim($_POST['disciplina']));
	$numero_perguntas	= addslashes(trim($_POST['numero_perguntas']));
	$data_inicio		= addslashes(trim($_POST['data_inicio']));
	$data_termino		= addslashes(trim($_POST['data_termino']));
	$dificuldade		= addslashes(trim($_POST['dificuldade']));
	$topicos			= $_POST['topicos'];
	#$qtde_topicos		= addslashes(trim($_POST['qtde_topicos']));

	try {
		$banco->iniciarTransacao();

		$prov = new Prova();
		$prov->setId($prova);
		$prov->setTitulo($titulo_prova);
		$prov->setNumeroPerguntas($numero_perguntas);
		$prov->setData(date("d/m/Y H:i:s"));
		$prov->setDataInicio($data_inicio);
		$prov->setDataTermino($data_termino);
		$prov->setDificuldade($dificuldade);
		$prov->setLiberada($data_inicio);

		if (strlen($disciplina)>0){
			$disc = $sessionFacade->recuperarDisciplina($disciplina); 
			$prov->setDisciplina($disc);
		}

		$prof = $sessionFacade->recuperarProfessor($_login_professor); 
		$prov->setProfessor($prof);

		/* Topicos */
		while (list ($key,$val) = @each ($topicos)) {
			if (strlen($val)>0){
				$topic = $sessionFacade->recuperarTopico($val); 
				if ( is_object($topic)){
					$prov->addTopico($topic);
				}
			}
		}
/*
		for ($i=0; $i<count($topicos);$i++){
			if (strlen($topicos[$i])>0){
				$topic = $sessionFacade->recuperarTopico($topicos[$i]); 
				if ( is_object($topic)){
					$prov->addTopico($topic);
				}
			}
		}
*/
		$sessionFacade->gravarProva($prov);
		$sessionFacade->gravaDadosProvaTopico($prov);
		$sessionFacade->selecionaPerguntas($prov);
		$sessionFacade->distruiProvaAluno($prov);
		$sessionFacade->gravaDadosProvaPerguntas($prov);

		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: prova.criar.automatica.php?prova=".$prov->getId()."&msg_codigo=1");
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

$layout     = "prova";
$titulo     = "Agendar Prova";
$sub_titulo = "Prova: Agendar";

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
	
if (isset($_GET['prova']) AND strlen(trim($_GET['prova']))>0){

	$prova = trim($_GET['prova']);
	try {
		$prov = $sessionFacade->recuperarProva($prova); 

		if ( is_object($prov)){
			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getId();
			$curso				= $prov->getDisciplina()->getCurso()->getId();
			$professor			= $prov->getProfessor()->getId();
			$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			$dificuldade		= $prov->getDificuldade();
		}else{
			array_push($msg_erro,"Prova n�o encontrada!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}


/*         PROFESSOR, CURSO, DISCIPLINAS         */

try {
	$professores = $sessionFacade->recuperarProfessorTodosDAO();
	$cursos      = $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){

		array_push($msg_ok,"Informa��es salvas com sucesso!");
		array_push($msg_ok,"<a href='prova.criar.manual.php?prova=$prova'>CLIQUE AQUI PARA ABRIR A PROVA</a>");
	}
}

if (strlen($dificuldade)==0){
	$dificuldade = 25;
}

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE_1'		=>	($dificuldade==25)?' CHECKED ':'',
							'DIFICULDADE_2'		=>	($dificuldade==50)?' CHECKED ':'',
							'DIFICULDADE_3'		=>	($dificuldade==75)?' CHECKED ':'',
							'DISCIPLINA'		=>	optionDisciplina($disciplinas,$disciplina),
							'CURSO'				=>	optionCurso($cursos,$curso),
							'PROFESSOR'			=>	optionProfessor($professores,$professor),
							'BTN_NOME'			=>  (strlen($prova)>0)?"Confirmar Altera��es":"Agendar Prova"
));	

$lista_topicos = array();
if (is_object($prov)){
	for ($i=0;$i<$prov->getQtdeTopico();$i++){
		array_push($lista_topicos,$prov->getTopico($i)->getId());
	}
}

if (count($disciplinas)){
	$count=0;
	for ($i=0; $i<count($disciplinas); $i++){
		$id_disc = $disciplinas[$i]->getId();
		try {
			$topicos = $sessionFacade->recuperarTopicoTodosDAO($id_disc);
		}catch(Exception $e) {
			array_push($msg_erro,$e->getMessage());
		}
		$model->assign_block_vars('topicos',array( 'I' => $id_disc ));
		for ($j=0; $j<count($topicos); $j++){
			if (in_array($topicos[$j]->getId(),$lista_topicos)>0) {
				$selecionado = " checked ";
			}else{
				$selecionado = " ";
			}
			$model->assign_block_vars('topicos.item', array('TOPICO'	=>	$topicos[$j]->getId(),
															'DESCRICAO'	=>	$topicos[$j]->getDescricao(),
															'CHECKED'	=>	$selecionado
														));
			$count++;
		}
	}
	#$model->assign_vars(array( 'QTDE_TOPICOS' => $count));
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>