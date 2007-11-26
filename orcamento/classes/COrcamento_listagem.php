<?php
/**
* Classe de controle
* Cria a listagem dos orçamentos
* @package Sistema
* @subpackage orcamento
*/
class COrcamento_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
// 		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this->pegarControle(),$arquivoXML));
		$this->adicionarColuna('Qtd.', 'quantidade', '10%', 'direita', 3);
		$this->adicionarColunaPersonalizada('Remover', 'remover', '5%', $alinhamento, 10);
	}
	/**
	* Método de criação da coleção a ser listada
	*/
	function remover(NOrcamento $nOrcamento){
		$imagem = new VEtiquetaHtml('img');
		$imagem->passarAlt('Nova');
		$imagem->passarSrc('tarefa/imagens/icone_lixo.png')	;
		$imagem->passarBorder(0);
		$link = new VEtiquetaHtml('a');
		$link->passarHref(sprintf('?c=%s&amp;removerItem=%s','CTarefa_atualizarTarefa',$nOrcamento->pegarOrcamento()));
		$link->passarConteudo($imagem->__toString());
		$link->passarTitle('Remover item');
		$center = new VEtiquetaHtml('center');
		$center->passarConteudo($link->__toString());
		return $center->__toString();
	}
}
?>