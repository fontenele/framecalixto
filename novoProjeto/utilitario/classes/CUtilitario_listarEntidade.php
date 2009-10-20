<?php
/**
* Classe de controle
* Listar as entidades do sistema
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_listarEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->visualizacao = new visualizacaoPadrao($this);
		$this->inter = new IUtilitario();
		$this->criarVisualizacaoPadrao();
		$d = dir(".");
		$negocios = new colecao();
		$controles = new colecao();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
					$obNegocio = new $negocio();
					if( $obNegocio instanceof negocioPadrao ) {
						$negocios->$arquivo = $obNegocio->pegarInter()->pegarNome();
						$controles->$arquivo = 'C'.ucfirst($arquivo).'_verPesquisa';
					}
				}
			}
		}
		$d->close();
		asort($negocios->itens);
		$this->gerarMenus();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->visualizacao->listagem = $negocios->itens;
		$this->visualizacao->controles = $controles->itens;
		$this->visualizacao->action = '';
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Novo Cadastro'} = new VMenu('Novo Cadastro','?c=CUtilitario_geradorDefinirEntidade','utilitario/imagens/nova_pasta.png');
		$menu->{'Tabelas do Banco'} = new VMenu('Tabelas do Banco','?c=CUtilitario_listarTabelas','utilitario/imagens/tabelas.png');
		return $menu;
	}
}
?>