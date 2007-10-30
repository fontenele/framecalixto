<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VRadioLista extends VEtiquetaHtml{
	public $valor;
	public $colunas;
	public $nome;
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('div',$nome, null);
		$this->passarClass('radiolist');
		$this->nome = $nome;
		$this->valor = $valor;
	}
	function configurar(){
		if(is_array($this->conteudo)){
			if(!$this->colunas){
				foreach($this->conteudo as $indice => $texto){
					if($indice == $this->valor){
						$conteudo .= "<input type='radio' name='{$this->nome}' value='{$indice}' id='id_{$this->nome}_{$indice}' checked='true' /><label for='id_{$this->nome}_{$indice}' >{$texto}</label>";
					}else{
						$conteudo .= "<input type='radio' name='{$this->nome}' value='{$indice}' id='id_{$this->nome}_{$indice}' /><label for='id_{$this->nome}_{$indice}' >{$texto}</label>";
					}
				}
			}else{
				$conteudo.='<table summary="text" >';
				$i = 0;
				foreach($this->conteudo as $indice => $texto){
					if($i == 0) $conteudo.='<tr>';
					if($indice == $this->valor){
						$conteudo .= "<td><input type='radio' name='{$this->nome}' value='{$indice}' id='id_{$this->nome}_{$indice}' checked='true' /><label for='id_{$this->nome}_{$indice}' >{$texto}</label></td>";
					}else{
						$conteudo .= "<td><input type='radio' name='{$this->nome}' value='{$indice}' id='id_{$this->nome}_{$indice}' /><label for='id_{$this->nome}_{$indice}' >{$texto}</label></td>";
					}
					if(++$i >= $this->colunas){
						$conteudo.='</tr>';
						$i = 0;
					}
				}
				$conteudo.='</table>';
			}
			$this->conteudo = $conteudo;
		}
	}
	function passarValores($valores){
		$this->conteudo = $valores;
	}
	function passarColunas($colunas){
		$this->colunas = $colunas;
	}
}
?>