<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage acessoDoUsuario
*/
class NAcessoDoUsuario extends negocioPadrao{
	/**
	* @var [numerico] id acesso usuario
	*/
	public $idAcessoUsuario;
	/**
	* @var [numerico] id usuario
	*/
	public $idUsuario;
	/**
	* @var [texto] controle
	*/
	public $controle;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'idAcessoUsuario'; }
}
?>