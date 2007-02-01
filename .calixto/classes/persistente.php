<?php
/**
* Classe de representa��o de uma camada de persistencia com Banco de Dados
* @package Infra-estrutura
* @subpackage Persistente
*/
abstract class persistente extends objeto{
	/**
	* @var [array] array com a estrutura dos objetos persistentes
	* criado para a execu��o de cache
	*/
	private static $estrutura;
	/**
	* @var [conexao] objeto de conex�o com o banco de dados
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conex�o com o banco de dados
	* @param [string] (opcional) nome do arquivo de configura��o da persistente
	*/
	public function __construct($conexao = null,$arquivoXML = null){
		try{
			if(is_subclass_of($conexao,'conexao')){
				$this->conexao = $conexao;
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* M�todo de sobrecarga para printar a classe
	* @return [string] texto de sa�da da classe
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
	public function mapearPersistente($arquivoXML){
		try{
			switch(true){
				case !($arquivoXML):
				break;
				case !(is_file($arquivoXML)):
					throw new erroInclusao("Arquivo [$arquivoXML] inexistente!");
				break;
				case !(is_readable($arquivoXML)):
					throw new erroInclusao("Arquivo [$arquivoXML] sem permiss�o de leitura!");
				break;
				default:
					$xml = simplexml_load_file($arquivoXML);
					$estrutura['nomeTabela'] = caracteres($xml['nomeBanco']);
					foreach($xml->propriedades->propriedade as $campo){
						if(isset($campo['indicePrimario']) && strtolower(caracteres($campo['indicePrimario'])) == 'sim'){ 
							$estrutura['chavePrimaria'] = caracteres($campo->banco['nome']);
						}
						$estrutura['campo'][caracteres($campo->banco['nome'])]['tipo'] = caracteres($campo['tipo']);
						$estrutura['campo'][caracteres($campo->banco['nome'])]['tamanho'] = caracteres($campo['tamanho']);
						$estrutura['campo'][caracteres($campo->banco['nome'])]['obrigatorio'] = (strtolower(caracteres($campo['obrigatorio'])) == 'sim') ? 'sim' : 'nao';
						if(isset($campo->banco->chaveEstrangeira)){
							$estrutura['campo'][caracteres($campo->banco['nome'])]['chaveEstrangeira']['tabela'] = caracteres($campo->banco->chaveEstrangeira['tabela']);
							$estrutura['campo'][caracteres($campo->banco['nome'])]['chaveEstrangeira']['campo'] = caracteres($campo->banco->chaveEstrangeira['campo']);
						}
						if(isset($campo->dominio->opcao)){
							foreach($campo->dominio->opcao as $opcao){
								$estrutura['campo'][caracteres($campo->banco['nome'])]['valoresPossiveis'][] = caracteres($opcao['id']);
							}
						}
						if(isset($campo->banco['ordem'])) $estrutura['ordem'][caracteres($campo->banco['ordem'])] = caracteres($campo->banco['nome']);
					}
				break;
			}
			if(isset($estrutura['ordem'])) ksort($estrutura['ordem']);
			return $estrutura;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* M�todo que retorna a estrutura da persitente
	* @return [vetor] estrutura da persitente
	*/
	public function pegarEstrutura($arquivoXML = null){
		try{
			if(!isset(persistente::$estrutura[get_class($this)])){
				return persistente::$estrutura[get_class($this)] = $this->mapearPersistente(definicaoArquivo::pegarXmlEntidade($this,$arquivoXML));
			}else{
				return persistente::$estrutura[get_class($this)];
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	+ Retorna a conex�o com o banco de dados, caso n�o exista, cria uma conex�o
	* @return [conexao] conexao com o banco de dados .
	*/
	public final function pegarConexao(){
		try{
			if(is_subclass_of($this->conexao,'conexao')){
				return  $this->conexao;
			}else{
				return conexao::criar();
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Caso o recurso de conex�o n�o tenha sido passado para a persistente fecha a conex�o.
	* @param [conexao] conex�o com o banco de dados
	* @return [booleano] se a conex�o foi fechada (true) se n�o.(false)
	*/
	public final function fecharConexao(conexao $conexao){
		try{
			if($this->conexao !== $conexao){
				$conexao->fechar();
				return true;
			}
			return false;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa um comando SQL no banco de dados.(necessita de controle de transa��o)
	* @param [string] comando SQL para a execu��o
	* @return [int] n�mero de linhas afetadas
	*/
	public function executarComando($comando = null){
		try{
			$conexao = $this->pegarConexao();
			$retorno = $conexao->executarComando($comando);
			if($this->fecharConexao($conexao)){
				$erro = new erroPersistente('Utiliza��o incorreta da persistente! Poss�velmente voc� efetuou uma chamada do m�todo '.get_class($this).'::executarComando() sem controle de conex�o!');
				$erro->comando = $comando;
				throw $erro;
			}
			return $retorno;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o mapeamento de tipo de dados do banco
	* @return [array] mapeamento
	*/
	public abstract function mapeamento();
	
	//**************************************************************************
	//**************************************************************************
	// 							COMANDOS DML
	//**************************************************************************
	//**************************************************************************
	/**
	* Retorna o registro corrente na conex�o com o banco.(necessita de controle de transa��o)
	* @return [array] registro corrente
	*/
	public function pegarRegistro(){
		try{
			if(!is_subclass_of($this->conexao,'conexao')) throw new erro;
			return $this->conexao->pegarRegistro();
		}
		catch(erro $e){
			$erro = new erroPersistente('Utiliza��o incorreta da persistente! Poss�velmente voc� efetuou uma chamada do m�todo '.get_class($this).'::pegarRegistro sem controle de conex�o!');
			throw $erro;
		}
	}
	/**
	* Retorna a sele��o de registros da conex�o com o banco
	* @param [string] comando SQL para a execu��o
	* @return [array] sele��o de registros
	*/
	public function pegarSelecao($comando = null){
		try{
			$conexao = $this->pegarConexao();
			$conexao->executarComando($comando);
			$retorno = $conexao->pegarSelecao();
			$this->fecharConexao($conexao);
			return $retorno;
		}
		catch(erro $e){
			throw $e;
		}
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
	* @return [array] sele��o de registros
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
	* Gera o comando SQL de leitura dos registros pesquisados
	* @return [string] comando SQL de leitura de um registro
	*/
	public function gerarComandoPesquisar($filtro){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "select * from {$estrutura['nomeTabela']} where ";
			$tamanhoComando = strlen($comando);
			foreach($filtro as $campo => $valor){
				switch(true){
					case($estrutura['campo'][$campo]['tipo'] == 'numero'):
						$comando.= ($valor) ? " {$campo} = {$valor} and ":"";
					break;
					case($estrutura['campo'][$campo]['tipo'] == 'texto'):
						$comando.= ($valor) ? " upper({$campo}) like upper('%$valor%') and ":"";
					break;
					default:
						$comando.= ($valor) ? " {$campo} = '{$valor}' and ":"";
					break;
				}
			}
			if($tamanhoComando != strlen($comando)){
				$comando = substr($comando,0,-4);
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
	* @return [array] sele��o de registros
	*/
	public function pesquisar($filtro, pagina $pagina){
		try{
			if(is_subclass_of($filtro, 'filtro')){
				trigger_error( 'Para se utilizar um "filtro" deve-se especializar o m�todo "pesquisar" da persistente ['.get_class($this).']');
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
	* @return [array] sele��o de registros
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
	* Executa o comando de leitura dos registros com pagina��o
	* @param [pagina] pagina referente
	* @param [string] comando sql para execu��o
	* @return [array] sele��o de registros
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
	* @param [string] comando sql para execu��o
	* @return [string] comando SQL de leitura
	*/
	public function gerarComandoLerPaginado(pagina $pagina, $sql){
		try{
			return "select * from ({$sql}) selecao limit ".($pagina->pegarTamanhoPagina())." offset ".($pagina->pegarLinhaInicial() - 1);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando SQL de leitura de um registro
	* @param [string] chave �nica de identifica��o do registro
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
	* @param [string] chave �nica de identifica��o do registro
	* @return [array] sele��o de registro
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
	* Gera o comando de inser��o de um registro no banco de dados
	* @param [array] correlativa entre campos e valores do registro
	* @return [string] comando de inser��o
	*/
	public function gerarComandoInserir($array){
		try{
			$estrutura = $this->pegarEstrutura();
			$campos = implode(',',array_keys($array));
			foreach($array as $campo => $valor){
				switch(true){
					case(empty($valor) && ($campo == $estrutura['chavePrimaria'])):
						$valores[] = $this->gerarSequencia();
					break;
					case(empty($valor)):
						$valores[] = "null";
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
			$conexao = $this->pegarConexao();
			$conexao->executarComando($this->gerarComandoInserir($array));
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de exclus�o de um registro no banco de dados
	* @param [string] chave prim�ria do registro 
	* @return [string] o comando de exclus�o de um registro no banco de dados
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
	* @param [string] chave prim�ria do registro
	*/
	public function excluir($valorChave){
		try{
			$conexao = $this->pegarConexao();
			$conexao->executarComando($this->gerarComandoExcluir($valorChave));
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de altera��o de um registro no banco de dados
	* @param [array] correlativa entre campos e valores do registro
	* @param [string] chave prim�ria do registro 
	* @return [string] comando de altera��o
	*/
	public function gerarComandoAlterar($array,$valorChave){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando =  "update {$estrutura['nomeTabela']} set \n";
			foreach($array as $campo => $valor){
				if (empty($valor)) {
					$comando .= "{$campo} = null,\n";
				}else{
					$valor = str_replace("'","''",$valor);
					$comando .= "{$campo} = '{$valor}',\n";
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
	* @param [string] chave prim�ria do registro
	*/
	public function alterar($array, $valorChave){
		try{
			$conexao = $this->pegarConexao();
			$conexao->executarComando($this->gerarComandoAlterar($array,$valorChave));
			$this->fecharConexao($conexao);
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
	* Monta o comando de cria��o da sequence no banco de dados 
	* @return [string] comando de cria��o
	*/
	public function gerarComandoCriacaoSequence(){
		try{
			$estrutura = $this->pegarEstrutura();
			return "create sequence sq_{$estrutura['nomeTabela']}";
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
			if($comandoCriacaoSequence = $this->gerarComandoCriacaoSequence()){
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoCriacaoSequence);
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de criacao no banco de dados
	* @return [string] comando de cria��o
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
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoCriacaoTabela);
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o comando de cria��o da chave primaria da tabela 
	* @return [string] comando de cria��o
	*/
	public function gerarComandoCriacaoChavePrimaria(){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "";
			if($estrutura['chavePrimaria']){
				$comando .= "alter table only {$estrutura['nomeTabela']} \n
				add constraint pk_{$estrutura['nomeTabela']} primary key ({$estrutura['chavePrimaria']})";
			}
			return $comando;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Cria a chave prim�ria da tabela no banco de dados
	*/
	public function criarChavePrimaria(){
		try{
			if($comandoCriacaoChavePrimaria = $this->gerarComandoCriacaoChavePrimaria()){
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoCriacaoChavePrimaria);
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Monta o comando de cria��o das chaves estrangeiras no banco de dados 
	* @return [string] comando de cria��o
	*/
	public function gerarComandoCriacaoChavesEstrangeiras(){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "";
			foreach($estrutura['campo'] as $nomeCampo => $referencia){
				if(isset($referencia['chaveEstrangeira'])) 
					$comando .= "alter table only {$estrutura['nomeTabela']} \n	
					add constraint {$estrutura['nomeTabela']}_{$nomeCampo}_fk foreign key ($nomeCampo) references {$referencia['chaveEstrangeira']['tabela']}({$referencia['chaveEstrangeira']['campo']});";
			}
			return $comando;
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
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoCriacaoChavesEstrangeiras);
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* @return [string] comando de cria��o de restri��o de dom�nios no banco de dados 
	*/
	public function gerarComandoRestricao(){
		try{
			$estrutura = $this->pegarEstrutura();
			$comando = "";
			// Cria��o de CHECKS
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
	* Cria as restri��es de dom�nio da tabela no banco de dados
	*/
	public function criarRestricoes(){
		try{
			if($comandoRestricao = $this->gerarComandoRestricao()){
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoRestricao);
				$this->fecharConexao($conexao);
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
	* Gera o comando de destrui��o no banco de dados
	* @return [string] comando de destrui��o
	*/
	public function gerarComandoDestruicaoSequence(){
		try{
			$estrutura = $this->pegarEstrutura();
			return "drop sequence sq_{$estrutura['nomeTabela']}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de destrui��o da sequence no banco de dados
	*/
	public function destruirSequence(){
		try{
			if($comandoDestruicaoSequence = $this->gerarComandoDestruicaoSequence()){
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoDestruicaoSequence);
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de destrui��o no banco de dados
	* @return [string] comando de destrui��o
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
	* Executa o comando de destrui��o da tabela no banco de dados
	*/
	public function destruirTabela(){
		try{
			if($comandoDestruicaoTabela = $this->gerarComandoDestruicaoTabela()){
				$conexao = $this->pegarConexao();
				$conexao->executarComando($comandoDestruicaoTabela );
				$this->fecharConexao($conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de destrui��o no banco de dados
	*/
	public function destruir(){
		try{
			$this->destruirSequence();
			$this->destruirTabela();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de criacao no banco de dados
	*/
	public function recriar(){
		try{
			$this->ler(null);
			$this->destruir();
			$this->criar();
		}
		catch(erro $e){
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
	* Retorna o comando de cria��o da entidade no banco de dados ;
	*/
	public function comandoCriacaoCompleto(){
		try{
			$comando = '';
			if($comandoCriacaoSequence = $this->gerarComandoCriacaoSequence()){
				$comando = "-- Comando de cria��o da sequence\n";
				$comando.= 	"{$comandoCriacaoSequence};\n";
			}
			if($comandoCriacaoTabela = $this->gerarComandoCriacaoTabela()){
				$comando.= "-- Comando de cria��o da tabela\n";
				$comando.= 	"{$comandoCriacaoTabela};\n";
			}
			if($comandoCriacaoChavePrimaria = $this->gerarComandoCriacaoChavePrimaria()){
				$comando.= "\n-- Comando de cria��o da chave prim�ria\n";
				$comando.= "{$comandoCriacaoChavePrimaria};\n";
			}
			if($comandoCriacaoChavesEstrangeiras = $this->gerarComandoCriacaoChavesEstrangeiras()){
				$comando.= "\n-- Comando de cria��o das chaves estrangeiras\n";
				$comando.= 	"{$comandoCriacaoChavesEstrangeiras};\n";
			}
			if($comandoRestricao = $this->gerarComandoRestricao()){
				$comando.= "\n-- Comando de cria��o das restri��es\n";
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
