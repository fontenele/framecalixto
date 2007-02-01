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
	* @var [texto] Nm Pessoa
	*/
	public $nmPessoa;
	/**
	* @var [texto] Endereço
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
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'idPessoa'; }
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
