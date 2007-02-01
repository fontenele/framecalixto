<?php
/**
* Classe que faz o gerenciamento da sess�o do cliente no servidor
* Esta classe se responsabiliza pelos dados do cliente no servidor
* @package Infra-estrutura
* @subpackage utilit�rios
*/
class sessaoPrograma extends objeto{
	/**
	* Nome do programa que ser�o registrados os valores por programa
	*/
	private $programa;
	/**
	* M�todo construtor
	* @param [st] nome do programa
	*/
	function __construct($programa = 'programaIndefinido'){
		$this->programa = $programa;
	}
	/**
	* Limpar valores do programa
	*/
	function limpar(){
		$_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa] = null;
	}
	/**
	* Registra valor por programa
	* @param [st] Nome da v�riavel
	* @param [st] Valor da v�riavel
	*/
	function registrar($variavel, $valor){
		if(!is_object($valor)){
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel] = $valor;
		}else{
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel]['tipoObjeto'] = get_class($valor);
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel]['objeto'] = serialize($valor);
		}
	}
	/**
	* Retira o valor por programa
	* @param [st] Nome da v�riavel
	* @return [mixed] valor
	*/
	function retirar($variavel){
		if ($this->tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel];
			unset($_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel]);
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				return unserialize($valor['objeto']);
			}
			return $valor;
		}
		throw(new erro(sprintf('Variavel [%s] inexistente na Sessao do Sistema !',$variavel)));
	}
	/**
	* Retorna valor por programa
	* @param [st] Nome da v�riavel
	* @return [mixed] valor
	*/
	function pegar($variavel){
		if ($this->tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel];
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				return unserialize($valor['objeto']);
			}
			return $valor;
		}
		throw(new erro(sprintf('Variavel [%s] inexistente na Sessao do Sistema !',$variavel)));
	}
	/**
	* Retorna um booleano da verifica��o de existencia
	* @param [st] Nome da v�riavel
	* @return [bo] caso exista a variavel retornar� verdadeiro
	*/
	function tem($variavel){
		return isset($_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel]);
	}
	/**
	* M�todo de sobrecarga para printar a classe
	* @return [string] texto de sa�da da classe
	*/
	public function __toString(){
		debug2($_SESSION);
		return '';
	}
}
?>
