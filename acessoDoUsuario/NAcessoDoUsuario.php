<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade 
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage acessoDoUsuario
*/
class NAcessoDoUsuario extends negocioPadrao{
	/**
	* @var [array] array com a estrutura do mapeamento  entre persistente e neg�cio
	* criado para a execu��o de cache
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
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return 'idAcessoUsuario'; }
	/**
	* M�todo que retorna o array com o mapeamento entre persistente e neg�cio
	* sobrescrito para a execu��o de cache
	* @return [vetor] de mapeamento  entre persistente e neg�cio
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