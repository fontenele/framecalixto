<?php
/**
* Classe de representação de uma camada de negócio da entidade [Usuário]
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Usuário
*/
class NUsuario extends negocioPadrao{
	/**
	* @var integer Pessoa
	*/
	public $idPessoa;
	/**
	* @var integer Identificador
	*/
	public $idUsuario;
	/**
	* @var string Login
	*/
	public $login;
	/**
	* @var string Senha
	*/
	protected $senha;
	/**
	 * Coleção de perfis do usuário
	 * @var colecaoPadraoNegocio
	 */
	public $coPerfis;
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
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
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
	/**
	* Executa o comando de gravação do objeto
	* @param boolean caso verdadeiro irá incluir com a chave de negócio passada caso falso irá verificar, se foi passada a chave irá alterar senão irá incluir
	*/
	public function gravar($gravarComChavePassada = false){
		try{
			$persistente = $this->pegarPersistente();
			switch(true){
				case $gravarComChavePassada:
					$this->verificarAntesInserir();
					$persistente->inserir($this->negocioPraVetor(), $gravarComChavePassada);
				break;
				case $this->valorChave():
					$negocio = get_class($this);
					$negocio = new $negocio();
					$negocio->ler($this->valorChave());
					$this->verificarAntesAlterar($negocio);
					$persistente->alterar($this->negocioPraVetor(),$this->valorChave());
				break;
				default:
					$this->valorChave($persistente->gerarSequencia());
					$this->verificarAntesInserir();
					$persistente->inserir($this->negocioPraVetor());
				break;
			}
		}
		catch(Erro $e){
			throw $e;
		}
	}
}
?>