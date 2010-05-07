<?php
/**
* Classe de persistência
* @package Sistema
* @subpackage Acesso
*/
class PAcesso extends persistentePadraoPG{
	/**
	 * Método de leitura de acessos pelo usuário
	 * @param idUsuario 
	 * @param nmAcesso
	 * @return array
	 */
	public function lerAcessosPorUsuario($idUsuario,$nmAcesso = null){
		$nmAcesso = $nmAcesso ? " and aces_nm_acesso = '{$nmAcesso}' " : null;
		$sql = "
			select
				acesso.*
			from
				usuario_perfil
				inner join perfil on (usup_id_perfil = perf_id_perfil)
				inner join acesso on (perf_id_perfil = aces_id_perfil)
			where
				usup_id_usuario = '{$idUsuario}' {$nmAcesso}
			union
			select
				acesso.*
			from
				acesso
			where
				aces_id_usuario = '{$idUsuario}' {$nmAcesso}
		";
		return $this->pegarSelecao($sql);
	}
}
?>