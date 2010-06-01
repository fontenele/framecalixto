<?php
/**
* Classe de representação de uma camada de negócio da entidade Perfil
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Perfil
*/
class NPerfil extends negocioPadrao{
	/**
	* @var integer Identificador
	*/
	public $idPerfil;
	/**
	* @var string Nome do Perfil
	*/
	public $nmPerfil;
	/**
	* @var boolean Registrar Logs de Acesso
	*/
	public $boLogAcesso;
	/**
	* @var colecao Acessos do Perfil
	*/
	public $coAcessos;
	/**
	* @var colecao Usuários do Perfil
	*/
	public $coUsuarios;
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		parent::__construct($conexao);
		$this->coAcessos = new colecaoPadraoNegocio(null,$conexao);
		$this->coUsuarios = new colecaoPadraoNegocio(null,$conexao);
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	function nomeChave(){ return 'idPerfil'; }
	/**
	* Carrega a coleção de usuários do perfil
	*/
	public function carregarUsuarios(){
		$nUsuario = new NUsuarioPerfil($this->conexao);
		$nUsuario->passarIdPerfil($this->pegarIdPerfil());
		$this->coUsuarios = $nUsuario->pesquisar(new pagina(0));
	}
	/**
	* Carrega a coleção de acessos do perfil
	*/
	public function carregarAcessos(){
		$nAcesso = new NAcesso($this->conexao);
		$nAcesso->passarIdPerfil($this->pegarIdPerfil());
		$this->coAcessos = $nAcesso->pesquisar(new pagina(0));
	}
}
?>