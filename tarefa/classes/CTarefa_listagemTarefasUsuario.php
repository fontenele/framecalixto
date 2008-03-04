<?php
/**
* Classe de definição da camada de controle
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_listagemTarefasUsuario extends CTarefa_listagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$alinhamento = null;
		$this->adicionarColunaPersonalizada('Ultimo', 'ultimoEncaminhamento', '10%', $alinhamento, 3);
	}
	/**
	* Método personalização de coluna
	* @param [negocio]
	*/
	function ultimoEncaminhamento(NTarefa $negocio){
		$nAtividade = new NAtividade();
		$nAtividade->lerUltimaAtividadeDeEncaminhamento($negocio);
		return $nAtividade->pegarDsAtividade();
	}
	/**
	* Método de abertura da linha da listagem
	* @param [mixed] item a ser apresentado na listagem
	* @param [numerico] número da linha a ser apresentada
	*/
	public function abrirLinha($negocio,$nrLinha){
		switch(true){
			case $negocio->pegarCsStatus()  == 'F': $css = 'descricaoFechada'		;break;
			case $negocio->pegarCsTipoTarefa()== 1: $css = 'descricaoAdministrativa';break;
			case $negocio->pegarCsTipoTarefa()== 2: $css = 'descricaoHardware'		;break;
			case $negocio->pegarCsTipoTarefa()== 3: $css = 'descricaoSoftware'		;break;
			case $negocio->pegarCsTipoTarefa()== 4: $css = 'descricaoRede'			;break;
			default:
				if($nrLinha%2){
					return "\t<tr class='linhaListagem1'>\n";
				}else{
					return "\t<tr class='linhaListagem2'>\n";
				}
		}
		return "\t<tr class='{$css}'>\n";
	}
}
?>