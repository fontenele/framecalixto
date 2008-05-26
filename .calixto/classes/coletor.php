<?php
/**
* Representação de um coletor de dados persistentes
* Esta classe coleta dados persistentes do banco de dados e retorna-os em coleções de dados
*/
class coletor extends objeto {
	/**
	* objeto de conexão com o banco de dados
	* @var [conexao]
	*/
	protected $conexao;
	/**
	* Negócios existentes no coletor
	* @var [vetor] com os negócios existentes
	*/
	protected $negocios = array();
	/**
	* Negócios existentes no coletor
	* @var [vetor] com os negócios existentes
	*/
	public $colecoes;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
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
	* @param Negocio $negocio1
	* @param Negocio $negocio2
	*/
	public function coletar(Negocio $negocio){
		//TODO verificar como criar a clausula "on" do join
		$this->colecoes->passar(get_class($negocio),new colecaoPadraoNegocio(null,$this->conexao));
		$this->negocios[get_class($negocio)] = $negocio;
	}
	/**
	* Cria a sql para a execução
	* @param Negocio $negocio
	*/
	public function sql(){
		$negocios = $this->negocios;
		$sql = "select * from ";
		$stFiltro = '';
		$tabelas = array();
		foreach ($negocios as $classe => $negocio) {
			$p = $negocio->pegarPersistente();
			$tabelas[$classe] = $p->pegarNomeTabela();
			$joins[$classe] = $p->gerarRelacoesDeChavesEstrangeiras();
			$sql .= "\n\t{$tabelas[$classe]},";
			$f = $p->gerarClausulaDeFiltro($negocio->negocioPraVetor(),false);
			$stFiltro .= ($f) ? "\n\t{$f} and": '';
		}
		$sql = substr($sql,0,-1);
		foreach ($joins as $join) {
			if($join){
				foreach ($join as $tabela => $strJoin) {
					if(in_array($tabela,$tabelas))
						$stFiltro .= "\n\t{$strJoin} and";
				}
			}
		}
		$stFiltro = substr($stFiltro,0,-3);
		return $sql.($stFiltro ? "\nwhere ".$stFiltro : $stFiltro);
	}
	/**
	* Executa um comando SQL no banco de dados.(necessita de controle de transação)
	* @return [int] número de linhas afetadas
	*/
	public function executar(){
		try{
			if(!count($this->negocios)) throw new erro('Não foram passados objetos de negócio para o coletor.');
			$return = $this->conexao->executarComando($this->sql());
			$negocios = array_keys($this->colecoes->itens);
			while ($registro = $this->conexao->pegarRegistro()) {
				foreach ($negocios as $nmNegocio) {
					$negocio = new $nmNegocio($this->conexao);
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
}
?>