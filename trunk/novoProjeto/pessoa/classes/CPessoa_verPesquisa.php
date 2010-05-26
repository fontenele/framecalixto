<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_verPesquisa extends controlePadraoPesquisa{
	/**
	 * Método de apresentação da listagem
	 * @param visualizacao $visualizacao
	 * @param colecao $colecao
	 * @param pagina $pagina
	 */
	public static function montarListagem(visualizacao $visualizacao,colecao $colecao,pagina $pagina, $entidade = null){
		parent::montarListagem($visualizacao,$colecao,$pagina, $entidade);
		$visualizacao->listagem->adicionarColunaPersonalizada('Tipo', 'CPessoa_verPesquisa::tipoPessoa', '5%', null, -1);
		//$this->visualizacao->listagem->adicionarColunaPersonalizada('Tarefa', 'CPessoa_verPesquisa::novaTarefa', '5%', null, 10000000);
	}
	/**
	* Retorno da apresentação do tipo de pessoa .
	*/
	public static function tipoPessoa($nPessoa){
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
	public static function novaTarefa($nPessoa){
		$imagem = new VEtiquetaHtml('img');
		$imagem->passarAlt('Nova');
		$imagem->passarSrc('pessoa/imagens/novaTarefa.png')	;
		$imagem->passarBorder(0);
		$link = new VEtiquetaHtml('a');
		$link->passarHref(sprintf('?c=%s&amp;pessoa=%s','CTarefa_verCriarNova',$nPessoa->pegarIdPessoa()));
		$link->passarConteudo($imagem->__toString());
		$link->passarTitle('Nova tarefa para: '.$nPessoa->pegarNmPessoa());
		$center = new VEtiquetaHtml('center');
		$center->passarConteudo($link->__toString());
		return $center->__toString();
	}
}
?>
