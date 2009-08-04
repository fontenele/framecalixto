<?php
/**
* Classe de representação de uma camada de persistencia com Banco de Dados postgreSql
* @package FrameCalixto
* @subpackage Persistente
*/
class persistentePadraoPG extends persistente{
	/**
	* Monta o mapeamento de tipo de dados do banco
	* @return [array] mapeamento
	*/
	public function mapeamento(){
		$mapeamento['obrigatorio']['sim'] = 'not null';
		$mapeamento['obrigatorio']['nao'] = 'null';
		$mapeamento['texto'] = 'character varying';
		$mapeamento['numerico'] = 'numeric';
		$mapeamento['tnumerico'] = 'numeric';
		$mapeamento['tmoeda'] = 'numeric';
		$mapeamento['ttelefone'] = 'character varying';
		$mapeamento['tdocumentopessoal'] = 'character varying';
		$mapeamento['tcep'] = 'character varying';
		$mapeamento['data'] = 'timestamp';
		return $mapeamento;
	}
	/**
	* Gera a sequencia numérica da persistente correspondente
	* @return [in] numero sequencial
	*/
	public function gerarSequencia(){
		try{
			$estrutura = $this->pegarEstrutura();
			$retorno = $this->pegarSelecao("select nextval('{$estrutura['nomeSequencia']}') as sequencia;");
			return $retorno[0]['sequencia'];
		}
		catch(erro $e){
			$this->criarSequence();
			$this->gerarSequencia();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	 * Método que verifica se um registro possui dependentes no banco
	 * @return boolean
	 */
	public function possuiDependentes($chave){
		$estrutura = $this->pegarEstrutura();
		$comando = "
		SELECT
			'select count(*) as dependentes from ' || n.nspname || '.' || cl.relname || ' where ' || a.attname || ' = ''%s'' ' as sql
		FROM
			pg_catalog.pg_attribute a
			JOIN pg_catalog.pg_class cl ON (a.attrelid = cl.oid AND cl.relkind= 'r')
			JOIN pg_catalog.pg_namespace n ON (n.oid = cl.relnamespace)
			JOIN pg_catalog.pg_constraint ct ON (a.attrelid = ct.conrelid AND ct.confrelid != 0 AND ct.conkey[1] = a.attnum)
			JOIN pg_catalog.pg_class clf ON (ct.confrelid = clf.oid AND clf.relkind = 'r')
			JOIN pg_catalog.pg_namespace nf ON (nf.oid = clf.relnamespace)
			JOIN pg_catalog.pg_attribute af ON (af.attrelid = ct.confrelid AND af.attnum = ct.confkey[1])
		where
			nf.nspname || '.' || clf.relname = '{$estrutura['nomeTabela']}'
		";
		$res = $this->pegarSelecao($comando);
		if($res) foreach($res as $comando){
			$dependentes = $this->pegarSelecao(sprintf($comando['sql'],$chave));
			if($dependentes) return true;
		}
		return false;
	}
}
?>