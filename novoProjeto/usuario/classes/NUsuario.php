<?php
/**
* Classe de representação de uma camada de negócio da entidade [Usuário]
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Usuário
*/
class NUsuario extends negocioPadrao{
	/**
	* @var [numerico] Pessoa
	*/
	public $idPessoa;
	/**
	* @var [numerico] Identificador
	*/
	public $idUsuario;
	/**
	* @var [texto] Login
	*/
	public $login;
	/**
	* @var [texto] Senha
	*/
	public $senha;
	public $coPerfis;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		parent::__construct($conexao);
		$this->coAcessos = new colecaoPadraoNegocio(null,$conexao);
		$this->coPerfis = new colecaoPadraoNegocio(null,$conexao);
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'idUsuario'; }
	/**
	* Carrega a coleção de perfis
	*/
	public function carregarPerfis(){
		$nUsuarioPerfil = new NUsuarioPerfil($this->conexao);
		$nUsuarioPerfil->passarIdUsuario($this->pegarIdUsuario());
		$this->coPerfis = $nUsuarioPerfil->pesquisar(new pagina(0));
	}
	/**
	* Carrega a coleção de acessos do usuario
	*/
	public function carregarAcessos(){
		$nAcesso = new NAcesso($this->conexao);
		$nAcesso->passarIdUsuario($this->pegarIdUsuario());
		$this->coAcessos = $nAcesso->pesquisar(new pagina(0));
	}
}
?>