<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Perfil do usuario
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_gravarColecaoUsuarioPerfil extends controlePadraoGravarColecao {
	/**
	 * Monta o objeto de subClasse para inclusão no banco de dados
	 *
	 * @param negocioPadrao $subNegocio
	 * @param string $idNegocio
	 * @param string $idSubNegocio
	 */
	public function montarSubNegocioParaInclusao($subNegocio,$idNegocio,$idSubNegocio){
		$subNegocio->passarIdPerfil($idNegocio);
		$subNegocio->passarIdUsuario($idSubNegocio);
	}
}
?>