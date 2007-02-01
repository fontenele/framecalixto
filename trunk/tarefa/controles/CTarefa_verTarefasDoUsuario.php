<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verTarefasDoUsuario extends controlePadraoVerPesquisa{
	/**
	* @var [NUsuario] usuario logado
	*/
	public $usuario;
	
	public function inicial(){
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->usuario = sessaoSistema::pegar('usuario');
		$this->visualizacaoPadrao->usuario = $this->usuario->valorDescricao();
		parent::inicial();
	}
	/**
	* Método de criação da coleção a ser listada
	* @return [colecao] coleção a ser listada
	*/
	public function definirColecao(){ 
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio();
		return $negocio->lerTarefasDoUsuario($this->pegarUsuario(), $this->pegarPagina());
	}
	/**
	* Método de criação do controle de listagem
	* @return [controle] Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CTarefa_listagem();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->internacionalizacao->pegarTexto('tarefasDoUsuario')]  = 
			'javascript:document.formulario.submit();';
		$menu[$this->internacionalizacao->pegarTexto('botaoNovo')] = 
			sprintf($link,definicaoEntidade::controle($this,'verEdicao'));
		return $menu;
	}
}
?>
