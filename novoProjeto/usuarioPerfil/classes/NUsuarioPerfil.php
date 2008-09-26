<?php
/**
* Classe de representação de uma camada de negócio da entidade [Usuário Perfil]
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Usuário Perfil
*/
class NUsuarioPerfil extends negocioPadrao{
	/**
	* @var [numerico] Identificador
	*/
	public $idUsuarioPerfil;
	/**
	* @var [numerico] Usuário
	*/
	public $idUsuario;
	/**
	* @var [numerico] Perfil
	*/
	public $idPerfil;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'idUsuarioPerfil'; }
}
?>