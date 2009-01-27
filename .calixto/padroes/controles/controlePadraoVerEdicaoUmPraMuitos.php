<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoVerEdicaoUmPraMuitos extends controlePadrao{
	public $colecao;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
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
	* Preenche os itens da propriedade menuPrograma
	* @return colecaoPadraoMenu do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$gravar = $this->inter->pegarTexto('botaoGravar');
		$excluir = $this->inter->pegarTexto('botaoExcluir');
		$listagem = $this->inter->pegarTexto('botaoListagem');
		$chave = isset($_GET['chave']) ? $_GET['chave'] : ($this->negocio->valorChave()) ? $this->negocio->valorChave() : null;
		$menu->$gravar = new VMenu($gravar,'javascript:document.formulario.submit();','.sistema/imagens/botao_gravar.png');
		if($chave) $menu->$excluir = new VMenu($excluir,sprintf("?c=%s&amp;chave=%s",definicaoEntidade::controle($this,'excluir'),$chave),'.sistema/imagens/botao_excluir.png');
		$menu->$listagem = new VMenu($listagem,sprintf("?c=%s",definicaoEntidade::controle($this,'verPesquisa')),'.sistema/imagens/botao_listagem.png');
		return $menu;
	}
}
?>
