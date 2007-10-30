<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VMenu extends VEtiquetaHtml{
	/**
	* @var [in] indice de tabulação do menu
	*/
	public $tabIndex;
	/**
	* @var [string] classe de CSS do menu
	*/
	public $classe;
	/**
	* @var [array] valores do menu
	*/
	public $valores;
	/**
	* Método construtor
	* @var [array] valores do menu
	* @var [string] classe de CSS do menu
	* @var [in] indice de tabulação do menu
	*/
	function __construct($valores, $classe = 'menu', $tabIndex = '9999'){
		$this->valores = $valores;
		$this->classe = $classe;
		$this->tabIndex = $tabIndex;
	}
	/**
	* Metodo de montagem dos botoes do menu
	*/
	function montarBotoes($valores){
		$stLinks = "<ul>\n";
		foreach($valores as $texto => $url){
			if(is_array($url)){
				$stLinks .= "<li><a href=\"#\">{$texto}\n<!--[if IE 7]><!--></a><!--<![endif]-->\n";
				$stLinks .= "<table summary='text' ><tr><td>".$this->montarBotoes($url)."</td></tr></table>";
				$stLinks .= "\n<!--[if lte IE 6]></a><![endif]-->\n</li>\n";
			}else{
				$stLinks .= "<li><a href='{$url}' tabindex='{$this->tabIndex}'>{$texto}</a></li>\n";
			}
		}
		return $stLinks.'</ul>';
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	function __toString(){
		if(count($this->valores)){
			$stSaida .= "<div class='{$this->classe}'>";
			$stSaida .= $this->montarBotoes($this->valores,$tabindexMenu);
			$stSaida .= "</div>";
			return $stSaida;
		}else{
			return '';
		}
	}
}
?>
