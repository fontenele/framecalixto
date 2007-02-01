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
		$this->visualizacaoPadrao->entidade = 
			VComponente::montar('input','entidade',null);
		$this->visualizacaoPadrao->recriarBase = 
			VComponente::montar('checkbox','recriarBase',null);
			
		$adicionar = VComponente::montar('botao','adicionar', $this->internacionalizacao->pegarTexto('adicionar'));
		$adicionar->adicionarOnClick('teste(document.formulario.novaPropriedade);');
		$this->visualizacaoPadrao->adicionar = $adicionar;
		$this->visualizacaoPadrao->action = '?c=CUtilitario_geradorGerarFonte';
		$this->visualizacaoPadrao->menuPrograma = VComponente::montar('menu de programa',null,null,null, array('definir'=>'javascript:validar();'));
		$this->visualizacaoPadrao->mostrar();
	}
}
?>
