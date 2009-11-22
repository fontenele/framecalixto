<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputDataHora extends VInput{
    /**
     * Componente de data
     * @var VInputData
     */
	public $data;
    /**
     * Componente de hora
     * @var VInputHora
     */
	public $hora;
    /**
     * Método construtor
     * @param string $nome
     * @param TData $valor
     */
	public function __construct($nome = 'naoInformado',TData $valor){
		$this->data = new VInputData($nome."[data]",$valor);
        $this->data->passarId("{$nome}_data");
		$this->hora = new VInputHora($nome."[hora]",$valor);
        $this->hora->passarId("{$nome}_hora");
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	public function __call($metodo, $parametros){
		$this->data->__call($metodo,$parametros);
		$this->hora->__call($metodo,$parametros);
	}
	public function passarSize(){}
    /**
     * Método de serialização em string html do componente
     * @return string
     */
	public function __toString(){
        if($this->obrigatorio){
            return $this->data->__toString().'&nbsp'.$this->hora->__toString().$this->campoObrigatorio();
        }else{
            return $this->data->__toString().'&nbsp'.$this->hora->__toString();
        }
	}
}
?>
