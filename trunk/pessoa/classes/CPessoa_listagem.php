<?php
/**
* Classe de controle
* Executa a listagem de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Tipo', 'tipoPessoa', '5%', $alinhamento, -1);
		$this->adicionarColunaPersonalizada('Tarefa', 'novaTarefa', '20%', $alinhamento, 30);
	}
	/**
	* Retorno da apresentação do tipo de pessoa .
	*/
	public function tipoPessoa($nPessoa){
		$imagem = new VEtiquetaHtml('img');
		switch($nPessoa->pegarCsPessoa()){
			case 'FI':
			case 'FE':
				$imagem->passarAlt('Física');
				$imagem->passarSrc('pessoa/imagens/fisica.png')	;
			break;
			case 'JI':
			case 'JE':
				$imagem->passarAlt('Jurídica');
				$imagem->passarSrc('pessoa/imagens/empresa.png')	;
			break;
		}
		$center = new VEtiquetaHtml('center');
		$center->passarConteudo($imagem->__toString());
		return $center->__toString();
	}
	/**
	* Retorno da apresentação do tipo de pessoa .
	*/
	public function novaTarefa($nPessoa){
		$imagem = new VEtiquetaHtml('img');
		$imagem->passarAlt('Nova');
		$imagem->passarSrc('pessoa/imagens/novaTarefa.png')	;
		$imagem->passarBorder(0);
		$link = new VEtiquetaHtml('a');
		$link->passarHref(sprintf('?c=%s&amp;pessoa=%s','CTarefa_criarNova',$nPessoa->pegarIdPessoa()));
		$link->passarConteudo($imagem->__toString());
		$link->passarTitle('Nova tarefa para: '.$nPessoa->pegarNmPessoa());
		$center = new VEtiquetaHtml('center');
		$center->passarConteudo($link->__toString());
		return $center->__toString();
	}
}
?>