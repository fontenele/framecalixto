<?php
/**
* Classe de representação de uma camada de negócio da entidade Log Acesso
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Log Acesso
*/
class NLogAcesso extends negocioPadrao{
	/**
	* @gerador variavelPadrao
	* @var integer Código
	*/
	public $idLogAcesso;
	/**
	* @gerador variavelPadrao
	* @var integer Usuário
	*/
	public $idUsuario;
	/**
	* @gerador variavelPadrao
	* @var string URL
	*/
	public $txUrl;
	/**
	* @gerador variavelPadrao
	* @var string Data
	*/
	public $dtAcesso;
	/**
	* @gerador variavelPadrao
	* @var string IP
	*/
	public $txIP;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @gerador metodoPadrao
	* @return string
	*/
	function nomeChave(){ return 'idLogAcesso'; }
}
?>