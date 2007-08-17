<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_geradorDefinirEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->sessao->limpar();
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->visualizacao->entidade = VComponente::montar('input','entidade',null);
		$this->visualizacao->recriarBase = VComponente::montar('checkbox','recriarBase',null);
		
		$adicionar = VComponente::montar('botao','adicionar', $this->inter->pegarTexto('adicionar'));
		$adicionar->adicionarOnClick('teste(document.formulario.novaPropriedade);');
		$this->visualizacao->adicionar = $adicionar;
		$this->visualizacao->action = '?c=CUtilitario_geradorGerarFonte';
		$this->visualizacao->menuPrograma = VComponente::montar('menu de programa',null,null,null, array('definir'=>'javascript:validar();'));
		$this->visualizacao->mostrar();
	}
}
?>