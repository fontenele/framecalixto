<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VCheckLista extends VEtiquetaHtml {
	public $valor;
	public $titulo;
	public $listagem;
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('fieldset');
		$this->passarId($nome);
		$this->valor = $valor;
	}
	function configurar(){
		$nome = $this->pegarId();
		$conteudo = '';
		if(is_array($this->conteudo)){
			$conteudo .= $this->titulo;
			foreach($this->conteudo as $indice => $texto){
				$check = $this->montarCheck($nome,$indice);
				$label = new VEtiquetaHtml('label');
				$label->passarConteudo($check.'&nbsp;'.$texto.$this->listagem);
				$conteudo .= $label;
			}
			$this->conteudo = $conteudo;
		}
	}
	function passarTitulo($legend){
		$this->titulo = new VEtiquetaHtml('legend');
		$this->titulo->passarConteudo($legend);
	}
	function tipoListagem($listagem = true){
		$this->listagem = $listagem ? '<br/>':'';
	}
	function passarValores($valores){
		$this->conteudo = $valores;
	}
	protected function montarCheck($nome,$indice){
		if(is_array($this->valor)){
			if(in_array($indice,$this->valor)){
				return VComponente::montar('checkbox',"{$nome}[{$indice}]",$indice,array('checked'=>'true'));
			}else{
				return VComponente::montar('checkbox',"{$nome}[{$indice}]",$indice);
			}
		}else{
			if($indice == $this->valor){
				return VComponente::montar('checkbox',"{$nome}[{$indice}]",$indice,array('checked'=>'true'));
			}else{
				return VComponente::montar('checkbox',"{$nome}[{$indice}]",$indice);
			}
		}
	}
}
?>