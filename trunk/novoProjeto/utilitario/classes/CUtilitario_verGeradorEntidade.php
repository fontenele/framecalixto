<?php
/**
* Classe de controle
* Visualiza a tela de definição de um cadastro do sistema
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_verGeradorEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->sessao->limpar();
		$this->gerarMenus();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->visualizacao->jsExtra = '
			<script language="JavaScript" type="text/javascript" src=".sistema/scripts/calixto.string.js"></script>
		';

		$this->visualizacao->entidade = VComponente::montar('input','entidade',null);
		$this->visualizacao->nomeTabela = VComponente::montar('input','nomeTabela',null);
		$this->visualizacao->nomeSequence = VComponente::montar('input','nomeSequence',null);
		$this->visualizacao->recriarBase = VComponente::montar('checkbox','recriarBase',null);
		$this->visualizacao->adicionar = VComponente::montar('botao','adicionar', $this->inter->pegarTexto('adicionar'));
		$this->visualizacao->action = '?c=CUtilitario_geradorEntidade';
		$this->visualizacao->dados = '<script>var definicao = false;</script>';
		$this->visualizacao->campos = null;
		$this->visualizacao->acesso = 'Nova geração de cadastro';
		$this->visualizacao->travarSugestaoDeNomesPersistente = 'false';


		$d = dir(".");
        $negocios = array();
        $tabelas = array();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
                    $negocios[] = $negocio;
                    $tabelas[] = $this->pegarTabela(new $negocio());
				}
			}
		}
		$d->close();
        foreach($tabelas as $index => $tabela){
            if(!$tabela) unset( $tabelas[$index] );
        }
        array_merge(array(''=>'&nbsp;'),$tabelas);
        array_merge(array(''=>'&nbsp;'),$negocios);

        $json = new json();
        $this->visualizacao->negocios = $json->pegarJson($negocios);
        $this->visualizacao->tabelas = $json->pegarJson(array($tabelas));

		if(isset($_GET['tabela'])) $this->montarTabela();
		if(isset($_GET['entidade'])) $this->montarEntidade();
		$this->visualizacao->mostrar();
	}
    /**
     * Método que retorna o nome da tabela de um objeto de negócio
     * @param negocio $negocio
     * @return string
     */
    protected function pegarTabela(negocio $negocio){
        try {
            $persistente = $negocio->pegarPersistente();
            if($negocio instanceof negocioPadrao){
                $arPersistente = $persistente->pegarEstrutura();
                return $arPersistente['nomeTabela'];
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }
	/**
	* Monta a coleção de menu do programa
	* @return colecaoPadraoMenu menu do programa
	*/
	public function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Gravar entidade'}->passar_link('javascript:$(this).gerarCadastro();');
		$menu->{'Gravar entidade'}->passar_imagem('icon-pencil icon-white');
		$menu->{'Gravar entidade'}->passar_classeLink('btn btn-primary');
		$menu->{'Entidades do sistema'}->passar_link('?c=CUtilitario_verListarEntidade');
		$menu->{'Entidades do sistema'}->passar_imagem('icon-globe');
		$menu->{'Tabelas do banco'}->passar_link('?c=CUtilitario_verListarTabelas');
		$menu->{'Tabelas do banco'}->passar_imagem('icon-list');
		return $menu;
	}

	/**
	* Método de montagem da entidade
	*/
	function montarEntidade(){
		$negocio = 'N'.ucfirst($_GET['entidade']);
		$persistente = 'P'.ucfirst($_GET['entidade']);
		$internacionalizacao = 'I'.ucfirst($_GET['entidade']);
		$controle = 'C'.ucfirst($_GET['entidade']).'_verPesquisa';
		$json = new json();
		$negocio = new $negocio();
		$persistente = new $persistente($negocio->pegarConexao());
		$internacionalizacao = new $internacionalizacao();
		$mapNegocio['negocio'] = $negocio->pegarMapeamento();
		$mapNegocio['bd'] = $persistente->pegarEstrutura();
		$mapNegocio['inter'] = $internacionalizacao->pegarInternacionalizacao();
		$map = self::pegarEstrutura($negocio);
		$mapNegocio['controle'] = $map['campos'];
		$arOrdem = isset($mapNegocio['bd']['ordem']) ? array_flip($mapNegocio['bd']['ordem']) : array();
		$arMapEntidade = array();
		$arMapEntidade['entidade']['nome'] = $mapNegocio['inter']['nome'];
		$arMapEntidade['banco']['tabela'] = $mapNegocio['bd']['nomeTabela'];
		$arMapEntidade['banco']['sequencia'] = $mapNegocio['bd']['nomeSequencia'];
		foreach($mapNegocio['negocio'] as $i => $map){
			
			$dominio = '';
			if(isset($mapNegocio['inter']['propriedade'][$map['propriedade']]['dominio'])){
				foreach($mapNegocio['inter']['propriedade'][$map['propriedade']]['dominio'] as $id => $valor){
					$dominio .= "[$id,$valor]";
				}
			}
			if($map['classeAssociativa'].$map['metodoLeitura']){
				$dominio = $map['classeAssociativa'].'::'.$map['metodoLeitura'];
			}
			$arMapEntidade['campos'][$i]['inter-nome'] = $mapNegocio['inter']['propriedade'][$map['propriedade']]['nome'];
			$arMapEntidade['campos'][$i]['inter-abreviacao'] = $mapNegocio['inter']['propriedade'][$map['propriedade']]['abreviacao'];
			$arMapEntidade['campos'][$i]['inter-descricao'] = $mapNegocio['inter']['propriedade'][$map['propriedade']]['descricao'];
			$arMapEntidade['campos'][$i]['negocio-propriedade'] = $map['propriedade'];
			$arMapEntidade['campos'][$i]['negocio-tipo'] = $map['tipo'];
			$arMapEntidade['campos'][$i]['negocio-tamanho'] = $mapNegocio['bd']['campo'][$map['campo']]['tamanho'];
			$arMapEntidade['campos'][$i]['negocio-pk'] = $mapNegocio['bd']['chavePrimaria'] == $map['campo'];
			$arMapEntidade['campos'][$i]['negocio-nn'] = $map['obrigatorio'];
			$arMapEntidade['campos'][$i]['negocio-uk'] = $map['indiceUnico'];
			$arMapEntidade['campos'][$i]['negocio-fk'] = isset($mapNegocio['bd']['campo'][$map['campo']]['chaveEstrangeira']);
			$arMapEntidade['campos'][$i]['negocio-dominio'] = $dominio;
			$arMapEntidade['campos'][$i]['persistente-campo'] = $map['campo'];
			$arMapEntidade['campos'][$i]['persistente-ordem'] = isset($arOrdem[$map['campo']]) ? $arOrdem[$map['campo']] : '';
			$arMapEntidade['campos'][$i]['persistente-tipo-ordem'] = '';
			$arMapEntidade['campos'][$i]['persistente-referencia-tabela'] = '';
			$arMapEntidade['campos'][$i]['persistente-referencia-campo'] = '';
			$arMapEntidade['campos'][$i]['visualizacao-componente'] = $mapNegocio['controle'][$map['propriedade']]['componente'];
			$arMapEntidade['campos'][$i]['visualizacao-edicao'] = $mapNegocio['controle'][$map['propriedade']]['edicao'];
			$arMapEntidade['campos'][$i]['visualizacao-pesquisa'] = $mapNegocio['controle'][$map['propriedade']]['pesquisa'];
			$arMapEntidade['campos'][$i]['visualizacao-ordem'] = isset($mapNegocio['controle'][$map['propriedade']]['ordem']) ? $mapNegocio['controle'][$map['propriedade']]['ordem'] : '';
			$arMapEntidade['campos'][$i]['visualizacao-ordem-descritivo'] = $map['descritivo'];
			
			if($arMapEntidade['campos'][$i]['negocio-fk']){
				$arMapEntidade['campos'][$i]['persistente-referencia-tabela'] = $mapNegocio['bd']['campo'][$map['campo']]['chaveEstrangeira']['tabela'];
				$arMapEntidade['campos'][$i]['persistente-referencia-campo'] = $mapNegocio['bd']['campo'][$map['campo']]['chaveEstrangeira']['campo'];
			}
			
			
			if(isset($mapNegocio['bd']['ordem'])){
				foreach($mapNegocio['bd']['ordem'] as $iOrdem => $ordem){
					$ordem = explode(' ',$ordem);
					if($map['campo'] == $ordem[0]){
						$arMapEntidade['campos'][$i]['persistente-tipo-ordem'] = isset($ordem[1]) ? true : false;
					}
				}
			}
		}
		$this->visualizacao->dados = '<script>var definicao = '.$json->pegarJson($arMapEntidade).';</script>';
		$this->visualizacao->acesso = 'Cadastro existente no sistema.';
	}
	/**
	* Método de montagem da tabela
	*/
	function montarTabela(){
		$json = new json();
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$desc = $persistente->lerTabela($_GET['tabela']);
		$sequences = $persistente->lerSequenciasDoBanco($_GET['tabela']);
		$sequences = array_merge(array(''=>'&nbsp;'),$sequences);
		if($sequences) $this->visualizacao->nomeSequence = VComponente::montar('select','nomeSequence',null,null,$sequences);
		$mapNegocio['entidade']['nome'] = '«Nome da entidade??»';
		$mapNegocio['banco']['tabela'] = $_GET['tabela'];
		$mapNegocio['banco']['sequencia'] = '«Nome da sequência???»';
		$mapNegocio['bd']['chavePrimaria'] = '';
		$mapNegocio['bd']['ordem'] = array('1'=>'');
		foreach ($desc as $i => $campo) {
			$tipoDeDado = $campo['tipo_de_dado'];
			switch ($campo['tipo_de_dado']) {
				case 'numerico':
					$campo['tamanho'] = $campo['tamanho'] > 30 ? 20 : $campo['tamanho'];
					$componente = 'numerico';
				break;
				case 'data':
					$campo['tamanho'] = '';
					$componente = 'data';
				break;
				default:
					$componente = 'caixa de entrada';
			}
			switch (true) {
				case $campo['campo_pk']:
					$pk = $mapNegocio['bd']['chavePrimaria'] = $campo['campo_pk'];
					$componente = 'oculto';
					$chaveEstrangeira = false;
				break;
				case $campo['campo_fk']:
					$componente = 'caixa de combinacao';
					$chaveEstrangeira = array('tabela'=>$campo['esquema_fk'].'.'.$campo['tabela_fk'],'campo'=>$campo['campo_fk']);
					$tabelaEstrangeira = $campo['esquema_fk'] ? $campo['esquema_fk'].'.'.$campo['tabela_fk'] : $campo['tabela_fk'];
					$campoEstrangeiro = $campo['campo_fk'];
				break;
				default:
					$tipoDeDado = ($campo['tipo_de_dado'] == 'numerico') ? 'tnumerico' : $campo['tipo_de_dado'];
					$chaveEstrangeira = false;
			}
			$mapNegocio['campos'][$i]['negocio-propriedade'] = str_replace(' ','',ucwords(str_replace('_',' ',$campo['campo'])));
			$mapNegocio['campos'][$i]['negocio-propriedade']{0} = strtolower($mapNegocio['campos'][$i]['negocio-propriedade']{0});
			$mapNegocio['campos'][$i]['negocio-tipo'] = $tipoDeDado;
			$mapNegocio['campos'][$i]['negocio-campo'] = $campo['campo'];
			$mapNegocio['campos'][$i]['negocio-pk'] = $campo['campo_pk'];
			$mapNegocio['campos'][$i]['negocio-nn'] = $campo['obrigatorio'] ? 'sim':'';
			$mapNegocio['campos'][$i]['negocio-uk'] = '';
			$mapNegocio['campos'][$i]['negocio-fk'] = $campo['campo_fk'];
			$mapNegocio['campos'][$i]['negocio-descritivo'] = '';
			$mapNegocio['campos'][$i]['negocio-dominio'] = $chaveEstrangeira ? '«Classe de Negocio ???»::lerTodos':'';
			
			$mapNegocio['campos'][$i]['controle-componente'] = $componente;
			$mapNegocio['campos'][$i]['controle-tamanho'] = '';
			$mapNegocio['campos'][$i]['controle-tipo'] = $campo['tipo_de_dado'];
			$mapNegocio['campos'][$i]['controle-obrigatorio'] = $campo['obrigatorio'] ? 'sim':'';
			$mapNegocio['campos'][$i]['controle-pesquisa'] = '';
			$mapNegocio['campos'][$i]['controle-valores'] = array();
			$mapNegocio['campos'][$i]['controle-classeAssociativa'] = $campo['campo_fk'] ? 'Classe de Negocio ???':'';
			$mapNegocio['campos'][$i]['controle-metodoLeitura'] = $chaveEstrangeira ? 'lerTodos':'';
			$mapNegocio['campos'][$i]['controle-listagem'] = $campo['campo_pk'] ? '1':'';
			$mapNegocio['campos'][$i]['controle-hyperlink'] = $campo['campo_pk'] ? 'sim': '';
			$mapNegocio['campos'][$i]['controle-largura'] = $campo['campo_pk'] ?'10%' :'';
			$mapNegocio['campos'][$i]['controle-ordem'] = $campo['campo_pk'] ? 1 : '';
			$mapNegocio['campos'][$i]['controle-campoPersonalizado'] = '';
			
			$mapNegocio['campos'][$i]['persistente-campo'] = $campo['campo'];
			$mapNegocio['campos'][$i]['persistente-tipo'] = $tipoDeDado;
			$mapNegocio['campos'][$i]['persistente-tamanho'] = $campo['tamanho'];
			$mapNegocio['campos'][$i]['persistente-obrigatorio'] = '';
			$mapNegocio['campos'][$i]['persistente-operadorDeBusca'] = 'igual';
			$mapNegocio['campos'][$i]['persistente-referencia-tabela'] = $campo['campo_fk'] ? ($campo['esquema_fk'] ? $campo['esquema_fk'].'.'.$campo['tabela_fk'] : $campo['tabela_fk']) : '';
			$mapNegocio['campos'][$i]['persistente-referencia-campo'] = $campo['campo_fk'] ? $campo['campo_fk'] : '';
			$mapNegocio['campos'][$i]['persistente-ordem'] = $campo['campo_pk']? '1':'';
			
			$mapNegocio['campos'][$i]['inter-nome'] = ucfirst(str_replace('_',' ',$campo['campo']));
			$mapNegocio['campos'][$i]['inter-abreviacao'] = ucwords(str_replace('_',' ',$campo['campo']));
			$mapNegocio['campos'][$i]['inter-descricao'] = $campo['descricao'];
			$mapNegocio['campos'][$i]['inter-dominio'] = '';
			
			$mapNegocio['campos'][$i]['visualizacao-componente'] = $componente;
			$mapNegocio['campos'][$i]['visualizacao-edicao'] = true;
			$mapNegocio['campos'][$i]['visualizacao-pesquisa'] = true;
			$mapNegocio['campos'][$i]['visualizacao-ordem'] = '';
			$mapNegocio['campos'][$i]['visualizacao-ordem-descritivo'] = '';
		}
		
		$this->visualizacao->campos = $mapNegocio;
		$this->visualizacao->acesso = 'Carga de tabela do banco';
		$this->visualizacao->travarSugestaoDeNomesPersistente = 'true';
		$this->visualizacao->dados = '<script>var definicao = '.$json->pegarJson($mapNegocio).';</script>';
	}
}
?>
