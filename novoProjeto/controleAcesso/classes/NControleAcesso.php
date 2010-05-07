<?php
/**
* Classe de representação de uma camada de negócio da entidade Usuario
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Pessoa
*/
class NControleAcesso extends negocio{
	/**
	* @var string login de acesso ao sistema
	*/
	public $nmLogin;
	/**
	* @var string senha de acesso ao sistema
	*/
	public $nmSenha;
	/**
	* Método criado para efetuar a validação de acesso a um controle do sistema
	* @param string nome do controle acessado
	*/
	public static function validarAcesso($controleAcessado){
		try{
			$definicoes = definicao::pegarDefinicao();
			if(strval($definicoes->controleDeAcesso['liberado']) == 'sim') return true;
			switch(true){
				case(!sessaoSistema::tem('usuario')):
					throw(new erroAcesso('Acesso não permitido, usuário não registrado!'));
				default:
					$nUsuario = sessaoSistema::pegar('usuario');
					$nUsuario->carregarPerfis();
					$nAcesso = new NAcesso();
					$colecao = $nAcesso->lerAcessosPorUsuario($nUsuario,$controleAcessado);
					if(!$colecao->contarItens()){
						throw(new erroAcesso('Acesso Não Permitido!'));
					}
			}
			return true;
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
				case(!$this->pegarNmLogin()):
					throw(new erroLogin('Login não informado!'));
				case(!$this->pegarNmSenha()):
					throw(new erroLogin('Senha não informada!'));
				default:
					$nUsuario = new NUsuario();
					$nUsuario->passarNmLogin(operador::igual($this->pegarNmLogin()));
					$nUsuario->passarNmSenha(operador::igual($this->pegarNmSenha()));
					$colecao = $nUsuario->pesquisar(new pagina());
					if(!$colecao->possuiItens()) throw(new erroAcesso('Usuário não autorizado!'));
					sessaoSistema::registrar('usuario',$colecao->avancar());
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>