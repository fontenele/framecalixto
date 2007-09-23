<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage usuario
*/
class NUsuario extends negocioPadrao{
	/**
	* @var [numerico] Id Usuario
	*/
	public $idUsuario;
	/**
	* @var [numerico] Id Pessoa
	*/
	public $idPessoa;
	/**
	* @var [texto] Nm Usuario
	*/
	public $nmUsuario;
	/**
	* @var [texto] senha
	*/
	public $senha;
	/**
	* @var [texto] status
	*/
	public $status;
	/**
	* @var [data] Dt Cadastro
	*/
	public $dtCadastro;
	/**
	* @var [colecao] Acessos do usuário
	*/
	public $coAcessos;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		parent::__construct($conexao);
		$this->coAcessos = new colecaoPadraoNegocio(null,$conexao);
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'idUsuario'; }
	/**
	* Carrega a coleção de acessos do usuário
	*/
	public function carregarAcessos(){
		$nAcesso = new NAcessoDoUsuario($this->conexao);
		$nAcesso->passarIdUsuario($this->pegarIdUsuario());
		$this->coAcessos = $nAcesso->pesquisar(new pagina(0));
	}
}
?>