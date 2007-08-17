<?php
/**
* Classe de controle
* Executa a gravação de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_gravar extends controlePadraoGravar{
	/**
	* Método de preenchimento do objeto de negócio para a gravação
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
