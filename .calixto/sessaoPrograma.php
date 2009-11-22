<?php
/**
* Classe que faz o gerenciamento da sessão do cliente no servidor
* Esta classe se responsabiliza pelos dados do cliente no servidor
* @package FrameCalixto
* @subpackage utilitários
*/
class sessaoPrograma extends objeto{
	/**
	* Nome do programa que serão registrados os valores por programa
	*/
	private $programa;
	/**
	* Método construtor
	* @param string nome do programa
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
	* @param string Nome da váriavel
	* @param string Valor da váriavel
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
	* @param string Nome da váriavel
	* @return mixed valor
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
		throw(new erroSessao(sprintf('Variavel [%s] inexistente na Sessao do Programa !',$variavel)));
	}
	/**
	* Retorna valor por programa
	* @param string Nome da váriavel
	* @return mixed valor
	*/
	function pegar($variavel){
		if ($this->tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel];
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				$objeto = unserialize($valor['objeto']);
				if(method_exists($objeto,'conectar')) $objeto->conectar();
				return $objeto;
			}
			return $valor;
		}
		throw(new erroSessao(sprintf('Variavel [%s] inexistente na Sessao do Programa !',$variavel)));
	}
	/**
	* Retorna um booleano da verificação de existencia
	* @param string Nome da váriavel
	* @return boolean caso exista a variavel retornará verdadeiro
	*/
	function tem($variavel){
		return isset($_SESSION[definicaoSistema::pegarNome()]['variaveisDePrograma'][$this->programa][$variavel]);
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return string texto de saída da classe
	*/
	public function __toString(){
		debug2($_SESSION);
		return '';
	}
}
?>