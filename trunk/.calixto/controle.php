<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
abstract class controle extends objeto{
    const ajax = 'ajax';
    const xml = 'xml';
    const json = 'json';
    const texto = 'texto';
	/**
	 * @var string armazana a chamada url atual 
	 */
	private static $urlAtual;
	/**
	 * @var string armazena o nome do controle construido
	 */
	private static $nomeControle;
	/**
	* @var gerenteControles
	*/
	public $gerente;
	/**
	* @var sessaoPrograma
	*/
	public $sessao;
	/**
	* @var visualizacao classe de visualização padrão do controle
	*/
	public $visualizacao;
	/**
	* Método construtor
	* Faz a chamada de validação de acesso ao controle
	*/
	final function __construct($gerente = null, $session = false) {
		try {
			controle::$nomeControle = get_class($this);
			if ($session)
				sessaoSistema::iniciar();
			$this->gerente = $gerente;
			$this->sessao = new sessaoPrograma(definicaoEntidade::entidade($this));
			$this->registrarAcesso();
			//$this->gravarLogAcesso();
			$this->validarAcessoAoControle();
			$this->criarVisualizacaoPadrao();
			$this->criarInternacionalizacaoPadrao();
			$this->inicial();
		} catch (erro $e) {
			throw $e;
		}
	}
	/**
	 * Registra o acesso requerido na sessao do sistema
	 */
	public function registrarAcesso(){
		sessaoSistema::registrar('ultimoAcesso', $_SERVER['REQUEST_URI']);
	}

	/**
	 * Retorna o nome do último controle construído
	 * @return string
	 */
	public static function urlAcessada(){
		return controle::$urlAtual;
	}
	/**
	 * Retorna o nome do último controle construído
	 * @return string
	 */
	public static function controleAcessado(){
		return controle::$nomeControle;
	}
	/**
	* Método de validação do controle de acesso
	* @return boolean resultado da validação
	*/
	public function validarAcessoAoControle(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeAcesso = $definicoes->xpath('//controleDeAcesso');
			if(isset($controleDeAcesso[0])){
				if(strval($controleDeAcesso[0]['liberado']) == 'sim') return true;
				$classe = strval($controleDeAcesso[0]['classe']);
				$metodo = strval($controleDeAcesso[0]['metodoLiberacao']);
				if($classe && $metodo){
					$classe = new $classe();
					$classe->$metodo(get_class($this));
				}
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que registra o log de acesso
	*/
	final public function gravarLogAcesso(){
		if(sessaoSistema::tem('usuario')){
			$boLogAcesso = false;
			
			$nUsuario = sessaoSistema::pegar('usuario');
			$nUsuario->carregarPerfis();
			if($nUsuario->coPerfis->possuiItens()){
				while($nPerfilUsuario = $nUsuario->coPerfis->avancar()){
					$nPerfil = new NPerfil();
					$nPerfil->ler($nPerfilUsuario->pegarIdPerfil());
					if($nPerfil->pegarBoLogAcesso()) { $boLogAcesso = true; }
				}
			}
			
			if($boLogAcesso){
				$nLogAcesso = new NLogAcesso();
				$nLogAcesso->passarIdUsuario(sessaoSistema::pegar('usuario')->valorChave());
				$nLogAcesso->passarDtAcesso(TDataHora::agora());
				$nLogAcesso->txIP = $_SERVER["REMOTE_ADDR"];
				$nLogAcesso->txUrl = $_SERVER['QUERY_STRING'];
				$nLogAcesso->gravar();
			}
		}
	}
	/**
	* Método de criação da visualizacao
	*/
	public function criarVisualizacaoPadrao(){
		$this->visualizacao = new visualizacaoPadrao($this);
	}
	/**
	* Método de criação da visualizacao
	*/
	public function criarInternacionalizacaoPadrao(){
		try{
			$classe = definicaoEntidade::internacionalizacao($this);
			$this->inter = new $classe();
		}
		catch(erro $e){
			$this->inter = new internacionalizacaoPadrao($this);
		}
	}
	/**
	* Método inicial a ser acessado em qualquer controle
	*/
	public abstract function inicial();
	/**
	* Método de passagem do próximo controle para redirecionamento
	* @param string nome do proximo controle
	*/
	public function passarProximoControle($proximoControle){
		if(!$this->requisicaoAjax())
		$this->gerente->proximoControle = $proximoControle;
	}
	/**
	 * executa na sessão do sistema o registro da comunicacao
	 * @param string $comunicacao mensagem de comunicacao
	 * @param string $tipo (erro, sucesso ou alerta)
	 */
	public function registrarComunicacao($comunicacao, $tipo = 'alerta'){
		controle::comunicar($comunicacao,$tipo);
	}
	/**
	 * executa na sessão do sistema o registro da comunicacao
	 * @param string $comunicacao mensagem de comunicacao
	 * @param string $tipo (erro, sucesso ou alerta)
	 */
	public static function comunicar($comunicacao, $tipo){
		switch($tipo){
			case 'erro':
				sessaoSistema::registrar('comunicacaoErro', $comunicacao);
			break;
			case 'sucesso':
				sessaoSistema::registrar('comunicacaoSucesso', $comunicacao);
			break;
			default:
				sessaoSistema::registrar('comunicacaoAlerta', $comunicacao);
		}
	}
	/**
	* retorna se a requisição do controle foi feita via ajax
	*/
	public static function requisicaoAjax(){
        if(controle::tipoResposta() == controle::ajax) return true;
        if(controle::tipoResposta() == controle::json) return true;
		return (isset($_GET['ajax']));
	}
    /**
    * Retorna o tipo de requisicao feita
    */
    public static function tipoResposta(){
        if (isset($_GET['tipoResposta'])){
            return strtolower($_GET['tipoResposta']);
        }
        return false;
    }
	public static function responderJson($json){
		header("Content-type:application/jsonrequest; charset=utf-8");
		echo $json;
		die;
	}
	public static function responderXml($xml){
		header("Content-type:application/xml; charset=utf-8");
		echo $xml;
		die;
	}
}
?>
