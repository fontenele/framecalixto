<?php
/**
* Classe de controle
* Listar as entidades do sistema
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_listarEntidade extends controlePadraoVerPesquisa{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$p = new NPessoa();
		$ps = $p->lerTodos();
		$d = dir(".");
		$negocios = new colecao();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
					$obNegocio = new $negocio();
					if( isset($obNegocio->inter) ) {
						$negocios->$arquivo = $obNegocio->inter->pegarNome();
					}
				}
			}
		}
		$d->close();
		asort($negocios->itens);
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->visualizacao->listagem = $negocios->itens;
		$this->visualizacao->mostrar('');
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoNovo')] =
			sprintf($link,definicaoEntidade::controle($this,'geradorDefinirEntidade'));
		return $menu;
	}
}
?>