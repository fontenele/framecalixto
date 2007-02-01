<?php
include_once('externas/Smarty-2.6.13/libs/Smarty.class.php');
/**
* Classe respons�vel por passar a intelig�ncia do controle para uma tela
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class visualizacao extends Smarty{
	/**
	* 
	*/
	public $_cache_include_info;
	/**
	* M�todo Contrutor
	*/
	function __construct(){
		parent::Smarty();
		$this->compile_check = true;
		$this->debugging = false;
		$this->left_delimiter  = '�';
		$this->right_delimiter = '�';
		$this->template_dir = '';
		$this->compile_dir = definicaoPasta::pegarTemporaria();
		$this->config_dir = '';
	}
	/**
	* Retorna o texto da pagina
	* @param [texto] caminho da pagina
	* @return [texto]
	*/
	function pegar($pagina){
		return $this->fetch($pagina);
	}
	/**
	* Mostra o conteudo da pagina
	* @param [texto] caminho da pagina
	*/
	function mostrar($pagina){
		$this->display($pagina);
	}
	/**
	* M�todo de sobrecarga para evitar a cria��o de m�todos repetitivos
	* @param [string] metodo chamado
	* @param [array] par�metros parassados para o m�todo chamado
	*/
	function __set($variavel, $parametros){
		$this->assign($variavel,$parametros);
    }
}
?>
