<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage pessoa
*/
class NPessoa extends negocioPadrao{
	/**
	* @var [numerico] Id Pessoa
	*/
	public $idPessoa;
	/**
	* @var [texto] Cs Pessoa
	*/
	public $csPessoa;
	/**
	* @var [texto] Nm Pessoa
	*/
	public $nmPessoa;
	/**
	* @var [texto] Documento
	*/
	public $documento;
	/**
	* @var [texto] Código de endeçamento postal
	*/
	public $cep;
	/**
	* @var [texto] Telefone
	*/
	public $telefone;
	/**
	* @var [texto] Telefone
	*/
	public $telefone2;
	/**
	* @var [texto] Telefone
	*/
	public $telefone3;
	/**
	* @var [numerico] Estado
	*/
	public $estado;
	/**
	* @var [texto] Município
	*/
	public $municipio;
	/**
	* @var [texto] Bairro
	*/
	public $bairro;
	/**
	* @var [texto] Endereço
	*/
	public $endereco;
	/**
	* @var [texto] Email
	*/
	public $email;
	/**
	* @var [texto] Site
	*/
	public $site;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
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
	* @return [TDocumentoPessoal]
	*/
	public function pegarDocumento(){
		if($this->documento instanceof TDocumentoPessoal){
			$this->documento->passarTipo(($this->csPessoa{0} == 'F') ? 'cpf' : 'cnpj');
		}
		return $this->documento;
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return [colecao]
	*/
	function lerColaboradores(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('FI');
		return $nPessoa->pesquisar(new pagina());
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return [colecao]
	*/
	function lerEmpresasInternas(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('JI');
		return $nPessoa->pesquisar();
	}
}
?>