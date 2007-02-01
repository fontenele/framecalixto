<?php
/**
* Classe responsável por passar a inteligência do controle para uma tela
* Formação genérica na montagem da tela de controles padronizados
* @package Infra-estrutura
* @subpackage visualização
*/
class visualizacaoPadrao extends visualizacao{
	/**
	* @var [texto] Nome da classe de controle
	*/
	public $controle;
	/**
	* Método contrutor
	* @param [controle] objeto de controle padronizado
	*/
	function __construct(controle $controle){
		parent::__construct();
		$this->controle = get_class($controle);
		$this->template_dir = definicaoEntidade::entidade($this->controle).'/visualizacoes/';
		if(sessaoSistema::tem('comunicacao')){
			$sessao = new sessaoSistema();
			$this->comunicacaoSistema = new VComunicacao($sessao->retirar('comunicacao'));
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
		$this->temaCSS = definicaoArquivo::pegarCss();
		$this->template_dir = '.';
		echo $this->pegar('.calixto/pagina.html');
	}
}
?>
