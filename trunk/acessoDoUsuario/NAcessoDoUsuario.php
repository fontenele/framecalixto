<?php
/**
* Classe de representaзгo de uma camada de negуcio da entidade 
* A camada de negуcio й a parte que engloba as regras e efetua os comandos de execuзгo de um sistema
* @package Sistema
* @subpackage acessoDoUsuario
*/
class NAcessoDoUsuario extends negocioPadrao{
	/**
	* @var [array] array com a estrutura do mapeamento  entre persistente e negуcio
	* criado para a execuзгo de cache
	*/
	private static $estrutura;
	/**
	* @var [numerico] id acesso usuario
	*/
	public $idAcessoUsuario;
	/**
	* @var [numerico] id usuario
	*/
	public $idUsuario;
	/**
	* @var [numerico] id funcionalidade
	*/
	public $idFuncionalidade;
		/**
	* Retorna o nome da propriedade que contйm o valor chave de negуcio
	* @return [string] 
	*/
	function nomeChave(){ return 'idAcessoUsuario'; }
	/**
	* Mйtodo que retorna o array com o mapeamento entre persistente e negуcio
	* sobrescrito para a execuзгo de cache
	* @return [vetor] de mapeamento  entre persistente e negуcio
	*/
	public function pegarMapeamento(){
		if(!is_array(NAcessoDoUsuario::$estrutura)){
			return NAcessoDoUsuario::$estrutura = $this->mapearNegocio(definicaoArquivo::pegarXmlEntidade($this));
		}else{
			return NAcessoDoUsuario::$estrutura;
		}
	}
}
?>