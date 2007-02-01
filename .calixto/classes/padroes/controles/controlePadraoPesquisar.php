<?php
/**
* Classe de defini��o da camada de controle 
* Forma��o especialista para pesquisar um objeto de negocio
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoPesquisar extends controle{
	/**
	* M�todo inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio();
		$pagina = new pagina();
		$pagina->passarPagina();
		$atributos = array_keys(get_class_vars(get_class($negocio)));
		foreach($_POST as $campo => $valor){
			if(in_array($campo,$atributos)){
				$metodo = 'passar'.ucfirst($campo);
				$negocio->$metodo($valor);
			}
		}
		$this->sessao->registrar('pagina',$pagina);
		$this->sessao->registrar('filtro',$negocio);
	}
}
?>
