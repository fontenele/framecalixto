<?php
/**
* Classe que representa uma coleção de itens
* Esta classe padroniza a forma de agrupamento de classes de negócio no sistema
* @package FrameCalixto
* @subpackage utilitários
*/
class colecaoPadraoMenu extends colecaoPadraoObjeto {
	/**
	 * classe css do menu
	 * @var string
	 */
	public $_classe;
	/**
	 * Identificador html do menu
	 * @var string
	 */
	public $_id;
	/**
	 * Indice de tabulação do menu
	 * @var string
	 */
	public $_tabIndex;
	/**
	* Método de inclusão de um subMenu no Menu
	* @param VMenu $menu
	*/
	public function subMenu(VMenu $menu){
		$this->itens[] = $menu;
	}
	public function __toString(){
		if($this->possuiItens()){
            $id = $this->_id ? "id='{$this->_id}' " : '';
            $class = $this->_classe ? "class='{$this->_classe}' " : '';
			$stMenu = "\n<ul {$id}{$class}>\n";
			foreach ($this->itens as $subMenu){
				$stMenu.= $subMenu;
			}
			$stMenu .= "\n</ul>\n\n";
		}else{
			$stMenu = '';
		}
		return $stMenu;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __get($variavel){
		if(!$this->tem($variavel)){
			$this->itens[$variavel] = new VMenu($variavel);
            $this->itens[$variavel]->passar_tabIndex($this->_tabIndex);
		}
		return $this->itens[$variavel];
    }
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		if (!($parametros instanceof VMenu))
			throw new InvalidArgumentException('Não foi passado um VMenu para '.get_class($this).'!');
        if(!$parametros->pegar_tabIndex()){
            $parametros->passar_tabIndex($this->pegar_tabIndex());
        }
		parent::__set($variavel, $parametros);
    }
}
?>