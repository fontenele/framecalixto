<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class PUtilitario extends persistentePadraoMySql {
	
	public function lerTabelas(){
		$sql = "show tables";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro[key($registro)] ;
		}
		return $retorno;
	}
	public function lerCampos($tabela){
		return $this->lerTabela($tabela);
	}

	public function lerTabela($tabela){
		$sql = "describe {$tabela}";
		$this->conexao->executarComando($sql);
		$retorno = array();
		$i = 0;
		while ($registro = $this->conexao->pegarRegistro()){
			$i++;
			$retorno[$i]['campo'] = $registro['Field'] ;//ajeitar tudo !!!
			$retorno[$i]['tipo_de_dado'] = $registro['Type'];
			$retorno[$i]['tamanho'] = $registro['Type'];
			$retorno[$i]['campo_pk'] = $registro['Key'] == 'PRI' ? $registro['Field'] : '';
			$retorno[$i]['esquema_fk'] = '';
			$retorno[$i]['tabela_fk'] = $registro['Key'] == 'MUL' ? "?" : '' ;
			$retorno[$i]['campo_fk'] = $registro['Key'] == 'MUL' ? "?" : '' ;
		}
		return $retorno;
	}
	
	public function lerSequenciasDoBanco(){
		return array();
	}
}
?>
