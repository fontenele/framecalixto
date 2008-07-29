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
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		if(!sessaoSistema::tem('usuario')) throw(new erroLogin('Usuário não registrado para acessar suas tarefas!'));
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->usuario = sessaoSistema::pegar('usuario');
		$this->visualizacao->usuario = $this->usuario->valorDescricao();
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
		return new CTarefa_listagemTarefasUsuario();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('tarefasDoUsuario')]  =
			'javascript:document.formulario.submit();';
		$menu[$this->inter->pegarTexto('botaoNovo')] =
			sprintf($link,definicaoEntidade::controle($this,'verEdicao'));
		return $menu;
	}
}
?>
