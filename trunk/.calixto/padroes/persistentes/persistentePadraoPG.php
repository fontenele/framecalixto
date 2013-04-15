<?php

/**
 * Classe de representação de uma camada de persistencia com Banco de Dados postgreSql
 * @package FrameCalixto
 * @subpackage Persistente
 */
class persistentePadraoPG extends persistente {

	/**
	 * Monta o mapeamento de tipo de dados do banco
	 * @return array mapeamento
	 */
	public function mapeamento() {
		$mapeamento['obrigatorio']['sim'] = 'not null';
		$mapeamento['obrigatorio']['nao'] = 'null';
		$mapeamento['texto'] = 'character varying';
		$mapeamento['numerico'] = 'numeric';
		$mapeamento['tnumerico'] = 'numeric';
		$mapeamento['tmoeda'] = 'numeric';
		$mapeamento['ttelefone'] = 'character varying';
		$mapeamento['tdocumentopessoal'] = 'character varying';
		$mapeamento['tcep'] = 'character varying';
		$mapeamento['tcnpj'] = 'character varying';
		$mapeamento['tcpf'] = 'character varying';
		$mapeamento['tdatahora'] = 'timestamp';
		$mapeamento['data'] = 'timestamp';
		$mapeamento['data e hora'] = 'timestamp';
		return $mapeamento;
	}

	/**
	 * Gera a sequencia numérica da persistente correspondente
	 * @return integer numero sequencial
	 */
	public function gerarSequencia() {
		try {
			$estrutura = $this->pegarEstrutura();
			$retorno = $this->pegarSelecao("select nextval('{$estrutura['nomeSequencia']}') as sequencia;");
			return $retorno[0]['sequencia'];
		} catch (erro $e) {
			$this->criarSequence();
			$this->gerarSequencia();
		} catch (erro $e) {
			throw $e;
		}
	}

	/**
	 * Retorna a ultima sequencia númerica inserida da persistente correspondente
	 * @return integer numero sequencial
	 */
	public function pegarUltimaSequencia() {
		return false;
	}

	/**
	 * Método que verifica se um registro possui dependentes no banco
	 * @return boolean
	 */
	public function possuiDependentes($chave) {
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
		if ($res)
			foreach ($res as $comando) {
				$dependentes = $this->pegarSelecao(sprintf($comando['sql'], $chave));
				if ($dependentes)
					return true;
			}
		return false;
	}
	function gerarItemDeFiltro(operador $operador, $campo, $tipo) {
		$retorno = parent::gerarItemDeFiltro($operador, $campo, $tipo);
		if ($retorno && ($operador->pegarOperador() == operador::generico))
			$retorno = sprintf(
				" upper(accent_remove(%s)) like upper(accent_remove('%%%s%%')) %s ",
				$campo,
				$this->tratarInjection($operador->pegarValor()),
				$operador->pegarRestricao()
			);
		return $retorno;
	}

	public static function gerarComandoAccentRemove() {
		return "
--------------------------------accent_remove-----------------------------------
--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--
-- CREATE PROCEDURAL LANGUAGE plpgsql;
--SET search_path = public, pg_catalog;
--
-- Name: accent_remove(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--
CREATE OR REPLACE FUNCTION accent_remove(text_input character varying) RETURNS character varying
	AS $$
DECLARE 
	text_output varchar;
BEGIN	
	text_output = text_input;

	text_output = replace(text_output,'Á','A');
	text_output = replace(text_output,'á','a');
	text_output = replace(text_output,'à','a');
	text_output = replace(text_output,'À','A');
	text_output = replace(text_output,'â','a');
	text_output = replace(text_output,'Â','A');
	text_output = replace(text_output,'ä','a');
	text_output = replace(text_output,'Ä','A');
	text_output = replace(text_output,'ã','a');
	text_output = replace(text_output,'Ã','A');
	text_output = replace(text_output,'å','a');
	text_output = replace(text_output,'Å','A');
	text_output = replace(text_output,'ð','o');
	text_output = replace(text_output,'é','e');
	text_output = replace(text_output,'É','E');
	text_output = replace(text_output,'È','E');
	text_output = replace(text_output,'è','e');
	text_output = replace(text_output,'Ê','E');
	text_output = replace(text_output,'ê','e');
	text_output = replace(text_output,'Ë','E');
	text_output = replace(text_output,'ë','e');
	text_output = replace(text_output,'í','i');
	text_output = replace(text_output,'Í','I');
	text_output = replace(text_output,'ì','i');
	text_output = replace(text_output,'Ì','I');
	text_output = replace(text_output,'î','i');
	text_output = replace(text_output,'Î','I');
	text_output = replace(text_output,'ï','i');
	text_output = replace(text_output,'Ï','I');
	text_output = replace(text_output,'ñ','n');
	text_output = replace(text_output,'Ñ','N');
	text_output = replace(text_output,'ó','o');
	text_output = replace(text_output,'Ó','O');
	text_output = replace(text_output,'Ò','O');
	text_output = replace(text_output,'ò','o');
	text_output = replace(text_output,'Ô','O');
	text_output = replace(text_output,'ô','o');
	text_output = replace(text_output,'Ö','O');
	text_output = replace(text_output,'ö','o');
	text_output = replace(text_output,'õ','o');
	text_output = replace(text_output,'Õ','O');
	text_output = replace(text_output,'Ú','U');
	text_output = replace(text_output,'ú','u');
	text_output = replace(text_output,'ù','u');
	text_output = replace(text_output,'Ù','U');
	text_output = replace(text_output,'û','u');
	text_output = replace(text_output,'Û','U');
	text_output = replace(text_output,'ü','u');
	text_output = replace(text_output,'Ü','U');
	text_output = replace(text_output,'ý','y');
	text_output = replace(text_output,'Ý','Y');
	text_output = replace(text_output,'ÿ','y');
	text_output = replace(text_output,'Ç','C');
	text_output = replace(text_output,'ç','c');
	return text_output;
end; $$
	LANGUAGE plpgsql STRICT;
--	
		";
	}

	/**
	 * Método de criação da função de banco accent_remove
	 */
	public function plAccentRemove() {
		$this->executarComando(self::gerarComandoAccentRemove());
		$this->executarComando("ALTER FUNCTION public.accent_remove(text_input character varying) OWNER TO postgres;");
	}

}

?>
