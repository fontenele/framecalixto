<?php
/**
* Classe responsável por passar a inteligência do controle para uma tela
* Formação genérica na montagem da tela de controles padronizados
* @package FrameCalixto
* @subpackage visualização
*/
class visualizacaoPadrao extends visualizacao{
	/**
	* @var [texto] Nome da classe de controle
	*/
	protected $controle;
	/**
	* Método contrutor
	* @param [controle] objeto de controle padronizado
	*/
	function __construct(controle $controle){
		parent::__construct();
		$this->controle = get_class($controle);
		$this->template_dir = definicaoPasta::templates($this->controle);
		if(sessaoSistema::tem('comunicacao')){
			$sessao = new sessaoSistema();
			$this->comunicacaoSistema = new VComunicacao($sessao->retirar('comunicacao'));
		}else{
			$this->comunicacaoSistema = false;
		}
	}
	/**
	* Executa o processamento e mostra a página
	* @param [texto] Nome do arquivo de formatação da visualização
	*/
	function mostrar($pagina = null){
		if($pagina) {
			$pagina = $this->pegar($pagina);
		}else{
			$pagina = $this->pegar($this->controle.'.html');
		}
		$this->pagina = $pagina;
		$this->CssGlobal = definicaoArquivo::pegarCss();
		$this->CssLocal = definicaoPasta::css($this->controle);
		$this->JsLocal = definicaoPasta::js($this->controle);
		$this->template_dir = '.';
		echo $this->pegar(definicaoArquivo::pegarHtmlPadrao());
	}
	/**
	* Executa o processamento e mostra a página
	* @param [texto] Nome do arquivo de formatação da visualização
	*/
	function mostrarParaAjax($pagina = null){
		if(isset($this->_tpl_vars['action'])){
			if (preg_match('/^\?c=C(.*)/', $this->action, $resultado)){
				$this->action .= '&ajax=1';
			}
		}
		if($pagina) {
			echo $this->pegar($pagina);
		}else{
			echo $this->pegar($this->controle.'.html');
		}
	}
}
?>