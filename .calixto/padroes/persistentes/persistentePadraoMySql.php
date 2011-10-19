<?php

/**
 * Classe de representação de uma camada de persistencia com Banco de Dados mySql
 * @package FrameCalixto
 * @subpackage Persistente
 */
abstract class persistentePadraoMySql extends persistente {

	/**
	 * Metodo criado para especificar a estrutura da persistente
	 * @param string caminho do arquivo
	 */
	public function pegarEstrutura($arquivoXML = null) {
		$estrutura = parent::pegarEstrutura($arquivoXML);
		$ar = explode('.', $estrutura['nomeTabela']);
		if (isset($ar[1]))
			$estrutura['nomeTabela'] = $ar[1];
		return $estrutura;
	}

	/**
	 * Monta o mapeamento de tipo de dados do banco
	 * @return array mapeamento
	 */
	public function mapeamento() {
		$mapeamento['obrigatorio']['sim'] = 'not null';
		$mapeamento['obrigatorio']['nao'] = 'null';
		$mapeamento['texto'] = 'VARCHAR';
		$mapeamento['numerico'] = 'INT';
		$mapeamento['tnumerico'] = 'INT';
		$mapeamento['tmoeda'] = 'INT';
		$mapeamento['ttelefone'] = 'VARCHAR';
		$mapeamento['tdocumentopessoal'] = 'VARCHAR';
		$mapeamento['tcep'] = 'VARCHAR';
		$mapeamento['data'] = 'DATETIME';
		return $mapeamento;
	}

	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DDL
	//**************************************************************************
	//**************************************************************************
	/**
	 * Monta o comando de criação da sequence no banco de dados
	 * @return string comando de criação
	 */
	public function gerarComandoCriacaoSequence() {
		return false;
	}

	/**
	 * Gera o comando de criacao no banco de dados
	 * @return string comando de criação
	 */
	public function gerarComandoCriacaoTabela() {
		$estrutura = $this->pegarEstrutura();
		$mapeamento = $this->mapeamento();
		$comando = "create table {$estrutura['nomeTabela']} (\n";
		foreach ($estrutura['campo'] as $nomeCampo => $campo) {
			if ($campo['tipo'] == 'data') {
				if ($nomeCampo == $estrutura['chavePrimaria']) {
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]} {$mapeamento['obrigatorio'][$campo['obrigatorio']]} auto_increment primary key,\n";
				} else {
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]} {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
				}
			} else {
				if ($nomeCampo == $estrutura['chavePrimaria']) {
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]}({$campo['tamanho']}) {$mapeamento['obrigatorio'][$campo['obrigatorio']]} auto_increment primary key,\n";
				} else {
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]}({$campo['tamanho']}) {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
				}
			}
		}
		$comando = substr($comando, 0, -2) . "\n) ENGINE=InnoDB";
		return $comando;
	}

	/**
	 * Monta o comando de criação das chaves estrangeiras no banco de dados
	 * @return string comando de criação
	 */
	public function gerarComandoCriacaoChavesEstrangeiras() {
		$estrutura = $this->pegarEstrutura();
		$comando = "";
		foreach ($estrutura['campo'] as $nomeCampo => $referencia) {
			if (isset($referencia['chaveEstrangeira']))
				$comando .= "SET FOREIGN_KEY_CHECKS = 0; alter table {$estrutura['nomeTabela']} \nadd foreign key {$estrutura['nomeTabela']}_{$nomeCampo}_fk ($nomeCampo) references {$referencia['chaveEstrangeira']['tabela']}({$referencia['chaveEstrangeira']['campo']});";
		}
		return $comando;
	}

	/**
	 * Gera o comando de destruição no banco de dados
	 * @return string comando de destruição
	 */
	public function gerarComandoDestruicaoTabela() {
		try {
			$estrutura = $this->pegarEstrutura();
			return $comando = "drop table if exists {$estrutura['nomeTabela']} cascade";
		} catch (erro $e) {
			throw $e;
		}
	}

	/**
	 * Monta o comando de criação da chave primaria da tabela
	 * @return string comando de criação
	 */
	public function gerarComandoCriacaoChavePrimaria() {
		return false;
	}

	/**
	 * Gera o comando de destruição no banco de dados
	 * @return string comando de destruição
	 */
	public function gerarComandoDestruicaoSequence() {
		return false;
	}

	/**
	 * Gera a sequencia numérica da persistente correspondente
	 */
	public function gerarSequencia() {
		return 'null';
	}

	/**
	 * Retorna a ultima sequencia númerica inserida da persistente correspondente
	 * @return integer numero sequencial
	 */
	public function pegarUltimaSequencia() {
		$estrutura = $this->pegarEstrutura();
		$retorno = $this->pegarSelecao("SELECT LAST_INSERT_ID() as sequencia;");
		return $retorno[0]['sequencia'];
	}

	/**
	 * Executa um comando SQL no banco de dados.(necessita de controle de transação)
	 * @param string comando SQL para a execução
	 * @return integer número de linhas afetadas
	 */
	public function executarComando($comando = null) {
		$arComandos = explode(';', $comando);
		foreach ($arComandos as $comando) {
			if (trim($comando))
				parent::executarComando($comando);
		}
	}

	/**
	 * Método de conversão de tipo de dado
	 * @param mixed dado a ser convertido
	 * @param array campo referente
	 */
	public function converterDado($valor, $campo = null) {
		if ($campo) {
			switch (strtolower($campo['tipo'])) {
				case 'datahora':
					return new TDataHora($valor, 'y/m/d');
					break;
				case 'data':
					return new TData($valor, 'y/m/d');
					break;
			}
		} else {
			switch (true) {
				case($valor instanceof TDataHora):
					return date('Y/m/d H:i:s', $valor->pegarTempoMarcado());
					break;
				case($valor instanceof TData):
					return date('Y/m/d', $valor->pegarTempoMarcado());
					break;
			}
		}
		return parent::converterDado($valor, $campo);
	}

	public function descrever($nomeTabela = null) {
		if(!$nomeTabela){
			$estrutura = $this->pegarEstrutura();
			$nomeTabela = $estrutura['nomeTabela'];
		}
		$comando = "SHOW CREATE TABLE {$nomeTabela}";
		$this->executarComando($comando);
		$res = $this->conexao->pegarRegistro();
		$cmp = array();
		if ($res) {
			$tipos = array(
				'varchar' => 'texto',
				'int' => 'numerico',
				'datetime' => 'data',
			);
			$sql = str_replace('`','',$res['Create Table']);
			$res = array();
			$res[0]['sql'] = $sql;
			foreach ($res as $ref => $comando) {
				if (preg_match("/^[\ \t\n]*create[\ \t\n]*table[\ \t\n]*(.*)[\ \t\n]*\(/i", $comando['sql'], $val)) {
					$referencias[$ref]['nomeTabela'] = trim($val[1]);
				}
				$linhas = explode("\n", $comando['sql']);
				foreach ($linhas as $lin => $linha) {
					if(!preg_match('/^[\ \t\n]*(create|key|constraint)/i', $linha))
					  if (preg_match("/^[\ \t\n]*([aA-zZ0-9]+)[\ \t\n]*([aA-zZ0-9]+)[\ \t\n]*(\([0-9]+\)|)(.*)(default null|not null)(.*)$/i", ($linha), $valores)) {
						$valores[3] = str_replace('(', '', $valores[3]);
						$valores[3] = str_replace(')', '', $valores[3]);
						$valores = array_map('trim', $valores);
						$cmp[$valores[1]]['esquema'] = '';
						$cmp[$valores[1]]['tabela'] = $nomeTabela;
						$cmp[$valores[1]]['campo'] = $valores[1];
						$cmp[$valores[1]]['obrigatorio'] = $valores[5] == 'DEFAULT NULL' ? null : $valores[5];
						$cmp[$valores[1]]['tipo'] = $valores[2];
						$cmp[$valores[1]]['tipo_de_dado'] = $tipos[$valores[2]];
						$cmp[$valores[1]]['tamanho'] = $valores[3];
						$cmp[$valores[1]]['descricao'] = null;
						$cmp[$valores[1]]['campo_pk'] = null;
						$cmp[$valores[1]]['esquema_fk'] = null;
						$cmp[$valores[1]]['unique_key'] = null;
						$cmp[$valores[1]]['constraint'] = null;
						$cmp[$valores[1]]['tabela_fk'] = null;
						$cmp[$valores[1]]['campo_fk'] = null;
					}
					if (preg_match("/(^[\ \t\n]*create)|(^[\ \t\n]*\)[\ \t\n]*$)/i", ($linha), $valores)) {
						continue;
					}
					if (preg_match("/^[\ \t\n]*primary[\ \t\n]*key[\ \t\n]*\((.*)\)/i", ($linha), $valores)) {
						$cmp[$valores[1]]['campo_pk'] = $valores[1];
					}
					if (preg_match("/^[\ \t\n]*key[\ \t\n]*(.*)[\ \t\n]*\((.*)\)/i", ($linha), $valores)) {
						$cmp[$valores[2]]['constraint'] = $valores[1];
					}
					if (preg_match("/^[\ \t\n]*constraint[\ \t\n]*(.*)[\ \t\n]*foreign[\ \t\n]*key[\ \t\n]*\((.*)\)[\ \t\n]*references[\ \t\n]*(.*)\((.*)\)/i", ($linha), $valores)) {
						$cmp[$valores[2]]['tabela_fk'] = $valores[3];
						$cmp[$valores[2]]['campo_fk'] = $valores[4];
						continue;
					}
				}
			}
		}
		return $cmp;
	}
	/**
	 * Gera o comando de criacao dos comentários da tabela
	 * @return string comando de criação dos comentários da tabela
	 */
	public function gerarComandoComentarioTabela() {

	}

	/**
	 * Gera os comandos de criacao dos comentários dos campos da tabela
	 * @return array comandos de criação dos comentários dos campos da tabela
	 */
	public function gerarComandoComentarioCampos() {

	}

	/**
	 * Cria os comentários da tabela no banco de dados
	 */
	public function criarComentarioTabela() {

	}

	/**
	 * Cria os comentários dos campos da tabela no banco de dados
	 */
	public function criarComentarioCampos() {
		
	}

}

?>