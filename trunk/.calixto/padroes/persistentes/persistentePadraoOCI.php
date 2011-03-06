<?php
/**
* Classe de representação de uma camada de persistencia com Banco de Dados Oracle
* @package FrameCalixto
* @subpackage Persistente
*/
class persistentePadraoOCI extends persistente{
	/**
	* Monta o mapeamento de tipo de dados do banco
	* @return array mapeamento
	*/
	public function mapeamento(){
		$mapeamento['obrigatorio']['sim'] = 'not null';
		$mapeamento['obrigatorio']['nao'] = 'null';
		$mapeamento['texto'] = 'varchar2';
		$mapeamento['numerico'] = 'number';
		$mapeamento['tnumerico'] = 'number';
		$mapeamento['tmoeda'] = 'number';
		$mapeamento['ttelefone'] = 'varchar2';
		$mapeamento['tdocumentopessoal'] = 'varchar2';
		$mapeamento['tcep'] = 'varchar2';
		$mapeamento['tcnpj'] = 'varchar2';
		$mapeamento['tcpf'] = 'varchar2';
		$mapeamento['data'] = 'date';
		return $mapeamento;
	}
	/**
	* Gera a sequencia numérica da persistente correspondente
	* @return integer numero sequencial
	*/
	public function gerarSequencia(){
		try{
			$estrutura = $this->pegarEstrutura();
			$retorno = $this->pegarSelecao("select {$estrutura['nomeSequencia']}.nextval as sequencia from dual");
			return $retorno[0]['sequencia'];
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de leitura paginada
	* @param pagina pagina referente
	* @param string comando sql para execução
	* @return string comando SQL de leitura
	*/
	public function gerarComandoLerPaginado(pagina $pagina, $sql){
		try{
			if($pagina->pegarTamanhoPagina() != 0){
				$sql = "select * from (select rownum as \"número da linha\", selecao.* from ({$sql}) selecao) selecao where \"número da linha\" >= ".($pagina->pegarLinhaInicial())." and \"número da linha\" <= ".($pagina->pegarLinhaInicial() + $pagina->pegarTamanhoPagina());
			}
			return $sql;
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
			select
                'select count(*) as dependentes from ' || lower(tb.owner) || '.' || lower(tb.table_name) || ' where ' || lower(tb.column_name) || ' = ''%s'' ' as sql
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
                fk.tabela_fk =  UPPER('{$estrutura['nomeTabela']}') 
			and tb.owner = 'SGT'
			order by
                tb.table_name,
		tb.column_id
		";
		$res = $this->pegarSelecao($comando);
		if($res) foreach($res as $comando){
			$dependentes = $this->pegarSelecao(sprintf($comando['sql'],$chave));
			if($dependentes[0]['dep']) return true;
		}
		return false;
	}
	/**
	* Método que manipula cada item da cláusula de filtro
	* @param string $operacao referência utilizada na cláusula de filtro
	* @param array $campo
	* @param operador $operador
	* @param mixed $valor
	* @param mixed $dominio
	*/
	public function manipularItemDeFiltro(&$operacao,$campo,operador $operador,$valor,$dominio){
		if($operador->pegarOperador() == operador::generico) {
			if($campo['tipo'] == 'numero'){
				$operacao = " upper(%s) like upper(%%%s%%) %s ";
			}else{
				$operacao = " upper(%s) like upper('%%%s%%') %s ";
			}
		}
	}
	/**
	* Gera o comando de criacao dos comentários da tabela
	* @return string comando de criação dos comentários da tabela
	*/
	public function gerarComandoComentarioTabela(){}
	/**
	* Gera os comandos de criacao dos comentários dos campos da tabela
	* @return array comandos de criação dos comentários dos campos da tabela
	*/
	public function gerarComandoComentarioCampos(){}

	/**
	* Cria os comentários da tabela no banco de dados
	*/
	public function criarComentarioTabela(){}
	/**
	* Cria os comentários dos campos da tabela no banco de dados
	*/
	public function criarComentarioCampos(){}
}
?>
