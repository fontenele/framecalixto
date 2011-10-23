<?php
/**
* Classe de controle
* Grava os registros de perfil relativos a um usuário
* @package Sistema
* @subpackage Usuario
*/
class CUsuario_gravarColecaoUsuarioPerfil extends controlePadraoGravarColecao {
	/**
	 * Monta o objeto de subClasse para inclusão no banco de dados
	 *
	 * @param negocioPadrao $subNegocio
	 * @param string $idNegocio
	 * @param string $idSubNegocio
	 */
	public function montarSubNegocioParaInclusao($subNegocio,$idNegocio,$idSubNegocio){
		$subNegocio->passarIdUsuario($idNegocio);
		$subNegocio->passarIdPerfil($idSubNegocio);
	}
}
?>