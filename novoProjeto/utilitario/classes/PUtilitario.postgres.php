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
			select 
				schemaname || '.' || tablename as tabela
			from 
				pg_tables 
			where 
				schemaname <> 'pg_catalog'
				and schemaname <> 'information_schema'
			order by
				schemaname, 
				tablename
		";
		$this->conexao->executarComando($sql);
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro['tabela'] ;
		}
		return $retorno;
	}
	
	public function lerTabela($tabela){
		if (strpos($tabela,'.') === false){
			$tabela = "tabela = '{$tabela}'";
		}else{
			$tabela = explode('.',$tabela);
			if(count($tabela) > 1){
				$tabela = "tabela.esquema = '{$tabela[0]}' and tabela.tabela = '{$tabela[1]}'";
			}
		}
		$sql = "
			select
				tabela.*,
				pk.campo_pk,
				fk.esquema_fk,
				fk.tabela_fk,
				fk.campo_fk
			from 
				(SELECT 
					n.nspname as esquema, 
					c.relname as tabela, 
					a.attname as campo, 
					--format_type(t.oid, null) as tipo,
					case 
					when format_type(t.oid, null) = 'character varying' then 'texto' 
					when format_type(t.oid, null) = 'numeric' then 'numerico' 
					else 'tdata'
					end as tipo_de_dado
				FROM 
					pg_namespace n, 
					pg_class c, 
					pg_attribute a, 
					pg_type t
				WHERE 
					n.oid = c.relnamespace
					and c.relkind = 'r'     -- no indices
					and n.nspname not like 'pg\\_%' -- no catalogs
					and n.nspname != 'information_schema' -- no information_schema
					and a.attnum > 0        -- no system att's
					and not a.attisdropped   -- no dropped columns
					and a.attrelid = c.oid
					and a.atttypid = t.oid
				) as tabela 
				left join 
				(SELECT
					pg_namespace.nspname AS esquema,
					pg_class.relname AS tabela,
					pg_attribute.attname  AS campo_pk
				FROM 
					pg_class
					JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace AND
					pg_namespace.nspname NOT LIKE 'pg_%'
					JOIN pg_attribute ON pg_attribute.attrelid=pg_class.oid AND
					pg_attribute.attisdropped='f'
					JOIN pg_index ON pg_index.indrelid=pg_class.oid AND
					pg_index.indisprimary='t' AND (
						pg_index.indkey[0]=pg_attribute.attnum OR
						pg_index.indkey[1]=pg_attribute.attnum OR
						pg_index.indkey[2]=pg_attribute.attnum OR
						pg_index.indkey[3]=pg_attribute.attnum OR
						pg_index.indkey[4]=pg_attribute.attnum OR
						pg_index.indkey[5]=pg_attribute.attnum OR
						pg_index.indkey[6]=pg_attribute.attnum OR
						pg_index.indkey[7]=pg_attribute.attnum OR
						pg_index.indkey[8]=pg_attribute.attnum OR
						pg_index.indkey[9]=pg_attribute.attnum
					)
				) as pk
				on (
					tabela.esquema = pk.esquema
					and tabela.tabela = pk.tabela
					and tabela.campo = pk.campo_pk
				)
				left join 
				(SELECT
					n.nspname AS esquema,
					cl.relname AS tabela,
					a.attname AS campo,
					--ct.conname AS chave,
					nf.nspname AS esquema_fk,
					clf.relname AS tabela_fk,
					af.attname AS campo_fk
					--pg_get_constraintdef(ct.oid) AS criar_sql
				FROM 
					pg_catalog.pg_attribute a
					JOIN pg_catalog.pg_class cl ON (a.attrelid = cl.oid AND cl.relkind = 'r')
					JOIN pg_catalog.pg_namespace n ON (n.oid = cl.relnamespace)
					JOIN pg_catalog.pg_constraint ct ON (a.attrelid = ct.conrelid AND ct.confrelid != 0 AND ct.conkey[1] = a.attnum)
					JOIN pg_catalog.pg_class clf ON (ct.confrelid = clf.oid AND clf.relkind = 'r')
					JOIN pg_catalog.pg_namespace nf ON (nf.oid = clf.relnamespace)
					JOIN pg_catalog.pg_attribute af ON (af.attrelid = ct.confrelid AND af.attnum = ct.confkey[1])
				) as fk
				on (
					tabela.esquema = fk.esquema
					and tabela.tabela = fk.tabela
					and tabela.campo = fk.campo
				)
			where
				{$tabela}
			order by
				tabela.esquema, 
				tabela.tabela,
				pk.campo_pk,
				fk.campo_fk
		";
		$this->conexao->executarComando($sql);
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro;
		}
		return $retorno;
	}
	
	public function lerSequenciasDoBanco(){
		$sql = "
			SELECT 
				c.relname AS sequencia, 
				u.usename AS usuario
			FROM 
				pg_catalog.pg_class c, pg_catalog.pg_user u, pg_catalog.pg_namespace n
			
			WHERE 
				c.relowner=u.usesysid 
				AND c.relnamespace=n.oid
				AND c.relkind = 'S' 
			ORDER BY 
				sequencia
		";
		$this->conexao->executarComando($sql);
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro['sequencia'] ;
		}
		return $retorno;
	}
}
?>