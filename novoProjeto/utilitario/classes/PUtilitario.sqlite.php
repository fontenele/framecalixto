<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class PUtilitario extends persistentePadraoSqlite {

	public function lerTabelasComDescricao(){
		$sql = "
			SELECT 
				'' as esquema,
				name as nome,
				'' as descricao
			FROM sqlite_master
			WHERE type='table' and name <> 'sqlite_sequence'
			ORDER BY name
		";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro ;
		}
		return $retorno;
	}
	public function lerTabelas(){
		$sql = "
			SELECT name as tabela FROM sqlite_master
			WHERE type='table' and name <> 'sqlite_sequence'
			ORDER BY name
		";
		$this->conexao->executarComando($sql);
		$retorno = array();
		while ($registro = $this->conexao->pegarRegistro()){
			$retorno[] = $registro['tabela'] ;
		}
		return $retorno;
	}
	public function lerCampos($tabela){
		return $this->pegarPersistente($tabela)->descrever();
	}

	/**
	 * Retorna uma persistente referente a tabela passada
	 * @param string $tabela 
	 */
	public function pegarPersistente($tabela){
		$d = dir(".");
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/xml/entidade.xml')){
					$xml = simplexml_load_file($arquivo.'/xml/entidade.xml');
					$arTabela = explode('.',strval($xml['nomeBanco']));
					$nomeTabela = $arTabela[count($arTabela)-1];
					if($tabela == $nomeTabela){
						$dir = dir($arquivo.'/classes');
						while ( false !== ($classe = $dir->read())){
							if(preg_match('/(.*)\.sqlite\.php/', $classe, $res)){
								return new $res[1]($this->conexao);
							}
						}
					}
				}
			}
		}
		$d->close();
	}
	public function lerTabela($tabela){
		$arTabela = explode('.',$tabela);
		if(count($arTabela)>1) $tabela = $arTabela[1];
		return ($this->pegarPersistente($tabela)) ? $this->pegarPersistente($tabela)->descrever($tabela) : array();
	}
	public function lerRestricoes($tabela){
		return array();
	}
	
	public function lerSequenciasDoBanco(){
		return array();
	}
}
?>
