<?php
/**
* Classe que faz o gerenciamento da sessão do cliente no servidor
* Esta classe se responsabiliza pelos dados do cliente no servidor
* @package Infra-estrutura
* @subpackage utilitários
*/
class sessaoSistema extends objeto{
	/**
	* Inicia a sessao com o servidor
	*/
	static function iniciar(){
		session_start();
	}
	/**
	* Encerra a sessão do sistema com o servidor
	*/
	static function encerrar(){
		unset($_SESSION[definicaoSistema::pegarNome()]);
	}
	/**
	* Limpar valores do sistema
	*/
	static function limpar(){
		$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'] = null;
	}
	/**
	* Registra valor por sistema
	* @param [st] Nome da váriavel
	* @param [st] Valor da váriavel
	*/
	static function registrar($variavel, $valor){
		if(!is_object($valor)){
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel] = $valor;
		}else{
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]['tipoObjeto'] = get_class($valor);
			$_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]['objeto'] = serialize($valor);
		}
	}
	/**
	* Retira o valor por sistema
	* @param [st] Nome da váriavel
	*/
	static function retirar($variavel){
		if (sessaoSistema::tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
			unset($_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]);
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				return unserialize($valor['objeto']);
			}
			return $valor;
		}
		throw(new erroSessao(sprintf('Variavel [%s] inexistente na Sessao do Sistema !',$variavel)));
	}
	/**
	* Retorna o valor por sistema
	* @param [st] Nome da váriavel
	*/
	static function pegar($variavel){
		if (sessaoSistema::tem($variavel)){
			$valor = $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
			if(is_array($valor) && isset($valor['tipoObjeto']) && isset($valor['objeto'])){
				__autoload($valor['tipoObjeto']);
				$objeto = unserialize($valor['objeto']);
				if(method_exists($objeto,'conectar')) $objeto->conectar();
				return $objeto;
			}
			return $_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel];
		}
		throw(new erroSessao(sprintf('Variavel [%s] inexistente na Sessao do Sistema !',$variavel)));
	}
	/**
	* Retorna um booleano da verificação de existencia
	* @param [st] Nome da váriavel
	*/
	static function tem($variavel){
		return isset($_SESSION[definicaoSistema::pegarNome()]['variaveisDeSistema'][$variavel]);
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	public function __toString(){
		debug2($_SESSION);
		return '';
	}
}
?>