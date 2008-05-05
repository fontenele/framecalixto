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
	protected $colecoes = array();
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
	public function juntar(Negocio $negocio){
		//TODO verificar como criar a clausula "on" do join
		$this->colecoes[get_class($negocio)]= new colecaoPadraoNegocio(null,$this->conexao);
		$this->negocios[] = array('negocio'=>$negocio);
	}
	/**
	* Cria a sql para a execução
	* @param Negocio $negocio
	*/
	public function sql(){
		$sql = "select * from \n";
		$negocios = $this->negocios;
		$negocioAnt = array_shift($negocios);
		foreach ($this->negocios as $negocio) {
			$sql .= "\t{$negocio['negocio']->pegarPersistente()->pegarNomeTabela()} inner join \n";
		}
		return $sql;
	}
}
?>