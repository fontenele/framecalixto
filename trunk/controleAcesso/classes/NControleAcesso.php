<?php
/**
* Classe de representação de uma camada de negócio da entidade Usuario
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
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
	* Método criado para efetuar a validação de acesso a um controle do sistema
	* @param [string] nome do controle acessado
	*/
	public function validarAcesso($controleAcessado){
		try{
			$conexao = $this->pegarConexao();
			switch(true){
				case(!sessaoSistema::tem('usuario')):
					throw(new erroAcesso('Acesso não permitido, usuário não registrado !'));
				default:
					$nUsuario = sessaoSistema::pegar('usuario');
					$nUsuario->passarConexao($conexao);
					$nAcesso = new NAcessoDoUsuario();
					$nAcesso->passarIdUsuario($nUsuario->pegarIdUsuario());
					$nAcesso->passarControle($controleAcessado);
					$colecao = $nAcesso->pesquisar(new pagina());
					if(!$colecao->contarItens())
						throw(new erroAcesso('Acesso Não Permitido !'));
			}
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método criado para efetuar a validação de login no sistema
	*/
	public function validarLogin(){
		try{
			switch(true){
				case(!$this->pegarLogin()):
					throw(new erroLogin('Login não informado !'));
				case(!$this->pegarSenha()):
					throw(new erroLogin('Senha não informada !'));
				default:
					$nUsuario = new NUsuario();
					$nUsuario->passarNmUsuario($this->pegarLogin());
					$nUsuario->passarSenha($this->pegarSenha());
					$colecao = $nUsuario->pesquisar(new pagina());
					if(!$colecao->possuiItens()) throw(new erroLogin('Usuário não autorizado !'));
					sessaoSistema::registrar('usuario',$colecao->avancar());
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
