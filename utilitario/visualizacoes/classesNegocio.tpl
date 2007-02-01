<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade �$entidade�
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage �$pacote�
*/
class �$negocioNome� extends negocioPadrao{
	�foreach from=$nomes key=indice item=propriedade �
/**
	* @var [�$tipos[$indice]�] �$nomesPropriedades[$indice]�
	*/
	public $�$propriedade�;
	�/foreach�
	/**
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return '�$chave�'; }
}
?>
