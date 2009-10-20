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
    protected $template;

    public function passarTemplate($template){
       
        $this->template = $template;
    }
	/**
	* Método contrutor
	* @param [controle] objeto de controle padronizado
	*/
	function __construct(controle $controle){
		parent::__construct();
		$this->controle = get_class($controle);
		$this->template_dir = definicaoPasta::templates($this->controle);
	}
	/**
	* Executa o processamento e mostra a página
	* @param [texto] Nome do arquivo de formatação da visualização
	*/
	function mostrar($pagina = null){
        switch(true){
            case $this->template:
                $pagina = $this->pegar($this->template.'.html');
            break;
            case $pagina:
                $pagina = $this->pegar($pagina);
            break;
            default:
    			$pagina = $this->pegar($this->controle.'.html');
        }
        if(controle::requisicaoAjax()) {
            echo $pagina;
            return;
        }
		$this->pagina = $pagina;
		$this->template_dir = '.';
		echo $this->pegar(definicaoArquivo::pegarHtmlPadrao());
	}
	/**
	* Executa o processamento e mostra a página
	* @param [texto] Nome do arquivo de formatação da visualização
	*/
	function mostrarParaAjax($pagina = null){
        $this->mostrar($pagina);
	}
}
?>
