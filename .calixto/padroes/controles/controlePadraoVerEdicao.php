<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoVerEdicao extends controlePadrao{
	/**
	* @var [negocio] objeto de negócio a ser editado
	*/
	public $negocio;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'gravar'));
		$this->visualizacao->chave = VComponente::montar('oculto',$this->negocio->nomeChave(),$this->negocio->valorChave());
		$this->montarApresentacao($this->negocio);
		$help = new VEtiquetaHtml('div');
		$help->passarClass('help');
		$help->passarConteudo($this->inter->pegarTexto('ajudaNovo'));
		switch(true){
			case(isset($_GET['chave'])):
			case($this->negocio->valorChave()):
				$help->passarConteudo($this->inter->pegarTexto('ajudaEdicao'));
			break;
		}
		$this->visualizacao->descricaoDeAjuda = $help;
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
	* @return array itens do menu do programa
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
