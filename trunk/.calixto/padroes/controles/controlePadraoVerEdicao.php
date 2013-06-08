<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoVerEdicao extends controlePadrao{
	/**
	* @var negocio objeto de negócio a ser editado
	*/
	public $negocio;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		if(controle::tipoResposta() == controle::xml) controle::responderXml($this->negocio->xml());
		if(controle::tipoResposta() == controle::json) controle::responderJson($this->negocio->json());
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->montarApresentacao($this->negocio);
		parent::inicial();
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param string tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'gravar'));
		$this->visualizacao->chave = VComponente::montar('oculto',$this->negocio->nomeChave(),$this->negocio->valorChave());
		$this->visualizacao->descricaoDeAjuda = $this->inter->pegarTexto('ajudaNovo');
		switch(true){
			case(isset($_GET['chave'])):
			case($this->negocio->valorChave()):
				$this->visualizacao->descricaoDeAjuda = $this->inter->pegarTexto('ajudaEdicao');
			break;
		}
		parent::montarApresentacao($negocio, $tipo);
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para edição
	*/
	public static function montarApresentacaoEdicao(negocio $negocio, visualizacao $visualizacao){
		parent::montarApresentacaoEdicao($negocio, $visualizacao);
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura['campos'] as $nome => $opcoes){
			if(!($visualizacao->$nome instanceof VHidden))
			$visualizacao->$nome->obrigatorio($opcoes['obrigatorio'] == 'sim');
		}
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
		$menu = parent::montarMenuPrograma();
		$this->montarBotaoGravar($menu);
		$this->montarBotaoExcluir($menu);
		$this->montarBotaoListagem($menu);
		return $menu;
	}
	/**
	 * Monta no menu o botão de gravar
	 * @param colecaoPadraoMenu $menu
	 */
	function montarBotaoGravar($menu){
		$gravar = $this->inter->pegarTexto('botaoGravar');
		$icoGravar = $this->inter->pegarTexto('icoBotaoGravar');
		$menu->$gravar = new VMenu($gravar,'javascript:$.submeter();','icon-pencil icon-white');
        $menu->$gravar->passar_classeLink('btn btn-primary');
	}
	/**
	 * Monta no menu o botão de excluir
	 * @param colecaoPadraoMenu $menu
	 */
	function montarBotaoExcluir($menu){
		$chave = isset($_GET['chave']) ? $_GET['chave'] : ($this->negocio->valorChave()) ? $this->negocio->valorChave() : null;
		if(!$chave) return;
		$excluir = $this->inter->pegarTexto('botaoExcluir');
		$icoExcluir = $this->inter->pegarTexto('icoBotaoExcluir');
		$menu->$excluir = new VMenu($excluir,sprintf("?c=%s&amp;chave=%s",definicaoEntidade::controle($this,'excluir'),$chave),'icon-trash icon-white');
		$menu->$excluir->passar_classeLink('btn btn-danger');
	}
	/**
	 * Monta no menu o botão de listagem
	 * @param colecaoPadraoMenu $menu
	 */
	function montarBotaoListagem($menu){
		$listagem = $this->inter->pegarTexto('botaoListagem');
		$icoListagem = $this->inter->pegarTexto('icoBotaoListagem');
		$menu->$listagem = new VMenu($listagem,sprintf("?c=%s",definicaoEntidade::controle($this,'verPesquisa')),'icon-list-alt');
		
	}
}
?>