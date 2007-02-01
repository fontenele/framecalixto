<?php
/**
* Classe de persist�ncia
* @package Sistema
* @subpackage funcionalidade
*/
class PFuncionalidade extends persistentePadraoMySql{
	/**
	* @var [array] array com a estrutura dos objetos persistentes
	* criado para a execu��o de cache
	*/
	private static $estrutura;
	/**
	* M�todo que retorna a estrutura da persitente
	* sobrescrito para a execu��o de cache
	* @return [vetor] estrutura da persitente
	*/
	public function pegarEstrutura(){
		if(!is_array(PFuncionalidade::$estrutura)){
			return PFuncionalidade::$estrutura = $this->mapearPersistente(definicaoArquivo::pegarXmlEntidade($this));
		}else{
			return PFuncionalidade::$estrutura;
		}
	}
}
?>