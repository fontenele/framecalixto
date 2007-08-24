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
						$negocios->$negocio = $obNegocio->inter->pegarNome();
					}
				}
			}
		}
		$d->close();
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina();
		$this->listagem = $this->criarControleListagem();
		$this->listagem->passarPagina($this->pegarPagina());
		$this->listagem->colecao = $negocios;
		$this->listagem->controle = definicaoEntidade::controle($this,'mudarPagina');
	echo $this->visualizacao->listagem = $this->listagem;
		$this->visualizacao->mostrar('');
	}
}
?>