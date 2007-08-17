<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage tarefa
*/
class CAtividade_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Fim', 'encerrarAtividade', '5%', 'centro', 4);
		$this->adicionarColunaPersonalizada('Tipo', 'apresentarTipo', '1%', 'centro', 2);
	}
	/**
	* Método de personalização de coluna
	* @param [negocio]
	*/
	function encerrarAtividade(NAtividade $negocio){
		$dtInicio = $negocio->pegarDtInicio();
		$dtFim = $negocio->pegarDtFim();
		$nUsuario = sessaoSistema::pegar('usuario');
		if($nUsuario->pegarIdUsuario() == $negocio->pegarIdUsuario()) {
			$id = $negocio->pegarIdAtividade();
			if($dtInicio->__toString() == $dtFim->__toString())
			return VComponente::montar('botao',"encerrarAtividade[$id]",'encerrar')->__toString();
		}
		return $dtFim->__toString();
	}
	/**
	* Método de personalização de coluna
	* @param [negocio]
	*/
	function apresentarTipo(NAtividade $negocio){
		switch(true){
			case($negocio->pegarCsAtividade() == '2'):
				$imagem = 'atividade_comunicacao.png';
			break;
			default:
				$dtInicio = $negocio->pegarDtInicio();
				$dtFim = $negocio->pegarDtFim();
				$imagem = ($dtInicio->__toString() == $dtFim->__toString()) ? 'atividade_execucao.png' : 'atividade_executado.png';
			break;
		}
		$img = new VEtiquetaHtml('img');
		$img->passarSrc('tarefa/imagens/'.$imagem);
		return $img->__toString();
	}
}
?>
