<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_geradorDefinirSistema extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->gerarMenuprincipal();
		$this->gerarMenuPrograma();
		global $definicoes;
		$arAmbiente		= array(
			'3'=>$this->inter->pegarTexto('ambiente3'),
			'2'=>$this->inter->pegarTexto('ambiente2'),
			'1'=>$this->inter->pegarTexto('ambiente1')
		);
		$arTiposBancosDisponiveis = array(
			'sqlite'=>'Sqlite',
			'postgres'=>'PostgreSQL',
			'mysql'=>'MySql',
			'oracle'=>'Oracle',
		);
		$arBooleano		= array('sim'=>$this->inter->pegarTexto('sim'),'nao'=>$this->inter->pegarTexto('nao'));

		$paginaInicial	= ($paginaInicial = strval($definicoes->sistema['paginaInicial'])) ? $paginaInicial : 'CControleAcesso_verPrincipal';
		$paginaErro		= ($paginaErro = strval($definicoes->sistema['paginaErro'])) ? $paginaErro : 'CControleAcesso_erroAcesso';

		$liberadoCA		= ($liberadoCA = strval($definicoes->controleDeAcesso['liberado'])) ? $liberadoCA : 'sim';
		$classeCA		= ($classeCA = strval($definicoes->controleDeAcesso['classe'])) ? $classeCA : 'NControleAcesso';
		$metodoCA		= ($metodoCA = strval($definicoes->controleDeAcesso['metodoLiberacao'])) ? $metodoCA : 'validarAcesso';

		$classeMenu		= ($classeMenu = strval($definicoes->controleDeMenu['classe'])) ? $classeMenu : 'NControleMenu';
		$metodoMenu1	= ($metodoMenu1 = strval($definicoes->controleDeMenu['metodoMenuSite'])) ? $metodoMenu1 : 'menuPrincipal';
		$metodoMenu2	= ($metodoMenu2 = strval($definicoes->controleDeMenu['metodoMenuSistema'])) ? $metodoMenu2 : 'menuMenuSistema';

		$this->sessao->limpar();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		//Páginas do sistema
		$this->visualizacao->paginaInicial 	= VComponente::montar('input','sistema[paginaInicial]',$paginaInicial);
		$this->visualizacao->paginaErro 	= VComponente::montar('input','sistema[paginaErro]',$paginaErro);
		$this->visualizacao->ambiente 	= VComponente::montar('select','sistema[ambiente]',$paginaErro,null,$arAmbiente);
		//Controle de acesso do sistema
		$this->visualizacao->liberadoCA = VComponente::montar('combobox','controleDeAcesso[liberado]',$liberadoCA,null,$arBooleano);
		$this->visualizacao->classeCA = VComponente::montar('input','controleDeAcesso[classe]',$classeCA);
		$this->visualizacao->metodoCA = VComponente::montar('input','controleDeAcesso[metodoLiberacao]',$metodoCA);
		//Controle de menu do sistema
		$this->visualizacao->classeMenu = VComponente::montar('input','controleDeMenu[classe]',$classeMenu);
		$this->visualizacao->metodoMenuPrincipal = VComponente::montar('input','controleDeMenu[metodoMenuSite]',$metodoMenu1);
		$this->visualizacao->metodoMenuSistema = VComponente::montar('input','controleDeMenu[metodoMenuSistema]',$metodoMenu2);
		//Bancos
		$conexao = $tipo = $porta = $nome = $usuario = $senha = array();
		$arInputConfig = array('size'=>'10','style'=>'margin-right: 2px;');
		$arSelectConfig = array('style'=>'margin-right: 2px;');
		foreach($definicoes->xpath('/definicoes/bancos/banco') as $banco){
			$conexao[] 	= VComponente::montar('input'			,'banco[id][]'		,$banco['id']		,$arInputConfig);
			$tipo[] 	= VComponente::montar('select'			,'banco[tipo][]'	,$banco['tipo']		,$arSelectConfig, $arTiposBancosDisponiveis);
			$servidor[] = VComponente::montar('input'			,'banco[servidor][]',$banco['servidor']	,$arInputConfig);
			$porta[] 	= VComponente::montar('input'			,'banco[porta][]'	,$banco['porta']	,array('size'=>'2'));
			$nome[] 	= VComponente::montar('input'			,'banco[nome][]'	,$banco['nome']		,$arInputConfig);
			$usuario[] 	= VComponente::montar('input'			,'banco[usuario][]'	,$banco['usuario']	,$arInputConfig);
			$senha[] 	= VComponente::montar('palavra chave'	,'banco[senha][]'	,$banco['senha']	,$arInputConfig);
			$multipla[] = VComponente::montar('combobox'		,'banco[conexaoMultipla][]',$banco['conexaoMultipla'] == 'sim' ? 'sim':'nao',null,$arBooleano);
		}
		$this->visualizacao->conexao = $conexao;
		$this->visualizacao->tipo = $tipo;
		$this->visualizacao->servidor = $servidor;
		$this->visualizacao->porta = $porta;
		$this->visualizacao->nome = $nome;
		$this->visualizacao->usuario = $usuario;
		$this->visualizacao->senha = $senha;
		$this->visualizacao->multipla = $multipla;

		//Diretorios
		foreach($definicoes->xpath('/definicoes/diretorios/diretorio') as $diretorio){
			$dirId[] 	= VComponente::montar('VHidden','diretorios[id][]',$diretorio['id']);
			$dir[] 		= VComponente::montar('input','diretorios[dir][]',$diretorio['dir'],array('size'=>'60px'));
			$entidade[] = VComponente::montar('combobox','diretorios[entidade][]',$diretorio['entidade'] == 'sim'?'sim':'nao',array('style'=>'width: 80px'),$arBooleano);
		}
		$this->visualizacao->dirId = $dirId;
		$this->visualizacao->dirCaminho = $dir;
		$this->visualizacao->dirEntidade = $entidade;

		//Arquivos
		foreach($definicoes->xpath('/definicoes/arquivos/arquivo') as $arquivo){
			$arqId[] 	= VComponente::montar('VHidden','arquivos[tipo][]',$arquivo['tipo']);
			$arq[] 		= VComponente::montar('input','arquivos[nome][]',$arquivo['nome'],array('size'=>'60px'));
		}
		$this->visualizacao->arqId = $arqId;
		$this->visualizacao->arquivo = $arq;

		$this->visualizacao->action = '?c=CUtilitario_definirSistema';
		$this->visualizacao->mostrar();
	}
	/**
	* Monta a coleção de menu do programa
	* @return colecaoPadraoMenu menu do programa
	*/
	public function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$item = $this->inter->pegarTexto('botaoGravar');
		$menu->$item->passar_link('javascript:document.formulario.submit();');
		$menu->$item->passar_imagem('.sistema/imagens/botao_gravar.png');
		return $menu;
	}
}
?>