<?php
/**
 *
 */
class colecaoPadraoFiltro extends colecaoPadraoObjeto{

	/**
	* Método de envio de valor pelo indice da colecao
	* @param string Indice da coleção
	* @param mixed Item da coleção
	*/
	public function passar($indice,$item){
		if (!($item instanceof operador))
			throw new InvalidArgumentException('Não foi passado um operador para '.get_class($this).'!');
		parent::__set($indice, $item);
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		if (!($parametros instanceof operador))
			throw new InvalidArgumentException('Não foi passado um operador para '.get_class($this).'!');
		$this->itens[][$variavel] = $parametros;
    }
}
?>
