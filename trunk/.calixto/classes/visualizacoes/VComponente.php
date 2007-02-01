<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VComponente extends VEtiquetaHtml{
	function __construct($etiqueta = 'naoInformada',$nome = 'naoInformado',$valor = null){
		parent::__construct($etiqueta);
		$this->passarTabindex(1);
		$this->passarName($nome);
		if($valor) $this->passarValue($valor);
	}
	final static function montar($componente,$nome,$valor,$opcoes = null,$valores = null){
		try{
			$objeto = null;
			switch(strtolower($componente)){
				case 'envio':
				case 'enviar':
					$objeto = new VButtonSubmit($nome,$valor);
					$objeto->passarConteudo($valor);
				break;
				case 'botão':
				case 'botao':
					$objeto = new VButton($nome,$valor);
					$objeto->passarConteudo($valor);
				break;
				case 'palavra chave':
				case 'senha':
					$objeto = new VPassword($nome,$valor);
				break;
				case 'menu de sistema':
					$objeto = new VMenu($valores,'menu1','9999');
				break;
				case 'menu de modulo':
					$objeto = new VMenu($valores,'menu2','9998');
				break;
				case 'menu de programa':
					$objeto = new VMenu($valores,'menu3','9997');
				break;
				case 'seletor':
				case 'radio':
					$objeto = new VRadio($nome,$valor);
				break;
				case 'marcador':
				case 'check':
				case 'checkbox':
					$objeto = new VCheck($nome,$valor);
				break;
				case 'input':
				case 'entrada':
				case 'caixa de entrada':
					$objeto = new VInput($nome,$valor);
				break;
				case 'oculto':
					$objeto = new VHidden($nome,$valor);
				break;
				case 'caixa de combinacao':
				case 'caixa de combinação':
				case 'combobox':
				case 'combo':
				case 'select':
					if(is_array($valores)) {
						$objeto = new VSelect($nome,$valor);
						$objeto->passarValores($valores);
					}
				break;
				case 'caixa de selecao':
				case 'caixa de seleção':
					if(is_array($valores)) {
						$objeto = new VSelect($nome,$valor);
						$objeto->passarMultiple(true);
						$objeto->passarValores($valores);
					}
				break;
				default:
					$objeto = new $componente($nome,$valor);
				break;
			}
			if(is_array($opcoes)){
				foreach($opcoes as $propriedade => $valor){
					$propriedade = 'passar'.$propriedade;
					$objeto->$propriedade($valor);
				}
			}
			return $objeto;
		}catch(erro $e){
			x(func_get_args());
			throw $e;
		}
	}
}
?>
