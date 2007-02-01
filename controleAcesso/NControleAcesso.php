<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade Usuario
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage Pessoa
*/
class NControleAcesso extends negocio{
	/**
	* @var [string] login de acesso ao sistema
	*/
	public $login;
	/**
	* @var [string] senha de acesso ao sistema
	*/
	public $senha;
	/**
	* M�todo criado para efetuar a valida��o de acesso a um controle do sistema
	* @param [string] nome do controle acessado
	*/
	public function validarAcesso($controleAcessado){
		try{
			switch(true){
				case(!sessaoSistema::tem('usuario')):
					throw(new erroAcesso('Acesso n�o permitido, usu�rio n�o registrado !'));
				default:
					// throw(new erroAcesso('Acesso N�o Permitido !'));
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* M�todo criado para efetuar a valida��o de login no sistema
	*/
	public function validarLogin(){
		try{
			switch(true){
				case(!$this->pegarLogin()):
					throw(new erroLogin('Login n�o informado !'));
				case(!$this->pegarSenha()):
					throw(new erroLogin('Senha n�o informada !'));
				default:
					$nUsuario = new NUsuario();
					$nUsuario->passarNmUsuario($this->pegarLogin());
					$nUsuario->passarSenha($this->pegarSenha());
					$colecao = $nUsuario->pesquisar(new pagina());
					if(!$colecao->possuiItens()) throw(new erroLogin('Usu�rio n�o autorizado !'));
					sessaoSistema::registrar('usuario',$colecao->avancar());
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
