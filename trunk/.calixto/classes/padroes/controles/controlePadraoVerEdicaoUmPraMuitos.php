<?php
/**
* Classe de definição da camada de controle
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoVerEdicaoUmPraMuitos extends controlePadrao{
	public $colecao;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		$this->registrarInternacionalizacao();
		$this->gerarMenus();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'gravarUmPraMuitos'));
		$this->visualizacao->chave = VComponente::montar('oculto',$this->negocio->nomeChave(),$this->negocio->valorChave());
		$this->montarApresentacao($this->negocio);
		parent::inicial();
	}
	/**
	* Método criado para definir o objeto de negócio a ser apresentado 
	*/
	public function definirNegocio(){
		$this->negocio = $this->pegarNegocio();
		switch(true){
			case isset($_GET['chave']):
				$this->sessao->registrar('negocio',$this->negocio->ler($_GET['chave']));
			break;
			case $this->sessao->tem('negocio'):
				$this->negocio = $this->sessao->pegar('negocio');
			break;
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	* @param [string] tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		$arControle = explode('_',get_class($this));
		preg_match('/(verEdicao)(.*)/', $arControle[1], $resultado);
		$negocioColecao = (definicaoEntidade::negocio(' '.$resultado[2]));
		parent::montarApresentacao($negocio, $tipo);
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoGravar')]  = 'javascript:document.formulario.submit();';
		switch(true){
			case(isset($_GET['chave'])):
				$linkExcluir = "?c=%s&amp;chave=%s";
				$menu[$this->inter->pegarTexto('botaoExcluir')] = sprintf($linkExcluir,definicaoEntidade::controle($this,'excluir'),$_GET['chave']);
			break;
			case($this->negocio->valorChave()):
				$linkExcluir = "?c=%s&amp;chave=%s";
				$menu[$this->inter->pegarTexto('botaoExcluir')] = sprintf($linkExcluir,definicaoEntidade::controle($this,'excluir'),$this->negocio->valorChave());
			break;
		}
		$menu[$this->inter->pegarTexto('botaoListagem')]= sprintf($link,definicaoEntidade::controle($this,'verPesquisa'));
		return $menu;
	}
}
?>
