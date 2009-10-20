<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
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
	* @var [string] Armazena o conteúdo da etiqueta
	*/
	public $conteudo;
	/**
	* Método contrutor
	* @param [string] nome da etiqueta html
	* @param [booleano] fechamento da etiqueta
	*/
	public function __construct($etiqueta){
		$this->etiqueta = $etiqueta;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	public function __call($metodo, $parametros){
		if (preg_match('/(pegar|passar|adicionar|remover)(.*)/', $metodo, $resultado)) {
			$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
			switch($resultado[1]){
				case 'pegar':
					return isset($this->propriedades[strtolower($resultado[2])]) ? $this->propriedades[strtolower($resultado[2])] : null;
				break;
				case 'passar':
					$this->propriedades[strtolower($resultado[2])] = (isset($parametros[0])) ? $parametros[0] : null;
				break;
				case 'adicionar':
					$this->propriedades[strtolower($resultado[2])][] = (isset($parametros[0])) ? $parametros[0] : null;
				break;
				case 'remover':
					unset($this->propriedades[strtolower($resultado[2])]);
				break;
			}
		}
    }
	/**
	* Método de passagem do conteúdo
	* @param [string] conteudo do compoenente
	*/
	public function passarConteudo($conteudo){
		$this->conteudo = $conteudo;
	}
	/**
	* Método de retorno do conteúdo
	* @return [string] conteudo do componente
	*/
	public function pegarConteudo(){
		return $this->conteudo;
	}
	/**
	* Método de configuração antes da impressão da etiqueta
	*/
	public function configurar(){}
	/**
	 * Método de adição de classe na etiqueta
	 * @param string $classe 
	 */
	public function adicionarClass($classe){
		$this->passarClass(trim($this->pegarClass().' '.$classe));
	}
	/**
	 * Método de remoção de classe na etiqueta
	 * @param string $classe
	 */
	public function removerClass($classe){
		if($this->pegarClass() == $classe){
			unset($this->propriedades['class']);
			return;
		}
		$this->passarClass(preg_replace(array("/\s+({$classe})\s+/","/^({$classe})\s+/","/\s+({$classe})$/"),' ',$this->pegarClass()));
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	public function __toString(){
		$this->configurar();
		$stEtiqueta = "<{$this->etiqueta}";
		if(is_array($this->propriedades)){
			foreach($this->propriedades as $propriedade => $valor){
				if(!is_array($valor)){
					$valor = str_replace("\"","&quot;",$valor);
					$stEtiqueta .= " {$propriedade}=\"{$valor}\" ";
				}else{
					$valor = implode(';',$valor);
					$stEtiqueta .= " {$propriedade}=\"javascript:{$valor};\" ";
				}
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