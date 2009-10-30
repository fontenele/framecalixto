<?php
class operador extends objeto{
    const restricaoE = 'and';
    const restricaoOU = 'or ';
    const maiorQue = '>';
    const maiorOuIgual = '>=';
    const menorQue = '<';
    const menorOuIgual = '<=';
    const naoENulo = 'não Nulo';
    const eNulo = '';
    const igual = '=';
    const diferente = '<>';
    const como = '%texto%';
    const iniciandoComo = 'texto%';
    const finalizandoComo = '%texto';
    const dominio = '1 ou 2 ou 3...';
    const entre = 'valores entre {valor1} e {valor2}';
    const generico = '%aáãàä%';

    public $operador;
    public $valor;
	public $restricao = operador::restricaoE;
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function generico($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::generico);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function entre($valor1,$valor2,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::entre);
		$operador->passarRestricao($restricao);
        $operador->passarValor(array('valor1'=>$valor1,'valor2'=>$valor2));
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function dominio($valor,$restricao = null){
		if(!is_array($valor)) {
			throw new Exception('Não foi passado um array para o operador de domínio');
		}
        $operador = new operador();
        $operador->passarOperador(operador::dominio);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function finalizandoComo($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::finalizandoComo);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function iniciandoComo($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::iniciandoComo);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function como($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::como);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function diferente($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::diferente);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function igual($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::igual);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param string $restricao
     * @return operador
     */
    public static function naoENulo($restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::naoENulo);
		$operador->passarRestricao($restricao);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param string $restricao
     * @return operador
     */
    public static function eNulo($restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::eNulo);
		$operador->passarRestricao($restricao);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function menorOuIgual($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::menorOuIgual);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function menorQue($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::menorQue);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function maiorOuIgual($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::maiorOuIgual);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @param string $restricao
     * @return operador
     */
    public static function maiorQue($valor,$restricao = null){
        $operador = new operador();
        $operador->passarOperador(operador::maiorQue);
		$operador->passarRestricao($restricao);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de visualização como string
     * @return string
     */
    public function __toString(){
        return "{$this->valor}";
    }
}
?>