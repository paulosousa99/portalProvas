<?

class TipoPergunta {

	private $tipo_pergunta			= "";
	private $descricao				= "";
	private $qtde_respostas			= "";
	private $imagem					= "";

	public $Xtipo_pergunta			= "";
	public $Xdescricao				= "";
	public $Xqtde_respostas			= "";
	private $Ximagem				= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function TipoPergunta(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->tipo_pergunta;
	}

	function setId($tipo_pergunta){
		$this->tipo_pergunta = $tipo_pergunta;
	}

	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}

	function getQtdeRespostas(){
		return $this->qtde_respostas;
	}

	function setQtdeRespostas($qtde_respostas){
		$this->qtde_respostas = $qtde_respostas;
	}
	
	function getImagem(){
		return $this->imagem;
	}

	function setImagem($imagem){
		$this->imagem = $imagem;
	}

}
?>