<?php
/**
* Classe de representação de uma camada de negócio da entidade [Usuário]
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Usuário
*/
class NUsuario extends negocioPadrao{
	/**
	* @var integer Identificador
	*/
	public $idUsuario;
	/**
	* @var integer Pessoa
	*/
	public $idPessoa;
	/**
	* @var string Login
	*/
	public $nmLogin;
	/**
	* @var string Senha
	*/
	protected $nmSenha;
	/**
	 * Coleção de perfis do usuário
	 * @var colecaoPadraoNegocio
	 */
	public $coPerfis;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	public function nomeChave(){ return 'idUsuario'; }
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		parent::__construct($conexao);
		$this->coAcessos = new colecaoPadraoNegocio(null,$conexao);
		$this->coPerfis = new colecaoPadraoNegocio(null,$conexao);
	}
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