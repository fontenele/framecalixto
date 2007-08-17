<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('', 'apresentarCsTipoTarefa', '1%', $alinhamento, 1);
		$this->adicionarColunaPersonalizada('Tarefa', 'apresentarTarefa', '5%', $alinhamento, 2);
		$this->adicionarColunaPersonalizada('De', 'apresentarUsuarioAnterior', '10%', $alinhamento, 3);
		$this->adicionarColuna('Descrição', 'dsTarefa', '30%', $alinhamento, 4);
		$this->adicionarColunaPersonalizada('Sit.', 'apresentarSituacao', '1%', $alinhamento, 9);
	}
	/**
	* Método de personalização de campo
	* @param [negocio] negocio para a personalização
	*/
	function apresentarTarefa(NTarefa $negocio){
		$controle = definicaoEntidade::controle($negocio,'verTarefa');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return $retorno.="\t\t<a href='{$link}' >".$negocio->valorChave()."</a>\n";
	}
	/**
	* Método de personalização de campo
	* @param [negocio] negocio para a personalização
	*/
	function apresentarUsuarioAnterior(NTarefa $negocio){
		$usuario = sessaoSistema::pegar('usuario');
		$usuario->ler($negocio->pegarIdResponsavelAnterior());
		return $usuario->valorDescricao();
	}
	/**
	* Método de personalização de campo
	* @param [negocio] negocio para a personalização
	*/
	function apresentarCsTipoTarefa(NTarefa $negocio){
		$imagem = new VEtiquetaHtml('img');
		switch(true){
			case $negocio->pegarCsStatus()  == 'F': $imagem->passarSrc('')		;break;
			case $negocio->pegarCsTipoTarefa()== 1: $imagem->passarSrc('tarefa/imagens/tarefa_administrativa.png')	;break;
			case $negocio->pegarCsTipoTarefa()== 2: $imagem->passarSrc('tarefa/imagens/tarefa_hardware.png')		;break;
			case $negocio->pegarCsTipoTarefa()== 3: $imagem->passarSrc('tarefa/imagens/tarefa_software.png')		;break;
			case $negocio->pegarCsTipoTarefa()== 4: $imagem->passarSrc('tarefa/imagens/tarefa_network.png')		;break;
			default:
		}
		return $imagem->__toString();
	}
	/**
	* Método de personalização de campo
	* @param [negocio] negocio para a personalização
	*/
	function apresentarSituacao(NTarefa $negocio){
		$imagem = new VEtiquetaHtml('img');
		switch(true){
			case $negocio->pegarCsStatus()  == 'F': 
				$imagem->passarSrc('tarefa/imagens/atividade_executado.png')		;
			break;
			default:
				$imagem->passarSrc('tarefa/imagens/atividade_execucao.png');
		}
		return $imagem->__toString();
	}
}
?>
