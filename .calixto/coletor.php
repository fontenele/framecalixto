<?php
/**
* Representação de um coletor de dados persistentes
* Esta classe coleta dados persistentes do banco de dados e retorna-os em coleções de dados
* @package FrameCalixto
* @subpackage utilitários
*/
class coletor extends objeto {
	protected $pagina = null;
	/**
	* objeto de conexão com o banco de dados
	* @var conexao
	*/
	protected $conexao;
	/**
	* Negócios existentes no coletor
	* @var array com os negócios existentes
	*/
	protected $negocios = array();
	protected $joins = array();
	protected $tabelas = array();
	protected $chavesExtrangeiras = array();
	protected $persistentes = array();
	/**
	* Negócios existentes no coletor
	* @var array com os negócios existentes
	*/
	public $colecoes;
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public function __construct(conexao $conexao = null){
		try{
			if($conexao){
				$this->conexao = $conexao;
			}else{
				$this->conexao = conexao::criar();
			}
			$this->colecoes = new colecao();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Unifica aos objetos selecionados
	* @param negocio $negocio
	* @param pagina $pagina
	*/
	public function coletar(Negocio $negocio, pagina $pagina = null){
		$this->join($negocio);
		if($pagina) $this->pagina = $pagina;
	}
	public function innerJoin(Negocio $negocio){
		$this->join($negocio,'inner join');
	}
	public function leftJoin(Negocio $negocio){
		$this->join($negocio,'left join');
	}
	public function rightJoin(Negocio $negocio){
		$this->join($negocio,'right join');
	}
	protected function join(Negocio $negocio, $join = 'inner join'){
		$stNegocio = get_class($negocio);
		$this->colecoes->passar($stNegocio,new colecaoPadraoNegocio(null,$this->conexao));
		$this->negocios[$stNegocio] = $negocio;
		$this->persistentes[$stNegocio] = $negocio->pegarPersistente();
		$this->tabelas[$stNegocio] = $this->persistentes[$stNegocio]->pegarNomeTabela();
		$this->joins[get_class($negocio)] = count($this->joins) ? $join : '';
		$this->chavesExtrangeiras[$this->tabelas[$stNegocio]] = $this->persistentes[$stNegocio]->gerarRelacoesDeChavesEstrangeiras();
	}
	protected function select(){
		return 'select * ';
	}
	protected function from(){
		$from = 'from ';
		$tabelas = array();
		foreach($this->tabelas as $classe => $tabela){
			$on = array();
			foreach($tabelas as $tabelaAnterior){
				if(isset($this->chavesExtrangeiras[$tabelaAnterior][$tabela])){
					$on[] = $this->chavesExtrangeiras[$tabelaAnterior][$tabela];
				}
				if(isset($this->chavesExtrangeiras[$tabela][$tabelaAnterior])){
					$on[] = $this->chavesExtrangeiras[$tabela][$tabelaAnterior];
				}
			}
			$join = count($on) ? ' on ('.implode(' and ',$on).')' : '';
			$from.="\t {$this->joins[$classe]} {$tabela} {$join} \n";
			$tabelas[] = $tabela;
		}
		return $from;
	}
	protected function where(){
		$stFiltro = '';
		foreach ($this->negocios as $classe => $negocio) {
			$f = $this->persistentes[$classe]->gerarClausulaDeFiltro($negocio->pegarFiltroParaPersistente(),false);
			$stFiltro .= trim($f) ? "\n\t{$f} and": '';
		}
		$stFiltro = substr($stFiltro,0,-3);
		return ($stFiltro ? "\nwhere ".$stFiltro : $stFiltro);
	}
	/**
	* Retorna as coleções do coletor
	* @param string nome do objeto de negócio
	*/
	public function pegar($nome){
		return $this->colecoes->pegar($nome);
	}
	/**
	* Cria a sql para a execução
	* @param negocio $negocio
	*/
	public function sql(){
		$sql = $this->select().$this->from().$this->where();
//		x($sql);
		return $sql;
	}
	/**
	 * Executa um comando SQL no banco de dados.(necessita de controle de transação)
	 * @param pagina $pagina
	 * @return integer número de linhas afetadas
	 * @throws erro
	 */
	public function executar(pagina $pagina = null){
		try{
			if(!count($this->negocios)) throw new erro('Não foram passados objetos de negócio para o coletor.');
			if(!$this->pagina) $this->pagina = new pagina(0);
			if($pagina) $this->pagina = $pagina;
			$this->lerPaginado($this->pagina, $this->sql());
			$negocios = array_keys($this->colecoes->itens);
			while ($registro = $this->conexao->pegarRegistro()) {
				foreach ($negocios as $nmNegocio) {
					$negocio = new $nmNegocio($this->conexao);
					$negocio->pegarPersistente()->formatarRegistro($negocio->pegarPersistente()->pegarEstrutura(),$registro);
					$negocio->vetorPraNegocio($registro);
					$id = $negocio->valorChave();
					$this->colecoes->$nmNegocio->passar($id,$negocio);
				}
			}
			return;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura dos registros com paginação
	* @param pagina pagina referente
	* @param string comando sql para execução
	* @return array seleção de registros
	*/
	public function lerPaginado(pagina $pagina, $sql){
		try{
			if(persistente::imprimindoComandos()){
				x("select count(*) as quantidade from ({$sql}) selecao");
				x("{$sql}");
			}
			$this->conexao->executarComando("select count(*) as quantidade from ({$sql}) selecao");
			$total = $this->conexao->pegarRegistro();
			if(isset($total['quantidade'])){
				$pagina->passarTamanhoGeral((integer) $total['quantidade']);
			}
			return $this->conexao->executarComando($this->gerarComandoLerPaginado($pagina, $sql));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Gera o comando de leitura paginada
	* @param pagina pagina referente
	* @param string comando sql para execução
	* @return string comando SQL de leitura
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
}
?>