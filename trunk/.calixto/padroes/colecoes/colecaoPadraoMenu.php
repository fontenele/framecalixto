<?php
/**
* Classe que representa uma coleção de itens
* Esta classe padroniza a forma de agrupamento de classes de negócio no sistema
* @package FrameCalixto
* @subpackage utilitários
*/
class colecaoPadraoMenu extends colecaoPadraoObjeto {
	public $_classe;
	public $_id;
	/**
	* Método de inclusão de um subMenu no Menu
	* @param VMenu $menu
	*/
	public function subMenu(VMenu $menu){
		$this->itens[] = $menu;
	}
	public function __toString(){
		if($this->possuiItens()){
			$stMenu = "<ul id='{$this->_id}' class='{$this->_classe}' >\n";
			foreach ($this->itens as $subMenu){
				$stMenu.= $subMenu;
			}
			$stMenu .= "</ul>\n\n";
		}else{
			$stMenu = '';
		}
		return $stMenu;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	function __get($variavel){
		if(!$this->tem($variavel)){
			$this->itens[$variavel] = new VMenu($variavel);
		}
		return $this->itens[$variavel];
    }
}
?>