<?php
class operador extends objeto{
    public $operador;
    public $valor;

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

    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function entre($valor1,$valor2){
		if(!is_array($valor)) {
			throw new Exception('Não foi passado um array para o operador de domínio');
		}
        $operador = new operador();
        $operador->passarOperador(operador::entre);
        $operador->passarValor(array('valor1'=>$valor1,'valor2'=>$valor2));
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function dominio($valor){
		if(!is_array($valor)) {
			throw new Exception('Não foi passado um array para o operador de domínio');
		}
        $operador = new operador();
        $operador->passarOperador(operador::dominio);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function finalizandoComo($valor){
        $operador = new operador();
        $operador->passarOperador(operador::finalizandoComo);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function iniciandoComo($valor){
        $operador = new operador();
        $operador->passarOperador(operador::iniciandoComo);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function como($valor){
        $operador = new operador();
        $operador->passarOperador(operador::como);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function diferente($valor){
        $operador = new operador();
        $operador->passarOperador(operador::diferente);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function igual($valor){
        $operador = new operador();
        $operador->passarOperador(operador::igual);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @return operador
     */
    public static function naoENulo(){
        $operador = new operador();
        $operador->passarOperador(operador::naoENulo);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @return operador
     */
    public static function eNulo(){
        $operador = new operador();
        $operador->passarOperador(operador::eNulo);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function menorOuIgual($valor){
        $operador = new operador();
        $operador->passarOperador(operador::menorOuIgual);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function menorQue($valor){
        $operador = new operador();
        $operador->passarOperador(operador::menorQue);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function maiorOuIgual($valor){
        $operador = new operador();
        $operador->passarOperador(operador::maiorOuIgual);
        $operador->passarValor($valor);
        return $operador;
    }
    /**
     * Método de configuração de um operador
     * @param mixed $valor
     * @return operador
     */
    public static function maiorQue($valor){
        $operador = new operador();
        $operador->passarOperador(operador::maiorQue);
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