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
	public $nrDocumento;
	/**
	* @var string Código de endeçamento postal
	*/
	public $nrCep;
	/**
	* @var string Telefone
	*/
	public $nrTelefone;
	/**
	* @var string Telefone
	*/
	public $nrTelefone2;
	/**
	* @var string Telefone
	*/
	public $nrTelefone3;
	/**
	* @var integer Estado
	*/
	public $idEstado;
	/**
	* @var string Município
	*/
	public $nmMunicipio;
	/**
	* @var string Bairro
	*/
	public $nmBairro;
	/**
	* @var string Endereço
	*/
	public $txEndereco;
	/**
	* @var string Email
	*/
	public $nmEmail;
	/**
	* @var string Site
	*/
	public $nmSite;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	public function nomeChave(){ return 'idPessoa'; }
	/**
	* Executa o comando de importação do objeto
	*/
	public function importar(){
		$nEstado = new NEstado($this->conexao);
		$nEstado->passarSgSigla(operador::igual($this->pegarIdEstado()));
		$resultado = $nEstado->pesquisar(new pagina());
		$this->passarIdEstado((!$resultado) ? null : $resultado->avancar()->valorChave());
		parent::importar();
	}
	/**
	* Método que retorna o número do documento da pessoa
	* @return TDocumentoPessoal
	*/
	public function pegarDocumento(){
		if($this->nrDocumento instanceof TDocumentoPessoal){
			$this->nrDocumento->passarTipo(($this->csPessoa{0} == 'F') ? 'cpf' : 'cnpj');
		}
		return $this->nrDocumento;
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return colecaoPadraoNegocio
	*/
	function lerColaboradores(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa(operador::igual('FI'));
		return $nPessoa->pesquisar(new pagina());
	}
	/**
	* Retorna uma coleção com os colaboradores do sistema
	* @return colecaoPadraoNegocio
	*/
	function lerEmpresasInternas(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa(operador::igual('JI'));
		return $nPessoa->pesquisar();
	}
}
?>