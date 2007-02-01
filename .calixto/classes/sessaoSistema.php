<?php
/**
* Classe que faz o gerenciamento da sess�o do cliente no servidor
* Esta classe se responsabiliza pelos dados do cliente no servidor
* @package Infra-estrutura
* @subpackage utilit�rios
*/
class sessaoSistema extends objeto{
	/**
	* Inicia a sessao com o servidor
	*/
	function iniciar(){
		session_start();
	}
	/**
	* Encerra a sess�o do sistema com o servidor
	*/
	function encerrar(){
		unset($_SESSION[definicaoSistema::pegarNome()]);
	}
	/**
	* Limpar valores do sistema
	*/
	function limpar(){
		$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'] = null;
	}
	/**
	* Registra valor por sistema
	* @param [st] Nome da v�riavel
	* @param [st] Valor da v�riavel
	*/
	function registrar($variavel, $valor){
		if(!is_object($valor)){
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel] = $valor;
		}else{
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]['tipoObjeto'] = get_class($valor);
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]['objeto'] = serialize($valor);
		}
	}
	/**
	* Retira o valor por sistema
	* @param [st] Nome da v�riavel
	*/
	function retirar($variavel){
		if ($this->tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
			unset($_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]);
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				return unserialize($valor['objeto']);
			}
			return $valor;
		}
		throw(new erro('Variavel inexistente na Sessao do Sistema !'));
	}
	/**
	* Retorna o valor por sistema
	* @param [st] Nome da v�riavel
	*/
	function pegar($variavel){
		if (sessaoSistema::tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				return unserialize($valor['objeto']);
			}
			return $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
		}
		throw(new erro('Variavel inexistente na Sessao do Sistema !'));
	}
	/**
	* Retorna um booleano da verifica��o de existencia
	* @param [st] Nome da v�riavel
	*/
	function tem($variavel){
		return isset($_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]);
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
