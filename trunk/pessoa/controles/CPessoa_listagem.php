<?php
/**
* Classe de controle
* Executa a listagem de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_listagem extends controlePadraoListagem{
	/**
	* Retorno da apresentação do tipo de pessoa .
	*/
	public function mostrarTipoPessoa($nPessoa){
		switch($nPessoa->pegarCsPessoa()){
			case('FI'):
				return $this->internacionalizacao->pegarTexto('FI');
			break;
			case('FE'):
				return $this->internacionalizacao->pegarTexto('FE');
			break;
			case('JI'):
				return $this->internacionalizacao->pegarTexto('JI');
			break;
			case('JE'):
				return $this->internacionalizacao->pegarTexto('JE');
			break;
		}
	}
}
?>
