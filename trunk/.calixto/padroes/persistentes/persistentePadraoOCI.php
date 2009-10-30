<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of persistentePadraoOCI
 *
 * @author modesto
 */
class persistentePadraoOCI extends persistente{
	/**
	* Monta o mapeamento de tipo de dados do banco
	* @return [array] mapeamento
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
		$mapeamento['data'] = 'date';
		return $mapeamento;
	}
	/**
	* Gera a sequencia numérica da persistente correspondente
	* @return [in] numero sequencial
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
	* @param [pagina] pagina referente
	* @param [string] comando sql para execução
	* @return [string] comando SQL de leitura
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
				'select count(*) as dep from '||ac.owner||'.'||ac.table_name||' where '||acc.column_name||' = ''%s''' as sql
			from
				all_cons_columns acc2,
				all_constraints ac2,
				all_constraints ac,
				all_cons_columns acc
			where
				ac.owner = 'SGT' and
				acc.constraint_name = ac.constraint_name and
				ac.constraint_type = 'R' and
				ac.r_constraint_name = ac2.constraint_name and
				ac2.constraint_name = acc2.constraint_name and
				ac2.table_name = upper('{$estrutura['nomeTabela']}')
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
}
?>
