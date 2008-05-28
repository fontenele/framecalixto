<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_verImportador extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->registrarInternacionalizacao();
		$this->gerarMenus();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'importadorXML'));
		$this->visualizacao->xml = VComponente::montar('textArea','xml',$coletor->sql());
		$this->visualizacao->xml->passarRows(20);
		$this->visualizacao->xml->passarCols(70);
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoGravar')]  = 'javascript:document.formulario.submit();';
		return $menu;
	}
}
?>