<?php
/**
* Classe de defini��o para entidade
* @package Infra-estrutura
* @subpackage Defini��o
*/
class definicaoEntidade{
	/**
	* fun��o que define o nome da entidade utilizado por classes de persist�ncia, controle, negocio e visualiza��o
	* @param [string|objeto] para a defini��o da entidade da classe
	*/
	function entidade($classe){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			return (strtolower($arEntidade[0]{0}).substr($arEntidade[0],1,strlen($arEntidade[0])));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* fun��o que define o nome da persistente de uma entidade
	* @param [string|objeto] para a defini��o da entidade da classe
	* @return [string] nome da classe
	*/
	function persistente($classe){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			$classe = $arEntidade[0];
			return "P{$classe}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* fun��o que define o nome do negocio de uma entidade
	* @param [string|objeto] para a defini��o da entidade da classe
	* @return [string] nome da classe
	*/
	function negocio($classe){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			$classe = $arEntidade[0];
			return "N{$classe}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* fun��o que define o nome da internacionalizacao de uma entidade
	* @param [string|objeto] para a defini��o da entidade da classe
	* @return [string] nome da classe
	*/
	function internacionalizacao($classe){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			$classe = $arEntidade[0];
			return "I{$classe}";
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* fun��o que define o nome do controle de uma entidade
	* @param [string|objeto] para a defini��o da entidade da classe
	* @param [string] sufixo (funcionalidade) do controle
	* @return [string] nome da classe
	*/
	function controle($classe, $sufixo = null){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			$classe = $arEntidade[0];
			if($sufixo){
				return "C{$classe}_{$sufixo}";
			}else{
				return "C{$classe}";
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* fun��o que define a funcionalidade do controle de uma entidade
	* @param [string|objeto] para a defini��o da funcionalidade do controle
	* @return [string] nome da classe
	*/
	function funcionalidade($classe){
		try{
			if(is_object($classe)) $classe = get_class($classe);
			$arEntidade = explode('_',substr($classe,1,strlen($classe)));
			if(isset($arEntidade[1])){
				return $arEntidade[1];
			}else{
				return '';
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
