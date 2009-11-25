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
		$pUtilitario = new PUtilitario(conexao::criar());
		$negocios = new colecao();
		$controles = new colecao();
		$tabelas = new colecao();
		$sequencias = new colecao();
		$classesNegocio = new colecao();
		$camposDefinidos = new colecao();
		$camposExistentes= new colecao();
		$diferencas = new colecao();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
					$obNegocio = new $negocio();
					if( $obNegocio instanceof negocioPadrao ) {
						$negocios->$arquivo = $obNegocio->pegarInter()->pegarNome();
						$tabelas->$arquivo = $obNegocio->pegarPersistente()->pegarNomeTabela();
						$sequencias->$arquivo = $obNegocio->pegarPersistente()->pegarNomeSequencia();
						$classesNegocio->$arquivo = 'N'.ucfirst($arquivo);
						$camposDefinidos->$arquivo = count($obNegocio->pegarMapeamento());
						$camposExistentes->$arquivo = count($pUtilitario->lerCampos($obNegocio->pegarPersistente()->pegarNomeTabela()));
						$diferencas->$arquivo = ($camposDefinidos->$arquivo - $camposExistentes->$arquivo) ? 'difereça': '';
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
		$this->visualizacao->tabelas = $tabelas->itens;
		$this->visualizacao->sequencias = $sequencias->itens;
		$this->visualizacao->negocios = $classesNegocio->itens;
		$this->visualizacao->camposDefinidos = $camposDefinidos->itens;
		$this->visualizacao->camposExistentes = $camposExistentes->itens;
		$this->visualizacao->diferencas = $diferencas->itens;
		$this->visualizacao->action = '';
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Novo Cadastro'} = new VMenu('Novo Cadastro','?c=CUtilitario_geradorDefinirEntidade','utilitario/imagens/nova_pasta.png');
		$menu->{'Tabelas do Banco'} = new VMenu('Tabelas do Banco','?c=CUtilitario_listarTabelas','utilitario/imagens/tabelas.png');
		return $menu;
	}
}
?>