<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class PUtilitario extends persistentePadraoPG {
	
	public function lerTabelas(){
		$sql = "
			select table_name tabela from all_tables order by table_name
		";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro['tabela'] ;
		}
		return $retorno;
	}
	public function lerCampos($tabela){
		$sql = "
			select column_name from all_tab_columns where table_name = upper('{$tabela}') and owner = 'SGT'
		";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro;
		}
		return $retorno;
	}
	public function lerTabela($tabela){
	    $sql = "
			-- Describe da tabela
			select
				lower(tb.owner) as esquema,
				lower(tb.table_name) as tabela,
				lower(tb.column_name) as campo,
				lower(cmt.comments) as descricao,
				decode(
					tb.data_type,'DATE','data',
					decode(tb.data_type,'NUMBER','numerico',
						decode(tb.data_type,'VARCHAR2','texto')
					)
				) as tipo_de_dado,
				lower(fk.esquema_fk) as esquema_fk,
				lower(fk.tabela_fk) as tabela_fk,
				lower(fk.campo_fk) as campo_fk,
				lower(pk.campo_pk) as campo_pk,
				decode(data_type,'DATE','',decode(data_type,'NUMBER',data_precision,data_length)) as tamanho,
				decode(nullable,'N','not null','') as OBRIGATORIO
			from
				all_tab_columns tb
				left join (-- Recupera a Primary Key
					select
						ac.owner as esquema_pk,
						acc.table_name as tabela_pk,
						acc.column_name as campo_pk
					from
						all_constraints ac, all_cons_columns acc
					where
						ac.owner = 'SGT' and
						acc.constraint_name = ac.constraint_name and
						ac.constraint_type = 'P'
					) pk on (pk.esquema_pk = tb.owner and pk.tabela_pk = tb.table_name and tb.column_name = pk.campo_pk)
				left join (-- Recupera Dados da Foreign Key
					select
						ac.owner as esquema,
						ac.table_name as tabela,
						acc.column_name as campo,
						ac2.owner as esquema_fk,
						ac2.table_name as tabela_fk,
						acc2.column_name as campo_fk
					from all_cons_columns acc2, all_constraints ac2, all_constraints ac, all_cons_columns acc
					where
						ac.owner = 'SGT' and
						acc.constraint_name = ac.constraint_name and
						ac.constraint_type = 'R' and
						ac.r_constraint_name = ac2.constraint_name and
						ac2.constraint_name = acc2.constraint_name
					) fk on (fk.esquema = tb.owner and fk.tabela = tb.table_name and tb.column_name = fk.campo)
				left join (-- Recupera comentários dos campos
					SELECT
						table_name,
						column_name,
						comments
					FROM
						user_col_comments
				) cmt on (tb.table_name = cmt.table_name and tb.column_name = cmt.column_name)
			where
				tb.table_name=upper('{$tabela}')
				and tb.owner = 'SGT'
			order by
				tb.column_id
			";

		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			if(!$registro['campo_fk']){
				$retorno[$registro['campo']] = $registro;
			}else{
				if(!isset($retorno[$registro['campo']])){
					$retorno[$registro['campo']] = $registro;
				}else{
					if($retorno[$registro['campo']]['campo'] == $registro['campo_fk']){
						$retorno[$registro['campo']] = $registro;
					}
				}
			}
		}
		return $retorno;
	}
	
	public function lerSequenciasDoBanco(){
		$sql = "
			select
				sequence_owner as esquema,
				sequence_name as sequencia
			from
				all_sequences
			where
				sequence_owner = 'SGT'
			order by
				sequence_name
		";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[$registro['esquema'].'.'.$registro['sequencia']] = $registro['esquema'].'.'.$registro['sequencia'] ;
		}
		return $retorno;
	}
}
?>