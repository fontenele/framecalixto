<?php
/**
* Classe que representa uma coleção de negócios
* Esta classe padroniza a forma de agrupamento de classes de negócio no sistema
* @package Infra-estrutura
* @subpackage utilitários
*/
class colecaoPadraoNegocio extends colecaoPadraoObjeto{
	/**
	* objeto de conexão com o banco de dados
	* @var [conexao]
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [vetor] (opcional) dados da colecao
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($array = null,conexao $conexao = null){
		parent::__construct($array);
		if($conexao){
			$this->conexao = $conexao;
		}else{
			$this->conexao = conexao::criar();
		}
	}
	/**
	* Método de captura de valor pelo indice da colecao
	* @param [string] Indice da coleção
	* @return [mixed] Item da coleção
	*/
	public function pegar($indice){
		return $this->itens[$indice];
	}
	/**
	* Método de envio de valor pelo indice da colecao
	* @param [string] Indice da coleção
	* @param [mixed] Item da coleção
	*/
	public function passar($indice,$item){
		if (!($item instanceof negocio))
			throw new InvalidArgumentException('Não foi passado um negocio para '.get_class($this).'!');
		parent::__set($indice, $item);
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		if (!($parametros instanceof negocio))
			throw new InvalidArgumentException('Não foi passado um negocio para '.get_class($this).'!');
		parent::__set($variavel, $parametros);
    }
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	protected function __call($metodo, $parametros){
		try{
			switch(true){
				case (preg_match('/(pegar|passar)(.*)/', $metodo, $resultado)) :
					$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
					if ($resultado[1] == 'passar') {
						$this->$var = $parametros[0];
						return;
					} else {
						return $this->$var;
					}
				break;
				default:
					foreach($this->itens as $indice => $objeto){
						$objeto->passarConexao($this->conexao);
						$argumentos = array();
						foreach($parametros as $arg => $parametro){
							$variavel = 'var_'.$arg;
							$$variavel = $parametro;
							$argumentos[] = $variavel;
						}
						$chamadaDeMetodoNoObjeto = '$objeto->'.$metodo.'('.implode(',',$argumentos).');';
						eval($chamadaDeMetodoNoObjeto);
					}
				break;
			}
		}
		catch(erro $e){
			throw $e;
		}
    }
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public final function conectar(conexao $conexao = null){
		try{
			switch(true){
				case($conexao):
					$this->passarConexao($conexao);
				break;
				case(is_resource($this->pegarConexao()->pegarConexao())):
				break;
				default:
					$this->passarConexao(conexao::criar());
			}
			foreach($this->itens as $indice => $negocio){
				$this->itens[$indice]->conectar($this->conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
    /**
    * Método de indexação de itens pelo identificador da classe de negócio
    */
    function indexarPorId(){
		try{
			$itens = array();
			foreach($this->itens as $negocio){
				$itens[$negocio->valorChave()] = $negocio;
			}
			$this->itens = $itens;
		}
		catch(erro $e){
			throw $e;
		}
    }
    /**
    * Método de geração de um vetor de um atributo do negócio
    * @param [string] primeiro item
    * @return [vetor] vetor com os valores do atributo dos negócios
    */
    function gerarVetorDescritivo($vazio = false){
		$arRetorno = array();
		if($vazio !== false) $arRetorno[''] = $vazio;
		foreach($this->itens as $indice => $negocio){
			$arRetorno[$negocio->valorChave()] = $negocio->valorDescricao();
		}
		return $arRetorno;
    }
}
?>