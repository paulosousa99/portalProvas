<?

class Topico {

	private $topico					= "";
	private $disciplina				= "";
	private $descricao				= "";

	public $Xtopico					= "";
	public $Xdisciplina				= "";
	public $Xdescricao				= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Topico(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->topico;
	}

	function setId($topico){
		$this->topico = $topico;
	}

	function getDisciplina(){
		return $this->disciplina;
	}

	function setDisciplina($disciplina){
		$this->disciplina = $disciplina;
	}
	
	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}
}
?>