<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VInputDataHora extends VInput{
	public $data;
	public $hora;
	public function __construct($nome = 'naoInformado',TData $valor){
		$this->data = new VInputData($nome."[data]",$valor);
		$this->hora = new VInputHora($nome."[hora]",$valor);
	}
	public function __toString(){
		return $this->data->__toString().$this->hora->__toString();
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	public function __call($metodo, $parametros){
		try{
			throw new erro();
		}
		catch(erro $e){
			$debug = debug_backtrace();
			echo 'Chamada de método inexistente !!!';
			$arRetorno['No Arquivo'] = $debug[1]['file'];
			$arRetorno['Na Linha'] = $debug[1]['line'];
			$arRetorno['Na Chamada'] = $debug[1]['function'];
			$arRetorno['Da Classe'] = $debug[1]['class'];
			$arRetorno['Argumentos'] = $debug[1]['args'];
			x($arRetorno);
			throw $e;
		}
	}
}
?>
