<?php
/**
* Classe de controle
* Visualiza a seleção de acessos para um usuário
* @package Sistema
* @subpackage Usuario
*/
class CUsuario_verSelecionarAcessos extends controlePadraoVerEdicaoUmPraMuitos{
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param string tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		if($negocio->pegarIdUsuario()){
			$nUsuario = new NUsuario();
			$nUsuario->ler($negocio->pegarIdUsuario());
			$this->visualizacao->usuario = $nUsuario->pegarNmLogin();
		}
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'selecionarAcessos'));
		$this->visualizacao->idUsuario = VComponente::montar('oculto','idUsuario',$negocio->pegarIdUsuario());
		$this->visualizacao->nmLogin = $negocio->pegarNmLogin();
		$this->visualizacao->listagem = CPerfil_verSelecionarAcessos::montarListagemAcessos($negocio);
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->removerItem($this->inter->pegarTexto('botaoExcluir'));
		return $menu;
	}

}
?>