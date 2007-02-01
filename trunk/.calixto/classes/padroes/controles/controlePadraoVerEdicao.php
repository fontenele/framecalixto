<?php
/**
* Classe de definição da camada de controle
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoVerEdicao extends controlePadrao{
	public $negocio;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		$this->registrarInternacionalizacao();
		$this->gerarMenus();
		$this->visualizacaoPadrao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'gravar'));
		$this->visualizacaoPadrao->chave = VComponente::montar('oculto',$this->negocio->nomeChave(),$this->negocio->valorChave());
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
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->internacionalizacao->pegarTexto('botaoGravar')]  = 'javascript:document.formulario.submit();';
		switch(true){
			case(isset($_GET['chave'])):
				$linkExcluir = "?c=%s&amp;chave=%s";
				$menu[$this->internacionalizacao->pegarTexto('botaoExcluir')] = sprintf($linkExcluir,definicaoEntidade::controle($this,'excluir'),$_GET['chave']);
			break;
			case($this->negocio->valorChave()):
				$linkExcluir = "?c=%s&amp;chave=%s";
				$menu[$this->internacionalizacao->pegarTexto('botaoExcluir')] = sprintf($linkExcluir,definicaoEntidade::controle($this,'excluir'),$this->negocio->valorChave());
			break;
		}
		$menu[$this->internacionalizacao->pegarTexto('botaoListagem')]= sprintf($link,definicaoEntidade::controle($this,'verPesquisa'));
		return $menu;
	}
}
?>
