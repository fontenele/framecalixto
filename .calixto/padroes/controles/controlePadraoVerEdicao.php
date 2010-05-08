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
		$gravar = $this->inter->pegarTexto('botaoGravar');
		$excluir = $this->inter->pegarTexto('botaoExcluir');
		$listagem = $this->inter->pegarTexto('botaoListagem');
		$chave = isset($_GET['chave']) ? $_GET['chave'] : ($this->negocio->valorChave()) ? $this->negocio->valorChave() : null;
		$menu->$gravar = new VMenu($gravar,'javascript:$.submeter();',definicaoPasta::tema().'icones/disk.png');
		if($chave) $menu->$excluir = new VMenu($excluir,sprintf("?c=%s&amp;chave=%s",definicaoEntidade::controle($this,'excluir'),$chave),definicaoPasta::tema().'icones/delete.png');
		$menu->$listagem = new VMenu($listagem,sprintf("?c=%s",definicaoEntidade::controle($this,'verPesquisa')),definicaoPasta::tema().'icones/application_view_list.png');
		return $menu;
	}
}
?>