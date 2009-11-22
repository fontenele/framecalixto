<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VMenu extends objeto{
	/**
	* Link
	*/
	public $_link;
	/**
	* id
	*/
	public $_id;
	/**
	* nome
	*/
	public $_nome;
	/**
	* imagem
	*/
	public $_imagem;
	/**
	* @var integer indice de tabulação do menu
	*/
	public $_tabIndex;
	/**
	* @var string classe de CSS do menu
	*/
	public $_classe;
	/**
	* @var string titulo do menu
	*/
	public $_title;
	/**
	* @var colecao itens do menu
	*/
	public $_coMenu;
	/**
	* @var target do link
	*/
	public $_target;
	/**
	* Método construtor
	* @var array valores do menu
	* @var string classe de CSS do menu
	* @var integer indice de tabulação do menu
	*/
	function __construct($nome,$link = null,$imagem = null,$target = null){
		$this->_coMenu = new colecaoPadraoMenu();
		if(is_array($nome)){
			$this->montarSubMenus($nome);
		}else{
			$this->_nome = $nome;
			$this->_imagem = $imagem;
			$this->_link = $link;
			$this->_target = $target;
		}
	}
	/**
	 * Metodo de parseamento do array para estrutura de menus
	 * @param array $array
	 */
	protected function montarSubMenus($array){
		foreach ($array as $chave => $valor) {
			if(is_array($valor)){
				$menu = new VMenu($valor);
				$menu->_nome = $chave;
				$this->subMenu($menu);
			}else{
				$menu = new VMenu($chave,$valor);
				$this->subMenu($menu);
			}
		}
	}
	/**
	 * Método de inclusão de um subMenu no Menu
	 * @param VMenu $menu
	 */
	public function subMenu(VMenu $menu){
		$this->_coMenu->subMenu($menu);
	}
	/**
	* Método de apresentação da classe como string
	*/
	public function __toString( )
	{
		$this->_nome = $this->_nome ? $this->_nome : 'não informado';
		$this->_tabIndex = $this->_tabIndex ? $this->_tabIndex : 9999;
		$imagem = !$this->_imagem ? null : "<img src='{$this->_imagem}' style='border:0px; vertical-align:bottom;' />";
		
		if( $this->_coMenu->possuiItens( ) )
		{
			$menu = "<li class='menuItem'><span class='menuTitulo'>{$this->_nome}</span>";
			$menu.= "\n\n<ul><li>{$this->_coMenu}</li></ul>";
			$menu.= "</li>";
		}else{
			if( !$this->_link ){ return ''; }
			$target = $this->_target ? "target='{$this->_target}' " : "";
            $id = $this->_id ? "id='{$this->_id}' " : '';
			$title = $this->_title ? "title='{$this->_title}' " : '';
			$class = $this->_classe ? "class='{$this->_classe}' " : '';
			$menu =  "<li {$id}{$class}{$title} ><a href='{$this->_link}' tabindex='{$this->_tabIndex}' {$target}>{$imagem} {$this->_nome}</a></li>\n";
		}
		
		/*if($this->_coMenu->possuiItens()){
			$menu  = "<li><a href=\"#\">{$imagem}<strong>{$this->_nome}...</strong>\n<!--[if IE 7]><!--></a><!--<![endif]-->\n";
			$menu .= "<table summary='text' ><tr><td>".$this->_coMenu."</td></tr></table>";
			$menu .= "\n<!--[if lte IE 6]></a><![endif]-->\n</li>\n";
		}else{
			if(!$this->_link)	return '';
			$target = $this->_target ? "target='{$this->_target}'" : "";
			$menu =  "<li id='{$this->_id}'><a href='{$this->_link}' tabindex='{$this->_tabIndex}' {$target}>{$imagem} {$this->_nome}</a></li>\n";
		}*/
		
		return $menu;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		$this->_coMenu->$variavel = $parametros;
    }
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __get($variavel){
		return $this->_coMenu->$variavel;
    }
}
?>