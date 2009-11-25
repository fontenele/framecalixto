<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage pessoa
*/
class NPessoa extends negocioPadrao{
	/**
	* @var integer Id Pessoa
	*/
	public $idPessoa;
	/**
	* @var string Cs Pessoa
	*/
	public $csPessoa;
	/**
	* @var string Nm Pessoa
	*/
	public $nmPessoa;
	/**
	* @var string Documento
	*/
	public $documento;
	/**
	* @var string Código de endeçamento postal
	*/
	public $cep;
	/**
	* @var string Telefone
	*/
	public $telefone;
	/**
	* @var string Telefone
	*/
	public $telefone2;
	/**
	* @var string Telefone
	*/
	public $telefone3;
	/**
	* @var integer Estado
	*/
	public $estado;
	/**
	* @var string Município
	*/
	public $municipio;
	/**
	* @var string Bairro
	*/
	public $bairro;
	/**
	* @var string Endereço
	*/
	public $endereco;
	/**
	* @var string Email
	*/
	public $email;
	/**
	* @var string Site
	*/
	public $site;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	function nomeChave(){ return 'idPessoa'; }
	/**
	* Executa o comando de importação do objeto
	*/
	public function importar(){
		$estado = new NEstado($this->conexao);
		$estado->passarSigla($this->pegarEstado());
		$resultado = $estado->pesquisar(new pagina());
		$this->passarEstado((!$resultado) ? null : $resultado->avancar()->pegarId());
		parent::importar();
	}
	/**
	* Método que retorna o número do documento da pessoa
	* @return TDocumentoPessoal
	*/
	public function pegarDocumento(){
		if($this->documento instanceof TDocumentoPessoal){
			$this->documento->passarTipo(($this->csPessoa{0} == 'F') ? 'cpf' : 'cnpj');
		}
		return $this->documento;
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return colecaoPadraoNegocio
	*/
	function lerColaboradores(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('FI');
		return $nPessoa->pesquisar(new pagina());
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return colecaoPadraoNegocio
	*/
	function lerEmpresasInternas(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('JI');
		return $nPessoa->pesquisar();
	}
}
?>