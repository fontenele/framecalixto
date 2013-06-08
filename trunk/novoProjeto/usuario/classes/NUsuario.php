<?php

/**
 * Classe de representação de uma camada de negócio da entidade [Usuário]
 * A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
 * @package Sistema
 * @subpackage Usuário
 */
class NUsuario extends negocioPadrao {

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
	public function nomeChave() {
		return 'idUsuario';
	}

	/**
	 * Metodo construtor
	 * @param conexao (opcional) conexão com o banco de dados
	 */
	public function __construct($conexao = null) {
		parent::__construct($conexao);
		$this->coAcessos = new colecaoPadraoNegocio(null, $conexao);
		$this->coPerfis = new colecaoPadraoNegocio(null, $conexao);
	}

	/**
	 * Carrega a coleção de perfis
	 */
	public function carregarPerfis() {
		$nUsuarioPerfil = new NUsuarioPerfil($this->conexao);
		$nUsuarioPerfil->passarIdUsuario($this->pegarIdUsuario());
		$this->coPerfis = $nUsuarioPerfil->pesquisar(new pagina(0));
	}

	/**
	 * Carrega a coleção de acessos do usuario
	 */
	public function carregarAcessos() {
		$nAcesso = new NAcesso($this->conexao);
		$nAcesso->passarIdUsuario($this->pegarIdUsuario());
		$this->coAcessos = $nAcesso->pesquisar(new pagina(0));
	}

	/**
	 * Altera a senha do usuário
	 * @param string $senhaAtual
	 * @param string $novaSenha
	 * @param string $confirmacao
	 * @throws erroNegocio
	 */
	public function alterarSenha($senhaAtual, $novaSenha, $confirmacao) {
		if (strlen($novaSenha) < 6)
			throw(new erroNegocio('A senha deve possuir no mínimo 6 caracteres!'));
		if ($novaSenha != $confirmacao)
			throw(new erroNegocio('A confirmação da senha não confere!'));
		if (!$this->validar($this->pegarNmLogin(), $senhaAtual))
			throw(new erroNegocio('Usuário não autorizado!'));
		$nUsuario = new NUsuario();
		$nUsuario->ler($this->valorChave());
		if (!$nUsuario->valorChave())
			throw new erroNegocio('Usuário não definido para alterar senha!');
		$nUsuario->passarNmSenha(self::encriptarSenha($novaSenha));
		$nUsuario->gravar();
	}

	/**
	 * Faz a validação do login e senha do usuário
	 * @param string $login
	 * @param string $senha
	 * @throws erroNegocio
	 */
	public function validar($login, $senha) {
		$nUsuario = new NUsuario();
		$nUsuario->passarNmLogin(operador::igual($login));
		$nUsuario->passarNmSenha(operador::igual(self::encriptarSenha($senha)));
		$colecao = $nUsuario->pesquisar(new pagina());
		if (!$colecao->possuiItens())
			return false;
		return $colecao->avancar();
	}

	/**
	 * Executa a encriptação da senha do usuário
	 * @param string $senha
	 * @return string
	 */
	protected static function encriptarSenha($senha) {
		return md5($senha);
	}

	/**
	 * Realiza a encriptação da senha deste usuário
	 */
	public function encriptarNmSenha() {
		$this->passarNmSenha(self::encriptarSenha($this->pegarNmSenha()));
	}


	/**
	 * Método de apresentação simplificada do objeto de negócio
	 * @return string descrição do objeto
	 */
	public function valorDescricao() {
		try {
			return parent::valorDescricao();
		} catch (Exception $e) {
			return 'Visitante';
		}
	}

	/**
	 * Retorna se o usuário possui acesso a um controle
	 * @param string $controle
	 * @return boolean
	 */
	public function possuiAcesso($controle) {
		if (!count(self::$acessosLiberados)) {
			$nAcesso = new NAcesso();
			self::$acessosLiberados = array_flip($nAcesso->lerAcessosPorUsuario($this)->gerarVetorDeAtributo('nmAcesso'));
		}
		return isset(self::$acessosLiberados[$controle]);
	}

}

?>