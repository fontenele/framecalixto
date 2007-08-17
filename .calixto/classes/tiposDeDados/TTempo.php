<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa uma data
* @package Infra-estrutura
* @subpackage tipoDeDados
*/
class TTempo extends objeto{
	/**
	* @var [inteiro] numero de segundos
	*/
	protected $tempo;
	/**
	* Método construtor
	* @param [inteiro] tempo em segundos 
	*/
	public function __construct($nrSegundos){
		$this->tempo = $nrSegundos;
	}
	/**
	* Método de soma de tempo
	* @param [TTempo] tempo para a soma
	*/
	public function somar(TTempo $tempo){
		$this->tempo	+= $tempo->pegarTempo();
	}
	/**
	* Método de subtração de tempo
	* @param [TTempo] tempo para a subtração
	*/
	public function subtrair(TTempo $tempo){
		$this->tempo	-= $tempo->pegarTempo();
		$this->ajustar();
	}
	/**
	* Metodo de retorno em segundos do tempo
	* @param [inteiro] numero de segundos
	*/
	public function pegarEmSegundos(){
		return (integer) $this->tempo;
	}
	/**
	* Metodo de retorno em minutos do tempo
	* @param [inteiro] numero de minutos
	*/
	public function pegarEmMinutos(){
		return (integer) round($this->tempo / 60);
	}
	/**
	* Metodo de retorno em horas do tempo
	* @param [inteiro] numero de horas
	*/
	public function pegarEmHoras(){
		return (integer) round($this->tempo / (3600));
	}
	/**
	* Metodo de retorno em dias do tempo
	* @param [inteiro] numero de dias
	*/
	public function pegarEmDias(){
		return (integer) round($this->tempo / (86400));
	}
	/**
	* Metodo de retorno em meses do tempo
	* @param [inteiro] numero de meses
	*/
	public function pegarEmMeses(){
		return (integer) round($this->tempo / (2592000));
	}
	/**
	* Metodo de retorno em anos do tempo
	* @param [inteiro] numero de anos
	*/
	public function pegarEmAnos(){
		return (integer) round($this->tempo / (31104000));
	}
	/**
	* Metodo de retorno em segundos do tempo
	* @param [inteiro] numero de segundos
	*/
	public function pegarSegundos(){
		$anosDecorridosEmSegundos = (integer)($this->pegarAnos() * (31104000));
		$mesesDecorridosEmSegundos = (integer)($this->pegarMeses() * (2592000));
		$diasDecorridosEmSegundos = (integer)($this->pegarDias() * (86400));
		$horasDecorridosEmSegundos = (integer)($this->pegarHoras() * (3600));
		$minutosDecorridosEmSegundos = (integer)($this->pegarMinutos() * 60);
		return (integer) round(($this->tempo - $anosDecorridosEmSegundos - $mesesDecorridosEmSegundos - $diasDecorridosEmSegundos - $horasDecorridosEmSegundos) - $minutosDecorridosEmSegundos);
	}
	/**
	* Metodo de retorno em minutos do tempo
	* @param [inteiro] numero de minutos
	*/
	public function pegarMinutos(){
		$anosDecorridosEmSegundos = (integer)($this->pegarAnos() * (31104000));
		$mesesDecorridosEmSegundos = (integer)($this->pegarMeses() * (2592000));
		$diasDecorridosEmSegundos = (integer)($this->pegarDias() * (86400));
		$horasDecorridosEmSegundos = (integer)($this->pegarHoras() * (3600));
		return (integer) round(($this->tempo - $anosDecorridosEmSegundos - $mesesDecorridosEmSegundos - $diasDecorridosEmSegundos - $horasDecorridosEmSegundos) / (60));
	}
	/**
	* Metodo de retorno em horas do tempo
	* @param [inteiro] numero de horas
	*/
	public function pegarHoras(){
		$anosDecorridosEmSegundos = (integer)($this->pegarAnos() * (31104000));
		$mesesDecorridosEmSegundos = (integer)($this->pegarMeses() * (2592000));
		$diasDecorridosEmSegundos = (integer)($this->pegarDias() * (86400));
		return (integer) round(($this->tempo - $anosDecorridosEmSegundos - $mesesDecorridosEmSegundos - $diasDecorridosEmSegundos) / (3600));
	}
	/**
	* Metodo de retorno em dias do tempo
	* @param [inteiro] numero de dias
	*/
	public function pegarDias(){
		$anosDecorridosEmSegundos = (integer)($this->pegarAnos() * (31104000));
		$mesesDecorridosEmSegundos = (integer)($this->pegarMeses() * (2592000));
		return (integer) round(($this->tempo - $anosDecorridosEmSegundos - $mesesDecorridosEmSegundos) / (86400));
	}
	/**
	* Metodo de retorno em meses do tempo
	* @param [inteiro] numero de meses
	*/
	public function pegarMeses(){
		$anosDecorridosEmSegundos = (integer)($this->pegarAnos() * (31104000));
		return (integer) round(($this->tempo - $anosDecorridosEmSegundos)/ (2592000));
	}
	/**
	* Metodo de retorno em anos do tempo
	* @param [inteiro] numero de anos
	*/
	public function pegarAnos(){
		return (integer) round($this->tempo / (31104000));
	}
}
?>
