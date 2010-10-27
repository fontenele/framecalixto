<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_geradorDefinirEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->sessao->limpar();
		$this->gerarMenus();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->visualizacao->jsExtra = '
			<script language="JavaScript" type="text/javascript" src=".sistema/scripts/jquery/js/jquery-ui-1.8.2.custom.min.js"></script>
			<script language="JavaScript" type="text/javascript" src=".sistema/scripts/calixto.string.js"></script>
		';

		$this->visualizacao->entidade = VComponente::montar('input','entidade',null);
		$this->visualizacao->nomeTabela = VComponente::montar('input','nomeTabela',null);
		$this->visualizacao->nomeSequence = VComponente::montar('input','nomeSequence',null);
		$this->visualizacao->recriarBase = VComponente::montar('checkbox','recriarBase',null);
		$this->visualizacao->adicionar = VComponente::montar('botao','adicionar', $this->inter->pegarTexto('adicionar'));
		$this->visualizacao->action = '?c=CUtilitario_geradorGerarFonte';
		$this->visualizacao->dados = null;
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
		$menu->{'Gravar entidade'}->passar_link('javascript:$.submeter();');
		$menu->{'Gravar entidade'}->passar_imagem('utilitario/imagens/gravar_arquivos.png');
		$menu->{'Entidades do sistema'}->passar_link('?c=CUtilitario_listarEntidade');
		$menu->{'Entidades do sistema'}->passar_imagem('utilitario/imagens/entidades.png');
		$menu->{'Tabelas do banco'}->passar_link('?c=CUtilitario_listarTabelas');
		$menu->{'Tabelas do banco'}->passar_imagem('utilitario/imagens/tabelas.png');
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
		foreach($mapNegocio['negocio'] as $i => $map){
			$mapEntidade[$i]['negocio'] = $map;
			$mapEntidade[$i]['controle'] = $mapNegocio['controle'][$map['propriedade']];
			$mapEntidade[$i]['persistente'] = $mapNegocio['bd']['campo'][$map['campo']];
			$mapEntidade[$i]['inter'] = $mapNegocio['inter']['propriedade'][$map['propriedade']];
 			if(!isset($mapNegocio['bd']['campo'][$map['campo']]['chaveEstrangeira']))
				$mapEntidade[$i]['persistente']['chaveEstrangeira'] = false;
			$mapEntidade[$i]['persistente']['ordem'] = '';
			if(isset($mapNegocio['bd']['ordem']))
			foreach($mapNegocio['bd']['ordem'] as $iOrdem => $ordem){
				$ordem = explode(' ',$ordem);
				if($map['campo'] == $ordem[0]){
					$mapEntidade[$i]['persistente']['ordem'] = $iOrdem;
					$mapEntidade[$i]['persistente']['tipoOrdem'] = isset($ordem[1]) ? true : false;
				}
			}
			$res = '';
			if(isset($mapEntidade[$i]['inter']['dominio'])){
				foreach($mapEntidade[$i]['inter']['dominio'] as $id => $valor){
					$res .= "[$id,$valor]";
				}
			}
			$mapEntidade[$i]['inter']['dominio'] = $res;
		}
		unset($mapNegocio['negocio']);
		unset($mapNegocio['controle']);
		unset($mapNegocio['inter']['propriedade']);
		unset($mapNegocio['inter']['mensagem']);
		unset($mapNegocio['inter']['texto']);
		unset($mapNegocio['bd']['campo']);
		$mapNegocio['entidade'] = $mapEntidade;
		$this->visualizacao->dados = $json->pegarJson(array($mapNegocio));
		$this->visualizacao->campos = $mapNegocio;
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
		$mapNegocio['bd']['nomeTabela'] = $_GET['tabela'];
		$mapNegocio['bd']['nomeSequencia'] = '«Nome da sequência???»';
		$mapNegocio['bd']['chavePrimaria'] = '';
		$mapNegocio['bd']['ordem'] = array('1'=>'');
		$mapNegocio['inter']['nome'] = '«Nome da entidade??»';
		$mapNegocio['inter']['titulo'] = '';
		$mapNegocio['inter']['tituloSistema'] = '';
		$mapNegocio['inter']['subtituloSistema'] = '';
		foreach ($desc as $indice => $campo) {
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
					$mapNegocio['bd']['chavePrimaria'] = $campo['campo_pk'];
					$componente = 'oculto';
					$chaveEstrangeira = false;
				break;
				case $campo['campo_fk']:
					$componente = 'caixa de combinacao';
					$chaveEstrangeira = array('tabela'=>$campo['esquema_fk'].'.'.$campo['tabela_fk'],'campo'=>$campo['campo_fk']);
				break;
				default:
					$tipoDeDado = ($campo['tipo_de_dado'] == 'numerico') ? 'tnumerico' : $campo['tipo_de_dado'];
					$chaveEstrangeira = false;
			}
			$mapNegocio['entidade'][$indice]['negocio']['propriedade'] = str_replace(' ','',ucwords(str_replace('_',' ',$campo['campo'])));
			$mapNegocio['entidade'][$indice]['negocio']['propriedade']{0} = strtolower($mapNegocio['entidade'][$indice]['negocio']['propriedade']{0});
			$mapNegocio['entidade'][$indice]['negocio']['tipo'] = $tipoDeDado;
			$mapNegocio['entidade'][$indice]['negocio']['campo'] = $campo['campo'];
			$mapNegocio['entidade'][$indice]['negocio']['obrigatorio'] = $campo['obrigatorio'] ? 'sim':'';
			$mapNegocio['entidade'][$indice]['negocio']['indiceUnico'] = '';
			$mapNegocio['entidade'][$indice]['negocio']['dominio'] = '';
			$mapNegocio['entidade'][$indice]['negocio']['descritivo'] = '';
			$mapNegocio['entidade'][$indice]['negocio']['classeAssociativa'] = $chaveEstrangeira ? '«Classe de Negocio ???»':'';
			$mapNegocio['entidade'][$indice]['negocio']['metodoLeitura'] = $chaveEstrangeira ? 'lerTodos':'';
			
			$mapNegocio['entidade'][$indice]['controle']['componente'] = $componente;
			$mapNegocio['entidade'][$indice]['controle']['tamanho'] = '';
			$mapNegocio['entidade'][$indice]['controle']['tipo'] = $campo['tipo_de_dado'];
			$mapNegocio['entidade'][$indice]['controle']['obrigatorio'] = $campo['obrigatorio'] ? 'sim':'';
			$mapNegocio['entidade'][$indice]['controle']['pesquisa'] = '';
			$mapNegocio['entidade'][$indice]['controle']['valores'] = array();
			$mapNegocio['entidade'][$indice]['controle']['classeAssociativa'] = $chaveEstrangeira ? 'Classe de Negocio ???':'';
			$mapNegocio['entidade'][$indice]['controle']['metodoLeitura'] = $chaveEstrangeira ? 'lerTodos':'';
			$mapNegocio['entidade'][$indice]['controle']['listagem'] = $campo['campo_pk'] ? '1':'';
			$mapNegocio['entidade'][$indice]['controle']['hyperlink'] = $campo['campo_pk'] ? 'sim': '';
			$mapNegocio['entidade'][$indice]['controle']['largura'] = $campo['campo_pk'] ?'10%' :'';
			$mapNegocio['entidade'][$indice]['controle']['ordem'] = $campo['campo_pk'] ? 1 : '';
			$mapNegocio['entidade'][$indice]['controle']['campoPersonalizado'] = '';
			
			$mapNegocio['entidade'][$indice]['persistente']['nome'] = $campo['campo'];
			$mapNegocio['entidade'][$indice]['persistente']['tipo'] = $tipoDeDado;
			$mapNegocio['entidade'][$indice]['persistente']['tamanho'] = $campo['tamanho'];
			$mapNegocio['entidade'][$indice]['persistente']['obrigatorio'] = '';
			$mapNegocio['entidade'][$indice]['persistente']['operadorDeBusca'] = 'igual';
			$mapNegocio['entidade'][$indice]['persistente']['chaveEstrangeira'] = $chaveEstrangeira;
			$mapNegocio['entidade'][$indice]['persistente']['ordem'] = $campo['campo_pk']? '1':'';
			
			$mapNegocio['entidade'][$indice]['inter']['nome'] = ucfirst(str_replace('_',' ',$campo['campo']));
			$mapNegocio['entidade'][$indice]['inter']['abreviacao'] = ucwords(str_replace('_',' ',$campo['campo']));
			$mapNegocio['entidade'][$indice]['inter']['descricao'] = $campo['descricao'];
			$mapNegocio['entidade'][$indice]['inter']['dominio'] = '';
		}
		$this->visualizacao->dados = $json->pegarJson(array($mapNegocio));
		$this->visualizacao->campos = $mapNegocio;
		$this->visualizacao->acesso = 'Carga de tabela do banco';
		$this->visualizacao->travarSugestaoDeNomesPersistente = 'true';
	}
}
?>
