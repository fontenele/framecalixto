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
			$retorno = $this->pegarSelecao("select {$estrutura['nomeSequencia']}.nextval as sequencia from dual;");
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
}
?>
