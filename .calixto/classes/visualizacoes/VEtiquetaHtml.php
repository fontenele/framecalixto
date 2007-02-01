<?php
/**
* Objeto de apresenta��o de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class VEtiquetaHtml extends objeto{
	/**
	* @var [string] Nome que abre e fecha uma etiqueta
	*/
	public $etiqueta;
	/**
	* @var [string] Identifica se a etiqueta precisa ser fechada
	*/
	public $fechada = true;
	/**
	* @var [string] Armazena as propriedades da etiqueta
	*/
	public $propriedades;
	/**
	* @var [string] Armazena o conte�do da etiqueta
	*/
	public $conteudo;
	/**
	* M�todo contrutor
	* @param [string] nome da etiqueta html
	* @param [booleano] fechamento da etiqueta
	*/
	public function __construct($etiqueta){
		$this->etiqueta = $etiqueta;
	}
	/**
	* M�todo de sobrecarga para evitar a cria��o de m�todos repetitivos
	* @param [string] metodo chamado
	* @param [array] par�metros parassados para o m�todo chamado
	*/
	public function __call($metodo, $parametros){
		if (preg_match('/(pegar|passar|adicionar|remover)(.*)/', $metodo, $resultado)) {
			$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
			switch($resultado[1]){
				case 'pegar':
					return $this->propriedades[strtolower($resultado[2])];
				break;
				case 'passar':
					$this->propriedades[strtolower($resultado[2])] = $parametros[0];
				break;
				case 'adicionar':
					$this->propriedades[strtolower($resultado[2])][] = $parametros[0];
				break;
				case 'remover':
					array_pop($this->propriedades[strtolower($resultado[2])]);
				break;
			}
		}
    }
	/**
	* M�todo de passagem do conte�do
	* @param [string] conteudo do compoenente
	*/
	public function passarConteudo($conteudo){
		$this->conteudo = $conteudo;
	}
	/**
	* M�todo de retorno do conte�do
	* @return [string] conteudo do componente
	*/
	public function pegarConteudo(){
		return $this->conteudo;
	}
	/**
	* M�todo de configura��o antes da impress�o da etiqueta
	*/
	public function configurar(){}
	/**
	* M�todo de sobrecarga para printar a classe
	* @return [string] texto de sa�da da classe
	*/
	public function __toString(){
		$this->configurar();
		$stEtiqueta = "<{$this->etiqueta}";
		foreach($this->propriedades as $propriedade => $valor){
			if(!is_array($valor)){
				$stEtiqueta .= " {$propriedade}='{$valor}' ";
			}else{
				$valor = implode(';',$valor);
				$stEtiqueta .= " {$propriedade}=\"javascript:{$valor};\" ";
			}
		}
		if($this->fechada){
			$stEtiqueta .= ">{$this->conteudo}</{$this->etiqueta}>";
		}else{
			$stEtiqueta .= "/>{$this->conteudo}";
		}
		return $stEtiqueta;
	}
}
?>
