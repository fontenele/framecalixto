<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VMenu extends VEtiquetaHtml{
	/**
	* Link
	*/
	public $link;
	/**
	* id
	*/
	public $id;
	/**
	* nome
	*/
	public $nome;
	/**
	* imagem
	*/
	public $imagem;
	
	
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
	* @var colecao itens do menu
	*/
	public $coMenu;
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
				$stLinks .= "<li><a class=\"menuComSubItem\" href=\"#\"><strong>{$texto}</strong>\n<!--[if IE 7]><!--></a><!--<![endif]-->\n";
				$stLinks .= "<table summary='text' ><tr><td>".$this->montarBotoes($url)."</td></tr></table>";
				$stLinks .= "\n<!--[if lte IE 6]></a><![endif]-->\n</li>\n";
			}else{
				$stLinks .= "<li><a href='{$url}' tabindex='{$this->tabIndex}'>{$texto}</a></li>\n";
			}
		}
		return $stLinks.'</ul>';
	}
	/**
	* Método de montagem do menu
	*/
	public function montarMenu(VMenu $vMenu){
		if($vMenu->coMenu->possuiItens()){
			$menu = "<ul class='{$this->classe}' >\n";
			while ($vSubMenu = $vMenu->coMenu->avancar()){
				$menu .= $vSubMenu->montarMenu();
			}
			$menu .= "</ul>";
		}else{
			$menu = "<li id={$this->id}><a href='{$this->link}' tabindex='{$this->tabIndex}' >{$this->nome}</a></li>\n";
		}
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	function __toString(){
		switch (true){
			case $this->coMenu instanceof colecao:
				$stSaida = "<div class='{$this->classe}'>";
				$stSaida .= $this->montarMenu($this);
				$stSaida .= "</div>";
				return $stSaida;
			break;
			case (count($this->valores)):
				$stSaida = "<div class='{$this->classe}'>";
				$stSaida .= $this->montarBotoes($this->valores,$this->tabIndex);
				$stSaida .= "</div>";
				return $stSaida;
			break;
			default:
				return '';
		}
	}
}
?>
