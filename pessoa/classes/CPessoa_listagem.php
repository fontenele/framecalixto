<?php
/**
* Classe de controle
* Executa a listagem de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Tarefa', 'novaTarefa', '20%', $alinhamento, 30);
	}
	/**
	* Retorno da apresentação do tipo de pessoa .
	*/
	public function novaTarefa($nPessoa){
		$link = sprintf('?c=%s&amp;pessoa=%s','CTarefa_criarNova',$nPessoa->pegarIdPessoa());
		return "<a href='{$link}'>Nova</a>";
	}
}
?>