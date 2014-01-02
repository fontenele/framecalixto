<?php

/**
 * Classe de representação de uma camada de persistencia com Banco de Dados sqlite
 * @package FrameCalixto
 * @subpackage Persistente
 */
class persistentePadraoSqlite extends persistente {

	/**
	 * Verificacao de correcao para o schema no nome da tabela
	 */
	protected static $correcaoSchema;

	/**
	 * Monta o mapeamento de tipo de dados do banco
	 * @return array mapeamento
	 */
	public function mapeamento() {
		$mapeamento['obrigatorio']['sim'] = 'not null';
		$mapeamento['obrigatorio']['nao'] = 'null';
		$mapeamento['texto'] = 'text';
		$mapeamento['numerico'] = 'integer';
		$mapeamento['tnumerico'] = 'integer';
		$mapeamento['tmoeda'] = 'integer';
		$mapeamento['ttelefone'] = 'text';
		$mapeamento['tdocumentopessoal'] = 'text';
		$mapeamento['tcep'] = 'text';
		$mapeamento['tcnpj'] = 'text';
		$mapeamento['tcpf'] = 'text';
		$mapeamento['data'] = 'timestamp';
		$mapeamento['data e hora'] = 'timestamp';
		return $mapeamento;
	}

	/**
	 * Metodo criado para especificar a estrutura da persistente
	 * @param string caminho do arquivo
	 */
	public function pegarEstrutura($arquivoXML = null) {
		if (isset(self::$correcaoSchema[get_class($this)])) {
			return self::$correcaoSchema[get_class($this)];
		} else {
			$estrutura = parent::pegarEstrutura($arquivoXML);
			$arNome = explode('.', $estrutura['nomeTabela']);
			$estrutura['nomeTabela'] = $arNome[count($arNome) - 1];
			;
			foreach ($estrutura['campo'] as $nomeCampo => $referencia) {
				if (isset($referencia['chaveEstrangeira'])) {
					$arNome = explode('.', $referencia['chaveEstrangeira']['tabela']);
					$estrutura['campo'][$nomeCampo]['chaveEstrangeira']['tabela'] = $arNome[count($arNome) - 1];
				}
			}
			return self::$correcaoSchema[get_class($this)] = $estrutura;
		}
	}
	/**
	* Método de conversão de tipo de dado
	* @param mixed dado a ser convertido
	* @param array campo referente
	*/
	public function converterDado($valor,$campo = null){
		if((!$campo) && ($valor instanceof TData)){
			return $valor->pegarData('Y-m-d');
		}
		if($campo){
			switch(strtolower($campo['tipo'])){
				case 'datahora':
					return new TDataHora($valor,'Y/m/d');
				break;
				case 'data':
					//x(new TData($valor,'Y-m-d'));
					return new TData($valor,'Y/m/d');
				break;
			}
		}
		return parent::converterDado($valor, $campo);
	}

	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DML
	//**************************************************************************
	//**************************************************************************
	/**
	 * Gera o comando de inserção de um registro no banco de dados
	 * @param array correlativa entre campos e valores do registro
	 * @return string comando de inserção
	 */
	public function gerarComandoInserir($array) {
		$estrutura = $this->pegarEstrutura();
		$campos = implode(',', array_keys($array));
		foreach ($array as $campo => $valor) {
			if (empty($valor)) {
				$valores[] = "null";
			} else {
				if ($campo == $estrutura['chavePrimaria']) {
					$valores[] = "null";
				} else {
					if(is_object($valor)){
						$valor = $this->converterDado($valor);
					}
					$valores[] = "'" . str_replace("'", "''", $valor) . "'";
				}
			}
		}
		$valores = implode(',', $valores);
		return "insert into {$estrutura['nomeTabela']} ($campos) values ($valores);\n";
	}

	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DDL
	//**************************************************************************
	//**************************************************************************
	/**
	 * Executa o comando de criacao no banco de dados
	 */
	public function recriar() {
		try {
			$this->destruir();
			$this->criar();
		} catch (erro $e) {
			throw $e;
		}
	}

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
		$fk = "";
		foreach ($estrutura['campo'] as $nomeCampo => $campo) {
			$tamanho = $campo['tamanho'] ? "({$campo['tamanho']})" : "";
			if ($nomeCampo == $estrutura['chavePrimaria']) {
				$comando .= "	$nomeCampo integer primary key autoincrement {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
			} else {
				$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]}{$tamanho} {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
				if (isset($campo['chaveEstrangeira'])) {
					$fk .= ",\n\tFOREIGN KEY({$nomeCampo}) REFERENCES {$campo['chaveEstrangeira']['tabela']}({$campo['chaveEstrangeira']['campo']})";
				}
			}
		}
		$comando = substr($comando, 0, -2) . $fk . "\n)";
		return $comando;
	}

	/**
	 * Gera o comando de destruição no banco de dados
	 * @return string comando de destruição
	 */
	public function gerarComandoDestruicaoTabela() {
		try {
			$estrutura = $this->pegarEstrutura();
			return $comando = "drop table if exists {$estrutura['nomeTabela']}";
		} catch (erro $e) {
			throw $e;
		}
	}

	/**
	 * Monta o comando de criação das chaves estrangeiras no banco de dados
	 * @return string comando de criação
	 */
	public function gerarComandoCriacaoChavesEstrangeiras() {
		return null;
		try {
			$estrutura = $this->pegarEstrutura();
			$arNomeTable = explode('.', $estrutura['nomeTabela']);
			$nomeTabela = $arNomeTable[count($arNomeTable) - 1];
			$comando = "";
			foreach ($estrutura['campo'] as $nomeCampo => $referencia) {
				if (isset($referencia['chaveEstrangeira'])) {
					$comando .= "alter table {$estrutura['nomeTabela']} \n
					add foreign key ($nomeCampo) \n
					references {$referencia['chaveEstrangeira']['tabela']}({$referencia['chaveEstrangeira']['campo']});";
				}
			}
			return $comando;
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
		$retorno = $this->pegarSelecao("SELECT last_insert_rowid() as sequencia;");
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
	 * Método que verifica se um registro possui dependentes no banco
	 * @return boolean
	 */
	public function possuiDependentes($chave) {
		$estrutura = $this->pegarEstrutura();
		$comando = "
			select sql from sqlite_master
		where
			lower(sql) like lower('%references {$estrutura['nomeTabela']}(%')
		";
		$res = $this->pegarSelecao($comando);
		$referencias = false;
		if ($res)
			foreach ($res as $ref => $comando) {
				if (preg_match("/^[\ \t\n]*create[\ \t\n]*table[\ \t\n]*(.*)[\ \t\n]*\(/i", $comando['sql'], $val)) {
					$referencias[$ref]['nomeTabela'] = $val[1];
				}
				$linhas = explode("\n", $comando['sql']);
				foreach ($linhas as $lin => $linha) {
					if (preg_match("/^[\ \t\n]*foreign[\ \t\n]*key[\ \t\n]*\((.*)\)[\ \t\n]*references[\ \t\n]*(.*)\((.*)\)/i", ($linha), $valores)) {
						if ($estrutura['nomeTabela'] == $valores[2]) {
							$referencias[$ref]['ref'][$lin]['campoTabela'] = $valores[1];
							$referencias[$ref]['ref'][$lin]['tabelaRef'] = $valores[2];
							$referencias[$ref]['ref'][$lin]['campoRef'] = $valores[3];
						}
					}
				}
			}
		if (!$referencias)
			return false;
		foreach ($referencias as $ref) {
			foreach ($ref['ref'] as $fk) {
				$dependentes = $this->pegarSelecao("select * from {$ref['nomeTabela']} where {$fk['campoTabela']} = '{$chave}'");
				if ($dependentes)
					return true;
			}
		}
		return false;
	}

	public function descrever($nomeTabela = null) {
		if(!$nomeTabela){
			$estrutura = $this->pegarEstrutura();
			$nomeTabela = $estrutura['nomeTabela'];
		}
		$comando = "
			select sql from sqlite_master
		where
			tbl_name = '{$nomeTabela}'";
		$res = $this->pegarSelecao($comando);
		$cmp = array();
		if ($res) {
			$tipos = array(
				'text' => 'texto',
				'integer' => 'numerico',
				'timestamp' => 'data',
			);
			foreach ($res as $ref => $comando) {
				if (preg_match("/^[\ \t\n]*create[\ \t\n]*table[\ \t\n]*(.*)[\ \t\n]*\(/i", $comando['sql'], $val)) {
					$referencias[$ref]['nomeTabela'] = $val[1];
				}
				$linhas = explode("\n", $comando['sql']);
				foreach ($linhas as $lin => $linha) {
					if (preg_match("/(^[\ \t\n]*create)|(^[\ \t\n]*\)[\ \t\n]*$)/i", ($linha), $valores)) {
						continue;
					}
					if (preg_match("/^[\ \t\n]*foreign[\ \t\n]*key[\ \t\n]*\((.*)\)[\ \t\n]*references[\ \t\n]*(.*)\((.*)\)/i", ($linha), $valores)) {
						$cmp[$valores[1]]['tabela_fk'] = $valores[2];
						$cmp[$valores[1]]['campo_fk'] = $valores[3];
						continue;
					}
					if (preg_match("/^([\ \t\n]*[aA-zZ0-9]+)([\ \t\n]*[aA-zZ0-9]+)[\ \t\n]*(\([0-9]+\)|)[\ \t\n]*(primary\ key|)([\ \t\n]*autoincrement|)[\ \t\n]*(null|not\ null)(,|)$/i", ($linha), $valores)) {
						$valores[3] = str_replace('(', '', $valores[3]);
						$valores[3] = str_replace(')', '', $valores[3]);
						$valores = array_map('trim', $valores);
						$cmp[$valores[1]]['esquema'] = '';
						$cmp[$valores[1]]['tabela'] = $nomeTabela;
						$cmp[$valores[1]]['campo'] = $valores[1];
						$cmp[$valores[1]]['obrigatorio'] = $valores[6] == 'null' ? null : $valores[6];
						$cmp[$valores[1]]['tipo'] = $valores[2];
						$cmp[$valores[1]]['tipo_de_dado'] = $tipos[$valores[2]];
						$cmp[$valores[1]]['tamanho'] = $valores[3];
						$cmp[$valores[1]]['descricao'] = null;
						$cmp[$valores[1]]['campo_pk'] = $valores[4];
						$cmp[$valores[1]]['esquema_fk'] = null;
						$cmp[$valores[1]]['unique_key'] = null;
						$cmp[$valores[1]]['constraint'] = 'undefined';
						$cmp[$valores[1]]['tabela_fk'] = isset($cmp[$valores[1]]['tabela_fk']) ? $cmp[$valores[1]]['tabela_fk'] : null;
						$cmp[$valores[1]]['campo_fk'] = isset($cmp[$valores[1]]['campo_fk']) ? $cmp[$valores[1]]['campo_fk'] : null;
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
	/**
	 * Nome do schema utilizado na persistente
	 * @param boolean $ponto setando para true já vem com o ponto para ligar na tabela
	 * @return string
	 */
	public function pegarNomeSchema($ponto = true) {
		return '';
	}


	/**
	 * Retorna o nome da tabela utilizada pela persistente
	 * @param boolean verificador se retorna com o nome do schema
	 * @return string Nome da tabela
	 */
	public function pegarNomeTabela($comSchema = true) {
		$estrutura = $this->pegarEstrutura();
		$n = explode('.',$estrutura['nomeTabela']);
		return strtolower(array_pop($n));
	}

	/**
	 * Retorna o nome da sequencia de banco utilizada pela persistente
	 * @return string Nome da Sequencia
	 */
	public function pegarNomeSequencia($comSchema = true) {
		$estrutura = $this->pegarEstrutura();
		$n = explode('.',$estrutura['nomeSequencia']);
		return strtolower(array_pop($n));
	}

}

?>