<?php
/**
* Classe de defini��o da camada de controle
* @package Infra-estrutura
* @subpackage Controle
*/
abstract class controle extends objeto{
	/**
	* @var [string] define o pr�ximo controle para ser redirecionado
	*/
	public $gerente;
	/**
	* @var [gerenteSessao]
	*/
	public $sessao;
	/**
	* @var [string] define o pr�ximo controle para ser redirecionado
	*/
	public $visualizacaoPadrao;
	/**
	* M�todo construtor
	* Faz a chamada de valida��o de acesso ao controle
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
	* M�todo de valida��o do controle de acesso
	* @return [booleano] resultado da valida��o
	*/
	public function validarAcessoAoControle(){ 
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeAcesso = $definicoes->xpath('//controleDeAcesso');
			$classe = strval($controleDeAcesso[0]['classe']);
			$metodo = strval($controleDeAcesso[0]['metodoLiberacao']);
			if($classe && $metodo){
				$classe = new $classe();
				$classe->$metodo(get_class($this));
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* M�todo de cria��o da visualizacao
	*/
	public function criarVisualizacaoPadrao(){
		$this->visualizacaoPadrao = new visualizacaoPadrao($this);
	}
	/**
	* M�todo de cria��o da visualizacao
	*/
	public function criarInternacionalizacaoPadrao(){
		try{
			$classe = definicaoEntidade::internacionalizacao($this);
			$this->internacionalizacao = new $classe();
		}
		catch(erro $e){
			$this->internacionalizacao = new internacionalizacaoPadrao($this);
		}
	}
	/**
	* M�todo inicial a ser acessado em qualquer controle
	*/
	public abstract function inicial();
	/**
	* M�todo de passagem do pr�ximo controle para redirecionamento
	* @param [string] nome do proximo controle
	*/
	public function passarProximoControle($proximoControle){
		$this->gerente->proximoControle = $proximoControle;
	}
	/**
	* executa na sess�o do sistema o registro da comunicacao
	* @param [string] mensagem de comunicacao
	*/
	public function registrarComunicacao($comunicacao){
		sessaoSistema::registrar('comunicacao', $comunicacao);
	}
}
?>
