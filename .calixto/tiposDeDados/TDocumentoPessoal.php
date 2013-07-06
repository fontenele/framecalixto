<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de CPF
* @package FrameCalixto
* @subpackage tipoDeDados
*/
abstract class TDocumentoPessoal extends objeto{
	/**
	* @var integer
	*/
	protected $numero = 0;
	/**
	* metodo construtor do documento pessoal
	* @param string numero formatado
	*/
	public function __construct($numero = ''){
		$this->numero = preg_replace('/[^0-9]/','',$numero);
	}
    /**
    * Método de codificação para XML
    * @return string XML
    */
    public function xml(){
        return (string) $this;
    }
}
?>