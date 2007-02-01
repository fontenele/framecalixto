<?php
/**
* Classe de controle
* Executa a grava��o de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_gravar extends controlePadraoGravar{
	/**
	* M�todo de preenchimento do objeto de neg�cio para a grava��o
	* @param [negocio] objeto para preenchimento
	*/
	public function preencherNegocioParaGravar(negocio $negocio){
		parent::preencherNegocioParaGravar($negocio);
		$nUsuario = sessaoSistema::pegar('usuario');
		if(!$negocio->valorChave()){
			$negocio->passarIdCriador($nUsuario->valorChave());
			$negocio->passarIdResponsavel($nUsuario->valorChave());
			$negocio->passarIdResponsavelAnterior($nUsuario->valorChave());
		}
	}
}
?>
