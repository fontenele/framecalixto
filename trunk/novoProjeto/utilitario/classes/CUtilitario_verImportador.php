<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_verImportador extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'importadorXML'));
		$this->visualizacao->xml = VComponente::montar('textArea','xml',null);
		$this->visualizacao->xml->passarRows(20);
		$this->visualizacao->xml->passarCols(70);
		$this->visualizacao->xml->passarTitle('Preencher o campo de texto com xml para a importação');
		$this->visualizacao->xml->passarLimite(1000000);
		$this->visualizacao->xml->obrigatorio(true);
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoGravar')]  = 'javascript:$.submeter();';
		return $menu;
	}
}
?>