<?php
/**
* Classe de persistъncia
* @package Sistema
* @subpackage funcionalidade
*/
class PFuncionalidade extends persistentePadraoMySql{
	/**
	* @var [array] array com a estrutura dos objetos persistentes
	* criado para a execuчуo de cache
	*/
	private static $estrutura;
	/**
	* Mщtodo que retorna a estrutura da persitente
	* sobrescrito para a execuчуo de cache
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