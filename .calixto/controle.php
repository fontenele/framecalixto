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
	* @var [string] define o próximo controle para ser redirecionado
	*/
	public $gerente;
	/**
	* @var [gerenteSessao]
	*/
	public $sessao;
	/**
	* @var [string] classe de visualização padrão do controle
	*/
	public $visualizacao;
	/**
	* Método construtor
	* Faz a chamada de validação de acesso ao controle
	*/
	final function __construct($gerente = null,$session = false){
		try{
			if($session) sessaoSistema::iniciar();
			$this->gerente = $gerente;
			$this->sessao = new sessaoPrograma(definicaoEntidade::entidade($this));
			$this->validarAcessoAoControle();
			$this->criarVisualizacaoPadrao();
			$this->criarInternacionalizacaoPadrao();
			$this->inicial();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de validação do controle de acesso
	* @return [booleano] resultado da validação
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
	* @param [string] nome do proximo controle
	*/
	public function passarProximoControle($proximoControle){
		if(!$this->requisicaoAjax())
		$this->gerente->proximoControle = $proximoControle;
	}
	/**
	* executa na sessão do sistema o registro da comunicacao
	* @param [string] mensagem de comunicacao
	*/
	public function registrarComunicacao($comunicacao){
		sessaoSistema::registrar('comunicacao', $comunicacao);
	}
	/**
	* retorna se a requisição do controle foi feita via ajax
	*/
	public static function requisicaoAjax(){
        if(controle::tipoResposta() == controle::ajax) return true;
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