<?php

/**
 * Classe de representação de uma camada de negócio da entidade [Usuário]
 * A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
 * @package Sistema
 * @subpackage Usuário
 */
class NUsuario extends negocioPadrao {

	const ativo = 1;
	const inativo = 2;
	const naoConfirmado = 3;

	/**
	 * @gerador variavelPadrao
	 * @var integer Código
	 */
	public $idUsuario;

	/**
	 * @gerador variavelPadrao
	 * @var string Login
	 */
	public $nmLogin;

	/**
	 * @gerador variavelPadrao
	 * @var string Senha
	 */
	protected $nmSenha;

	/**
	 * @gerador variavelPadrao
	 * @var string Senha Gerada
	 */
	protected $senhaGerada;

	/**
	 * @gerador variavelPadrao
	 * @var string Status do usuário
	 */
	public $status;

	/**
	 * @gerador variavelPadrao
	 * @var string Status do usuário
	 */
	public $dataSolicitacao;
	/**
	 * @gerador variavelPadrao
	 * @var string Status do usuário
	 */
	public $dataConfirmacao;

	/**
	 * Coleção de perfis do usuário
	 * @var colecaoPadraoNegocio
	 */
	public $coPerfis;
	
	/**
	 * Array de acessos liberados para o usuário
	 * @var array
	 */
	protected static $acessosLiberados = array();

	/**
	 * Retorna o nome da propriedade que contém o valor chave de negócio
	 * @gerador metodoPadrao
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
		//$this->nPessoa = new NPessoa();
	}

	/**
	 * Carrega a pessoa referente ao usuário
	 */
	public function carregarPessoa() {
		if ($this->idPessoa) {
			$this->nPessoa->ler($this->idPessoa);
		}
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
	 * Retorna os acessos liberados para o usuário
	 * @return array
	 */
	public function pegarAcessosLiberados(){
		if (!count(self::$acessosLiberados)) {
			$nAcesso = new NAcesso();
			self::$acessosLiberados = array_flip($nAcesso->lerAcessosPorUsuario($this)->gerarVetorDeAtributo('nmAcesso'));
		}
		return self::$acessosLiberados;
	}

	/**
	 * Retorna se o usuário possui acesso a um controle
	 * @param string $controle
	 * @return boolean
	 */
	public function possuiAcesso($controle) {
		$acessos = $this->pegarAcessosLiberados();
		return isset($acessos[$controle]);
	}


	/**
	 * Gera uma nova senha para alteração de senha do usuário
	 */
	public function gerarNovaSenha($linkRecuperarSenha) {
		$this->ler($this->valorChave());
		//$this->carregarPessoa();
		$this->passarSenhaGerada(md5($novaSenha = caracteres::encrypt(rand(1, 1000000))));
		$this->gravar();
		$email = new emailSistema();
		$email->addEmailDestinatario($this->pegarNmLogin(), $this->pegarNmLogin());
		$email->passarAssunto($this->inter->pegarMensagem('mensagemAssuntoTrocaSenhaEmail'));
		$email->passarConteudo(sprintf($this->inter->pegarMensagem('mensagemTrocaSenhaEmail'), $novaSenha, $linkRecuperarSenha));
		$email->enviar();
	}

	/**
	 * Valida a senha do usuário
	 * @param string $senha
	 * @return boolean 
	 */
	public function validarSenha($senha) {
		if ($this->pegarSenhaGerada() && ($this->pegarSenhaGerada() == md5($senha)))
			return true;
		$nUsuario = new NUsuario($this->pegarConexao());
		$nUsuario->ler($this->valorChave());
		return md5($senha) == $nUsuario->pegarNmSenha();
	}

	/**
	 * Altera a senha atual do usuário
	 * @param string $novaSenha
	 */
	public function trocarSenha($novaSenha) {
		$this->ler($this->valorChave());
		$this->passarNmSenha(md5($novaSenha));
		$this->passarSenhaGerada('');
		$this->gravar();
	}
	
}

?>