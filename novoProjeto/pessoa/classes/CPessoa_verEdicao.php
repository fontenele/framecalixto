<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_verEdicao extends controlePadraoVerEdicao{
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu[$this->inter->pegarTexto('novaTarefa')]= sprintf('?c=%s&amp;pessoa=%s','CTarefa_criarNova',$this->negocio->pegarIdPessoa());
		return $menu;
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para edição
	*/
	public static function montarApresentacaoEdicao(negocio $negocio, visualizacao $visualizacao){
		parent::montarApresentacaoEdicao($negocio,$visualizacao);
		$visualizacao->csPessoa->adicionarOnChange("ajustarDocumento();");
		$visualizacao->documento->removerOnBlur();
		$visualizacao->documento->adicionarOnBlur("formatarDocumento();");
	}
}
?>