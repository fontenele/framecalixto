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
	* @var string classe de CSS do link
	*/
	public $_classeLink;
	/**
	* @var colecao itens do menu
	*/
	public $_coMenu;
	/**
	* Método construtor
	* @var array valores do menu
	* @var string classe de CSS do menu
	* @var integer indice de tabulação do menu
	*/
	function __construct($nome,$link = null,$imagem = null){
		$this->_coMenu = new colecaoPadraoMenu();
		if(is_array($nome)){
			$this->montarSubMenus($nome);
		}else{
			$this->_nome = $nome;
			$this->_imagem = $imagem;
			$this->_link = $link;
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
	public function __toString(){
		$this->_nome = $this->_nome ? $this->_nome : 'não informado';
		$this->_tabIndex = $this->_tabIndex ? $this->_tabIndex : 9999;
		$id = $this->_id ? " id='{$this->_id}'" : null;
		$classe = $this->_classe ? " class='{$this->_classe}'" : null;
		$classeLink = $this->_classe ? " class='{$this->_classeLink}'" : null;
		$link = $this->_link ? " href='{$this->_link}'" : " href='#'";
		$imagem = !$this->_imagem ? null : "<img src='{$this->_imagem}' style='border:0px; vertical-align:bottom;' /> ";
		if($this->_coMenu->possuiItens()){
			$menu = "<li{$classe}{$id} ><a href='#'>{$imagem}{$this->_nome}</a>\n";
			$menu.= $this->_coMenu;
			$menu.= "\n</li>\n";
		}else{
			if(!$this->_link)	return '';
			$tabindex = $this->_tabIndex ? " tabindex='{$this->_tabIndex}'" : null;
			$menu =  "<li{$classe}{$id}><a{$link}{$classeLink}>{$imagem}{$this->_nome}</a></li>\n";
		}
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