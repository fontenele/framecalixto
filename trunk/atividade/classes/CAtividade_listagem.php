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
			return VComponente::montar('confirmar',"encerrarAtividade[$id]",'encerrar')->__toString();
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
				$imagem = 'tarefa/imagens/atividade_comunicacao.png';
				$alt = 'Comunicação';
			break;
			case ($negocio->pegarDtInicio()->__toString() == $negocio->pegarDtFim()->__toString()):
				$imagem = 'tarefa/imagens/atividade_execucao.png' ;
				$alt = 'Executando';
			break;
			default:
				$imagem = 'tarefa/imagens/atividade_executado.png';
				$alt = 'Concluído';
			break;
		}
		$img = new VEtiquetaHtml('img');
		$img->passarSrc($imagem);
		$img->passarAlt($alt);
		return $img->__toString();
	}
}
?>
