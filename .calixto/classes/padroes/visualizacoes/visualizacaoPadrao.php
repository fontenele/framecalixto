<?php
/**
* Classe respons�vel por passar a intelig�ncia do controle para uma tela
* Forma��o gen�rica na montagem da tela de controles padronizados
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class visualizacaoPadrao extends visualizacao{
	/**
	* @var [texto] Nome da classe de controle
	*/
	public $controle;
	/**
	* M�todo contrutor
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
	* Executa o processamento e mostra a p�gina
	* @param [texto] Nome do arquivo de formata��o da visualiza��o 
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
