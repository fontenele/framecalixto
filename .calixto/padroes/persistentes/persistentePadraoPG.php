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
}
?>