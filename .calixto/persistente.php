<?php
/**
* Classe de representação de uma camada de persistencia com Banco de Dados
* @package FrameCalixto
* @subpackage Persistente
*/
abstract class persistente extends objeto{
	/**
	* @var [array] array com a estrutura dos objetos persistentes
	* criado para a execução de cache
	*/
	private static $estrutura;
	/**
	* @var [conexao] objeto de conexão com o banco de dados
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	* @param [string] (opcional) nome do arquivo de configuração da persistente
	*/
	public function __construct(conexao $conexao,$arquivoXML = null){
		try{
			$this->conexao = $conexao;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	public function __toString(){
		try{
			debug2($this);
			echo '<pre>';
			echo $this->comandoCriacaoCompleto();
			echo '</pre>';
			debug2($this->pegarEstrutura());
			return '';
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Metodo criado para especificar a estrutura da persistente
	* @param [st] caminho do arquivo
	*/
	public function pegarEstrutura($arquivoXML = null){
		try{
			if(!isset(persistente::$estrutura[get_class($this)])){
				$arquivoXML = definicaoArquivo::pegarXmlEntidade($this,$arquivoXML);
				switch(true){
					case !($arquivoXML):
					break;
					case !(is_file($arquivoXML)):
						throw new erroInclusao("Arquivo [$arquivoXML] inexistente!");
					break;
					case !(is_readable($arquivoXML)):
						throw new erroInclusao("Arquivo [$arquivoXML] sem permissão de leitura!");
					break;
					default:
						$xml = simplexml_load_file($arquivoXML);
						$estrutura['nomeTabela'] = strval($xml['nomeBanco']);
						$estrutura['nomeSequencia'] = strval($xml['nomeSequencia']) ? strval($xml['nomeSequencia']) : "sq_{$estrutura['nomeTabela']}";
						foreach($xml->propriedades->propriedade as $campo){
							$nomeCampo = strtolower(strval($campo->banco['nome']));
							if(isset($campo['indicePrimario']) && strtolower(strval($campo['indicePrimario'])) == 'sim'){
								$estrutura['chavePrimaria'] = $nomeCampo;
							}
							$estrutura['campo'][$nomeCampo]['nome'] = $nomeCampo;
							$estrutura['campo'][$nomeCampo]['tipo'] = strtolower(strval($campo['tipo']));
							$estrutura['campo'][$nomeCampo]['tamanho'] = strval($campo['tamanho']);
							$estrutura['campo'][$nomeCampo]['obrigatorio'] = (strtolower(strval($campo['obrigatorio'])) == 'sim') ? 'sim' : 'nao';
							if($estrutura['campo'][$nomeCampo]['tipo'] == 'texto'){
								$estrutura['campo'][$nomeCampo]['operadorDeBusca'] = isset($campo->banco['operadorDeBusca']) ? strtolower(strval($campo->banco['operadorDeBusca'])): 'como';
							}else{
								$estrutura['campo'][$nomeCampo]['operadorDeBusca'] = isset($campo->banco['operadorDeBusca']) ? strtolower(strval($campo->banco['operadorDeBusca'])): 'igual';
							}
							if(isset($campo->banco->chaveEstrangeira)){
								$estrutura['campo'][$nomeCampo]['chaveEstrangeira']['tabela'] = strval($campo->banco->chaveEstrangeira['tabela']);
								$estrutura['campo'][$nomeCampo]['chaveEstrangeira']['campo'] = strval($campo->banco->chaveEstrangeira['campo']);
							}
							if(isset($campo->dominio->opcao)){
								foreach($campo->dominio->opcao as $opcao){
									$estrutura['campo'][$nomeCampo]['valoresPossiveis'][] = strval($opcao['id']);
								}
							}
							if(isset($campo->banco['ordem'])){
								if(isset($campo->banco['tipoOrdem']) && $campo->banco['tipoOrdem'] == 'inversa'){
									$estrutura['ordem'][strval($campo->banco['ordem'])] = $nomeCampo.' desc';
								}else{
									$estrutura['ordem'][strval($campo->banco['ordem'])] = $nomeCampo;
								}
							}
						}
					break;
				}
				if(isset($estrutura['ordem'])) ksort($estrutura['ordem']);
				persistente::$estrutura[get_class($this)] = $estrutura;
			}
			return persistente::$estrutura[get_class($this)];
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa um comando SQL no banco de dados.(necessita de controle de transação)
	* @param [string] comando SQL para a execução
	* @return [int] número de linhas afetadas
	*/
	public function executarComando($comando = null){
		try{
			return $this->conexao->executarComando($comando);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o mapeamento de tipo de dados do banco
	* @return array mapeamento
	*/
	public abstract function mapeamento();

	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DML
	//**************************************************************************
	//**************************************************************************
	/**
	* Método de conversão de tipo de dado
	* @param mixed dado a ser convertido
	* @param array campo referente
	*/
	public function converterDado($valor,$campo = null){
		if($campo){
			switch(strtolower($campo['tipo'])){
				case 'data':
					return new TData($valor);
				break;
				case 'tnumerico':
					return new TNumerico((float) $valor);
				break;
				case 'tmoeda':
					return new TMoeda((float) $valor);
				break;
				case 'ttelefone':
					return new TTelefone($valor);
				break;
				case 'tcep':
					return new TCep($valor);
				break;
				case 'tdocumentopessoal':
					return new TDocumentoPessoal($valor);
				break;
				default:
					return $valor;
			}
		}else{
			switch(true){
				case($valor instanceof TNumerico):
					return $valor->pegarNumero();
				break;
				case(is_object($valor)):
					return $valor->__toString();
				break;
				default:
					return $valor;
			}
		}
	}
	/**
	* Retorna o registro corrente na conexão com o banco.(necessita de controle de transação)
	* @return array registro corrente
	*/
	public function pegarRegistro(){
		try{
			if(!is_subclass_of($this->conexao,'conexao')) throw new erroPersistente('Utilização incorreta da persistente! Possívelmente você efetuou uma chamada do método '.get_class($this).'::pegarRegistro sem controle de conexão!');
			$tupla = $this->conexao->pegarRegistro();
			$estrutura = $this->pegarEstrutura();
			foreach($estrutura['campo'] as $campo => $atributos){
				if(isset($tupla[$campo])) {
					$tupla[$campo] = $this->converterDado($tupla[$campo],$atributos);
				}
			}
			return $tupla;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna a seleção de registros da conexão com o banco
	* @param string $comando SQL para a execução
	* @return array seleção de registros
	*/
	public function pegarSelecao($comando = null){
		try{
			$this->conexao->executarComando($comando);
			while ($arTupla = $this->pegarRegistro()) {
				$recordSet[] = $arTupla;
			}
			$retorno = isset($recordSet)? $recordSet : false ;
			return $retorno;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o nome da tabela utilizada pela persistente
	* @return string Nome da tabela
	*/
	public function pegarNomeTabela(){
		$estrutura = $this->pegarEstrutura();
		return strtolower($estrutura['nomeTabela']);
	}
	/**
	* Gera o comando SQL de leitura de todos os registros
	* @return [string] comando SQL de leitura de um registro
	*/
	public function gerarComandoLerTodos(){
		try{
			$estrutura = $this->pegarEstrutura();
			if(isset($estrutura['ordem'])){
					$ordem = ' order by '.implode(',',$estrutura['ordem']);
				}else{
					$ordem = '';
				}
			return "select * from {$estrutura['nomeTabela']}".$ordem;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura de todos os registros
	* @return array seleção de registros
	*/
	public function lerTodos(){
		try{
			return $this->pegarSelecao($this->gerarComandoLerTodos());
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera a cláusula de filtro de leitura
	* @param [array] $filtro
	* @param [boolean] $nomeDaClausula
	* @return [string]
	*/
	public function gerarClausulaDeFiltro($filtro, $nomeDaClausula = true){
		$estrutura = $this->pegarEstrutura();
		$comando = '';
		foreach($filtro as $campo => $valor){
			$valor = $this->converterDado($valor);
			if(!$valor) continue;
			switch(strtolower($estrutura['campo'][$campo]['operadorDeBusca'])){
				case('diferente'):
					if($estrutura['campo'][$campo]['tipo'] == 'numero'){
						$operacao = " %s <> %s and ";
					}else{
						$operacao = " %s <> '%s' and ";
					}
				break;
				case('como'):
					if($estrutura['campo'][$campo]['tipo'] == 'numero'){
						$operacao = " upper(%s) like upper(%%%s%%) and ";
					}else{
						$operacao = " upper(%s) like upper('%%%s%%') and ";
					}
				break;
				case('generico'):
				case('generica'):
				case('genérico'):
				case('genérica'):
					if($estrutura['campo'][$campo]['tipo'] == 'numero'){
						$operacao = " upper(accent_remove(%s)) like upper(accent_remove(%%%s%%)) and ";
					}else{
						$operacao = " upper(accent_remove(%s)) like upper(accent_remove('%%%s%%')) and ";
					}
				break;
				case('igual'):
					if($estrutura['campo'][$campo]['tipo'] == 'numero'){
						$operacao = " %s = %s and ";
					}else{
						$operacao = " %s = '%s' and ";
					}
				break;
			}
			$comando.= sprintf($operacao,$campo,str_replace("'","''",$valor));
		}
		if($comando){
			$comando = substr($comando,0,-4);
			if($nomeDaClausula) $comando = ' where '.$comando;
		}
		return $comando;
	}
	/**
	* Gera o comando SQL de leitura dos registros pesquisados
	* @return [string] comando SQL de leitura de um registro
	*/
	public function gerarComandoPesquisar($filtro){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "select * from {$estrutura['nomeTabela']} ";
			$tamanhoComando = strlen($comando);
			$comando .= $this->gerarClausulaDeFiltro($filtro);
			if($tamanhoComando != strlen($comando)){
				if(isset($estrutura['ordem'])){
					$ordem = ' order by '.implode(',',$estrutura['ordem']);
				}else{
					$ordem = '';
				}
				return $comando.$ordem;
			}else{
				return $this->gerarComandoLerTodos();
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura dos registros pesquisados
	* @param [array] dados do filtro
	* @param [pagina] pagina referente
	* @return array seleção de registros
	*/
	public function pesquisar($filtro, pagina $pagina){
		try{
			if(is_subclass_of($filtro, 'filtro')){
				trigger_error( 'Para se utilizar um "filtro" deve-se especializar o método "pesquisar" da persistente ['.get_class($this).']');
			}
			return $this->lerPaginado($pagina, $this->gerarComandoPesquisar($filtro));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura de todos os registros
	* @param [pagina] pagina referente
	* @return array seleção de registros
	*/
	public function lerTodosPaginado(pagina $pagina){
		try{
			return $this->lerPaginado($pagina, $this->gerarComandoLerTodos());
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura dos registros com paginação
	* @param [pagina] pagina referente
	* @param [string] comando sql para execução
	* @return array seleção de registros
	*/
	public function lerPaginado(pagina $pagina, $sql){
		try{
			$total = $this->pegarSelecao("select count(*) as quantidade from ({$sql}) selecao");
			if(isset($total[0]['quantidade'])){
				$pagina->passarTamanhoGeral((integer) $total[0]['quantidade']);
			}
			return $this->pegarSelecao($this->gerarComandoLerPaginado($pagina, $sql));
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
			if($pagina->pegarTamanhoPagina() == 0){
				return $sql;
			}else{
				return "select * from ({$sql}) selecao limit ".($pagina->pegarTamanhoPagina())." offset ".($pagina->pegarLinhaInicial() - 1);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando SQL de leitura de um registro
	* @param [string] chave única de identificação do registro
	* @return [string] comando SQL de leitura de um registro
	*/
	public function gerarComandoLer($chave){
		try{
			$estrutura = $this->pegarEstrutura();
			if($chave) return "select * from {$estrutura['nomeTabela']} where {$estrutura['chavePrimaria']} = '$chave'";
			return "select * from {$estrutura['nomeTabela']} where {$estrutura['chavePrimaria']} is null";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura de um registro
	* @param [string] chave única de identificação do registro
	* @return array seleção de registro
	*/
	public function ler($valorChave){
		try{
			$arRetorno = $this->pegarSelecao($this->gerarComandoLer($valorChave));
			if(isset($arRetorno[0])) return $arRetorno[0];
		}
		catch(erro $e){
			throw $e;
		}
	}
	abstract function gerarSequencia();
	/**
	* Gera o comando de inserção de um registro no banco de dados
	* @param [array] correlativa entre campos e valores do registro
	* @return [string] comando de inserção
	*/
	public function gerarComandoInserir($array){
		try{
			$estrutura = $this->pegarEstrutura();
			$campos = implode(',',array_keys($array));
			$valores = '';
			foreach($array as $campo => $valor){
				switch(true){
					case(empty($valor) && ($campo == $estrutura['chavePrimaria'])):
						$valores[] = $this->gerarSequencia();
					break;
					case(empty($valor)):
						$valores[] = "null";
					break;
					case(is_object($valor)):
						$valor = $this->converterDado($valor);
						$valores[] = (empty($valor)) ? "null" : "'$valor'";
					break;
					default:
						$valores[] = "'".str_replace("'","''",$valor)."'";
				}
			}
			$valores = implode(',',$valores);
			return "insert into {$estrutura['nomeTabela']} ($campos) values ($valores);\n";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Insere um registro no banco
	* @param [array] correlativa entre campos e valores do registro
	*/
	public function inserir($array){
		try{
			$this->conexao->executarComando($this->gerarComandoInserir($array));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de exclusão de um registro no banco de dados
	* @param [string] chave primária do registro
	* @return [string] o comando de exclusão de um registro no banco de dados
	*/
	public function gerarComandoExcluir($valorChave){
		try{
			$estrutura = $this->pegarEstrutura();
			return "delete from {$estrutura['nomeTabela']} where {$estrutura['chavePrimaria']} = '{$valorChave}';\n";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Exclui um registro no banco
	* @param [string] chave primária do registro
	*/
	public function excluir($valorChave){
		try{
			$this->conexao->executarComando($this->gerarComandoExcluir($valorChave));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de alteração de um registro no banco de dados
	* @param [array] correlativa entre campos e valores do registro
	* @param [string] chave primária do registro
	* @return [string] comando de alteração
	*/
	public function gerarComandoAlterar($array,$valorChave){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando =  "update {$estrutura['nomeTabela']} set \n";
			foreach($array as $campo => $valor){
				if (empty($valor)) {
					$comando .= "{$campo} = null,\n";
				}else{
					if(is_object($valor)){
						$valor = $this->converterDado($valor);
						$comando .= empty($valor) ? "{$campo} = null,\n" : "{$campo} = '{$valor}',\n";
					}else{
						$valor = str_replace("'","''",$valor);
						$comando .= "{$campo} = '{$valor}',\n";
					}
				}
			}
			$comando = substr($comando,0,-2)."\n";
			$comando .= "where {$estrutura['chavePrimaria']} = '{$valorChave}';";
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Altera um registro no banco
	* @param [array] array de campos e valores
	* @param [string] chave primária do registro
	*/
	public function alterar($array, $valorChave){
		try{
			$this->conexao->executarComando($this->gerarComandoAlterar($array,$valorChave));
		}
		catch(erro $e){
			throw $e;
		}
	}
	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DDL
	//**************************************************************************
	//**************************************************************************
	/**
	* Monta o comando de criação da sequence no banco de dados
	* @return [string] comando de criação
	*/
	public function gerarComandoCriacaoSequence(){
		try{
			$estrutura = $this->pegarEstrutura();
			return "create sequence {$estrutura['nomeSequencia']}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria a sequence no banco de dados
	*/
	public function criarSequence(){
		try{
			$estrutura = $this->pegarEstrutura();
			if($comandoCriacaoSequence = $this->gerarComandoCriacaoSequence()){
				$this->conexao->executarComando($comandoCriacaoSequence);
			}
		}
		catch(erro $e){
			$this->conexao->executarComando("alter sequence {$estrutura['nomeSequencia']} restart 1;");
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de criacao no banco de dados
	* @return [string] comando de criação
	*/
	public function gerarComandoCriacaoTabela(){
		try{
			$estrutura = $this->pegarEstrutura();
			$mapeamento = $this->mapeamento();
			$comando = "create table {$estrutura['nomeTabela']} (\n";
			foreach($estrutura['campo'] as $nomeCampo => $campo){
				if($campo['tamanho']){
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]}({$campo['tamanho']}) {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
				}else{
					$comando .= "	$nomeCampo {$mapeamento[$campo['tipo']]} {$mapeamento['obrigatorio'][$campo['obrigatorio']]},\n";
				}
			}
			$comando = substr($comando,0,-2)."\n)";
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria a tabela no banco de dados
	*/
	public function criarTabela(){
		try{
			if($comandoCriacaoTabela = $this->gerarComandoCriacaoTabela()){
				$this->conexao->executarComando($comandoCriacaoTabela);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o comando de criação da chave primaria da tabela
	* @return [string] comando de criação
	*/
	public function gerarComandoCriacaoChavePrimaria(){
		try{
			$estrutura = $this->pegarEstrutura();
			$arNomeTable = explode('.',$estrutura['nomeTabela']);
			$nomeTabela = $arNomeTable[count($arNomeTable) -1];
			$comando = "";
			if($estrutura['chavePrimaria']){
				$comando .= "alter table only {$estrutura['nomeTabela']} \n
				add constraint {$nomeTabela}_pk primary key ({$estrutura['chavePrimaria']})";
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria a chave primária da tabela no banco de dados
	*/
	public function criarChavePrimaria(){
		try{
			if($comandoCriacaoChavePrimaria = $this->gerarComandoCriacaoChavePrimaria()){
				$this->conexao->executarComando($comandoCriacaoChavePrimaria);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o comando de criação das chaves estrangeiras no banco de dados
	* @return [string] comando de criação
	*/
	public function gerarComandoCriacaoChavesEstrangeiras(){
		try{
			$estrutura = $this->pegarEstrutura();
			$arNomeTable = explode('.',$estrutura['nomeTabela']);
			$nomeTabela = $arNomeTable[count($arNomeTable) -1];
			$comando = "";
			foreach($estrutura['campo'] as $nomeCampo => $referencia){
				if(isset($referencia['chaveEstrangeira'])){
					$comando .= "alter table only {$estrutura['nomeTabela']} \n
					add constraint {$nomeTabela}_{$nomeCampo}_fk foreign key ($nomeCampo) references {$referencia['chaveEstrangeira']['tabela']}({$referencia['chaveEstrangeira']['campo']});";
				}
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria um array contendo as relações da tabela com suas chaves extrangeiras
	* @return array Chaves extrangeiras
	*/
	public function gerarRelacoesDeChavesEstrangeiras(){
		try{
			$chaves = array();
			$estrutura = $this->pegarEstrutura();
			foreach($estrutura['campo'] as $nomeCampo => $referencia){
				if(isset($referencia['chaveEstrangeira']))
					$chaves[strtolower($referencia['chaveEstrangeira']['tabela'])] = " {$estrutura['nomeTabela']}.{$nomeCampo} = {$referencia['chaveEstrangeira']['tabela']}.{$referencia['chaveEstrangeira']['campo']} ";
			}
			return $chaves;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria as chaves estrangeiras da tabela no banco de dados
	*/
	public function criarChavesEstrangeiras(){
		try{
			if($comandoCriacaoChavesEstrangeiras = $this->gerarComandoCriacaoChavesEstrangeiras()){
				$this->conexao->executarComando($comandoCriacaoChavesEstrangeiras);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* @return [string] comando de criação de restrição de domínios no banco de dados
	*/
	public function gerarComandoRestricao(){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "";
			// Criação de CHECKS
			foreach($estrutura['campo'] as $nomeCampo => $campo){
				if(isset($campo['valores'])){
					$valores = null;
					foreach($campo['valores'] as $valor){
						$valores .="'$valor',";
					}
					$valores = substr($valores,0,-1);
					$comando .= "alter table only {$estrutura['nomeTabela']} \n
					add check ($nomeCampo in ($valores))";
				}
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria as restrições de domínio da tabela no banco de dados
	*/
	public function criarRestricoes(){
		try{
			if($comandoRestricao = $this->gerarComandoRestricao()){
				$this->conexao->executarComando($comandoRestricao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de criacao no banco de dados
	*/
	public function criar(){
		try{
			$this->criarSequence();
			$this->criarTabela();
			$this->criarChavePrimaria();
			$this->criarChavesEstrangeiras();
			$this->criarRestricoes();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de destruição no banco de dados
	* @return [string] comando de destruição
	*/
	public function gerarComandoDestruicaoSequence(){
		try{
			$estrutura = $this->pegarEstrutura();
			return "drop sequence {$estrutura['nomeSequencia']}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de destruição da sequence no banco de dados
	* @return [booleano] retorno de destruição da sequence
	*/
	public function destruirSequence(){
		try{
			if($comandoDestruicaoSequence = $this->gerarComandoDestruicaoSequence()){
				$this->conexao->executarComando($comandoDestruicaoSequence);
			}
			return true;
		}
		catch(erro $e){
			return true;
		}
	}
	/**
	* Gera o comando de destruição no banco de dados
	* @return [string] comando de destruição
	*/
	public function gerarComandoDestruicaoTabela(){
		try{
			$estrutura = $this->pegarEstrutura();
			return $comando = "drop table {$estrutura['nomeTabela']} cascade";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de destruição da tabela no banco de dados
	* @return [booleano] retorno de destruição da tabela
	*/
	public function destruirTabela(){
		try{
			$this->ler(null);
			if($comandoDestruicaoTabela = $this->gerarComandoDestruicaoTabela()){
				$this->conexao->executarComando($comandoDestruicaoTabela );
			}
			return true;
		}
		catch(erro $e){
			return true;
		}
	}
	/**
	* Executa o comando de destruição no banco de dados
	*/
	public function destruir(){
		try{
			$this->destruirSequence();
			$this->destruirTabela();
		}
		catch(erro $e){
			return true;
		}
	}
	/**
	* Executa o comando de criacao no banco de dados
	*/
	public function recriar(){
		try{
			$this->destruir();
			$this->criar();
		}
		catch(erro $e){
			$this->criar();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o comando de criação da entidade no banco de dados ;
	*/
	public function comandoDestruicaoCompleto(){
		try{
			$comando = '';
			if($comandoDestruicaoSequence = $this->gerarComandoDestruicaoSequence()){
				$comando = "-- Comando de destruição da sequence\n";
				$comando.= 	"{$comandoDestruicaoSequence};\n";
			}
			if($comandoDestruicaoTabela = $this->gerarComandoDestruicaoTabela()){
				$comando.= "-- Comando de destruição da tabela\n";
				$comando.= 	"{$comandoDestruicaoTabela};\n";
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o comando de criação da entidade no banco de dados ;
	*/
	public function comandoCriacaoCompleto(){
		try{
			$comando = '';
			if($comandoCriacaoSequence = $this->gerarComandoCriacaoSequence()){
				$comando = "-- Comando de criação da sequence\n";
				$comando.= 	"{$comandoCriacaoSequence};\n";
			}
			if($comandoCriacaoTabela = $this->gerarComandoCriacaoTabela()){
				$comando.= "-- Comando de criação da tabela\n";
				$comando.= 	"{$comandoCriacaoTabela};\n";
			}
			if($comandoCriacaoChavePrimaria = $this->gerarComandoCriacaoChavePrimaria()){
				$comando.= "\n-- Comando de criação da chave primária\n";
				$comando.= "{$comandoCriacaoChavePrimaria};\n";
			}
			if($comandoCriacaoChavesEstrangeiras = $this->gerarComandoCriacaoChavesEstrangeiras()){
				$comando.= "\n-- Comando de criação das chaves estrangeiras\n";
				$comando.= 	"{$comandoCriacaoChavesEstrangeiras};\n";
			}
			if($comandoRestricao = $this->gerarComandoRestricao()){
				$comando.= "\n-- Comando de criação das restrições\n";
				$comando.= 	"{$comandoRestricao};\n";
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>