<?php
/**
 * Classe de controle
 * Visualiza as persistentes que irão executar a recriação da tabela no banco
 * @author calixto
 */
class CUtilitario_verRecriarBase extends controlePadrao {
	/**
	 * Método inicial do controle
	 */
	public function  inicial() {
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->montarApresentacao(new NAcesso());
		$this->gerarMenuPrograma();
		$this->gerarMenus();
		parent::inicial();
	}
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao') {
		$this->visualizacao->cssExta = '.sistema/debug.css';
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'recriarBase'));
		$this->visualizacao->tituloEspecifico = 'Recriador de Base';
		$this->visualizacao->listagem = $this->classes();
		$this->visualizacao->executarNoBanco = VComponente::montar('checkbox', 'executarNoBanco', 'executarNoBanco');
		foreach($this->classes() as $classe){
			$obNegocio = new $classe();
			$arPersistente = $obNegocio->pegarPersistente()->pegarEstrutura();
			$listagem[$classe]['check'] = VComponente::montar('checkbox', "classes[{$classe}]", $classe);
			$listagem[$classe]['tabela'] = $arPersistente['nomeTabela'];
			$listagem[$classe]['nome'] = $obNegocio->pegarInter()->pegarNome();
		}
		$this->visualizacao->listagem = $listagem;
	}
	/**
	* Método de geração das classes e ordem a serem recriadas
	*/
	function classes(){
 		$classes[] = 'NEstado';
 		$classes[] = 'NPessoa';
 		$classes[] = 'NUsuario';
 		$classes[] = 'NPerfil';
 		$classes[] = 'NAcesso';
 		$classes[] = 'NUsuarioPerfil';
 		$classes[] = 'NLogAcesso';
 		$classes[] = 'NMenu';
		return $classes;
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Executar'} = new VMenu('Executar','#','.sistema/icones/disk.png');
		$menu->{'Executar'}->passar_id('executar');
		return $menu;
	}
}
?>
