<?php
/**
* Classe de representa��o de uma camada de persistencia com Banco de Dados postgreSql
* @package Infra-estrutura
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
		$mapeamento['data'] = 'date';
		return $mapeamento;
	}
	/**
	* Gera a sequencia num�rica da persistente correspondente
	* @return [in] numero sequencial
	*/
	public function gerarSequencia(){
		try{
			$estrutura = $this->pegarEstrutura();
			$conexao = $this->pegarConexao();
			$retorno = $this->pegarSelecao("select nextval('sq_{$estrutura['nomeTabela']}') as sequencia;");
			$this->fecharConexao($conexao);
			return $retorno[0]['sequencia'];
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
