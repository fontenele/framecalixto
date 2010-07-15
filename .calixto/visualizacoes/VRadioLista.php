<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VRadioLista extends VComponente {
	public $valor;
	public $colunas;
	public $nome;
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('div',$nome, null);
		$this->passarClass('radiolist');
		$this->nome = $nome;
		$this->valor = $valor;
	}
	/**
	* Método de passagem do conteúdo
	* @param string conteudo do compoenente
	*/
	public function passarConteudo($conteudo){
		if(is_array($conteudo)){
			$this->conteudo = array();
			foreach ($conteudo as $index => $valor) {
				$this->conteudo[$index]['radio'] = new VRadio($this->nome,$index);
				$this->conteudo[$index]['radio']->passarId("id_{$this->nome}_{$index}");
				$this->conteudo[$index]['label'] = new VEtiquetaHtml('label');
				$this->conteudo[$index]['label']->passarConteudo($valor);
				$this->conteudo[$index]['label']->passarId("id_{$this->nome}_{$index}");
			}
		}else{
			$this->conteudo = $conteudo;
		}
	}
	/**
	 * Método de configuração do componente
	 */
	function configurar(){
		if(is_array($this->conteudo)){
			if(!$this->colunas){
				$conteudo = '';
				foreach($this->conteudo as $indice => $texto){
					if($indice == $this->valor){
						$this->conteudo[$indice]['radio']->passarChecked(true);
						$conteudo .= "{$this->conteudo[$indice]['radio']} {$this->conteudo[$indice]['label']} ";
					}else{
						$conteudo .= "{$this->conteudo[$indice]['radio']} {$this->conteudo[$indice]['label']} ";
					}
				}
			}else{
				
                $conteudo = '';
				$i = 0;
				foreach($this->conteudo as $indice => $texto){
					if($indice == $this->valor){
						$this->conteudo[$indice]['radio']->passarChecked(true);
						$conteudo.= "<div>{$this->conteudo[$indice]['radio']} {$this->conteudo[$indice]['label']} </div>";
					}else{
						$conteudo.= "<div>{$this->conteudo[$indice]['radio']} {$this->conteudo[$indice]['label']} </div>";
					}
					if(++$i >= $this->colunas){
						$i = 0;
					}
				}
			}
			$this->conteudo = $conteudo;
		}
	}
	function passarValores($valores){
		$this->passarConteudo($valores);
	}
	function passarColunas($colunas){
		$this->colunas = $colunas;
	}
}
?>
