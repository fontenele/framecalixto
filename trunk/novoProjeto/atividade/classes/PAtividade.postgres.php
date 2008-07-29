<?php
/**
* Classe de persistência
* @package Sistema
* @subpackage atividade
*/
class PAtividade extends persistentePadraoPG{
	public function lerUltimaAtividadeDeEncaminhamento($idTarefa){
		$sql = "
		select * from atividade
		where
			ativ_id_tarefa = {$idTarefa}
			and ativ_cs_atividade = '2'
			and ativ_dt_inicio = (
						select 
							max(ativ_dt_inicio) 
						from 
							atividade 
						where 
							ativ_id_tarefa = {$idTarefa})
							and ativ_cs_atividade = '2'
		";
		return $this->pegarSelecao($sql);
	}
}
?>