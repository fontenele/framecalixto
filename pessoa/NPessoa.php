<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade 
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage pessoa
*/
class NPessoa extends negocioPadrao{
	/**
	* @var [numerico] Id Pessoa
	*/
	public $idPessoa;
	/**
	* @var [texto] Nm Pessoa
	*/
	public $nmPessoa;
	/**
	* @var [texto] Endere�o
	*/
	public $endereco;
	/**
	* @var [texto] Telefone
	*/
	public $telefone;
	/**
	* @var [texto] Cs Pessoa
	*/
	public $csPessoa;
	/**
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return 'idPessoa'; }
	/**
	* Retorna uma cole��o com os colaboradores do sistema
	* @return [colecao]
	*/
	function lerColaboradores(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('FI');
		return $nPessoa->pesquisar(new pagina());
	}
	/**
	* Retorna uma cole��o com os colaboradores do sistema
	* @return [colecao]
	*/
	function lerEmpresasInternas(){
		$nPessoa = new NPessoa();
		$nPessoa->passarCsPessoa('JI');
		return $nPessoa->pesquisar();
	}
}
?>
