<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VComponenteIntervalo
 *
 * @author calixto
 */
class VComponenteIntervalo {

	public $vComponente1;
	public $vComponente2;
	
	public function __call($metodo, $parametros){
		$args = '';
		foreach($parametros as $index =>$parametro){
			$args .="\$parametros[{$index}],";
		}
		$args = substr($args,0,-1);
		eval("\$a = \$this->vComponente1->{$metodo}({$args});");
		eval("\$b = \$this->vComponente2->{$metodo}({$args});");
		return $a;
		return array($a,$b);
	}
	/**
	 * Factory de componentes padronizados
	 *
	 * @param string Tipo do componente
	 * @param string Nome do componente (name)
	 * @param string Valor do componente (value)
	 * @param array Opções de modificação do componente
	 * @param array Valores para componentes multiplos
	 * @return unknown
	 */
	final static function montar($componente,$nome,$valor,$opcoes = null,$valores = null){
		$valorIni = isset($valor['ini']) ? $valor['ini'] : $valor;
		$valorFim = isset($valor['fim']) ? $valor['fim'] : $valor;
		$vComponenteIntervalo = new VComponenteIntervalo();
		$vComponenteIntervalo->vComponente1 = VComponente::montar($componente, $nome.'[ini]', $valorIni, $opcoes, $valores);
		$vComponenteIntervalo->vComponente2 = VComponente::montar($componente, $nome.'[fim]', $valorFim, $opcoes, $valores);
		return $vComponenteIntervalo;
	}
	public function __toString(){
		return "{$this->vComponente1} até {$this->vComponente2}";
	}
}

?>
