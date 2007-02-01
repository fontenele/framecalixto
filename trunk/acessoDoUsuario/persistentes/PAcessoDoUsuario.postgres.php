<?php
/**
* Classe de persistъncia
* @package Sistema
* @subpackage acessoDoUsuario
*/
class PAcessoDoUsuario extends persistentePadraoPG{
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
		if(!is_array(PAcessoDoUsuario::$estrutura)){
			return PAcessoDoUsuario::$estrutura = $this->mapearPersistente(definicaoArquivo::pegarXmlEntidade($this));
		}else{
			return PAcessoDoUsuario::$estrutura;
		}
	}
}
?>