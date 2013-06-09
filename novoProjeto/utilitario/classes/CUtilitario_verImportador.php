<?php
/**
* Classe de controle
* Visualiza a tela de importação de dados para o sistema
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
		$this->visualizacao->descricaoDeAjuda = $this->inter->pegarTexto('ajudaImportador');
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'importadorXML'));
		$this->visualizacao->xml = VComponente::montar('textArea','xml',null);
		$this->visualizacao->xml->passarRows(15);
		$this->visualizacao->xml->passarCols(70);
		$this->visualizacao->xml->passarTitle('Preencher o campo de texto com xml para a importação');
		$this->visualizacao->xml->passarLimite(1000000);
		$this->visualizacao->xml->adicionarClass('input-xxlarge');
		$this->visualizacao->xml->obrigatorio(true);
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
        $menu = parent::montarMenuPrograma();
		$gravar = $this->inter->pegarTexto('botaoGravar');
		$menu->$gravar = new VMenu($gravar,'javascript:$(this).importar();','icon-pencil icon-white');
        $menu->$gravar->passar_classeLink('btn btn-primary');
		return $menu;
	}
}
?>