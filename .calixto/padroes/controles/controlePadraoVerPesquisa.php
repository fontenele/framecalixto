<?php

/**
 * Classe de definição da camada de controle que apresenta uma tela de pesquisa
 * @package FrameCalixto
 * @subpackage Controle
 */
abstract class controlePadraoVerPesquisa extends controlePadrao {

	/**
	 * @var pagina pagina a ser listada
	 */
	public $pagina;

	/**
	 * @var coletor de dados que será utilizado para gerar a pesquisa
	 */
	public $filtro;
	
	/**
	 * Objeto de negócio utilizado para preencher a tela de pesquisa
	 * @var negocioPadrao
	 */
	public $negocio;
	
	/**
	 * @var array valores postados para a pesquisa
	 */
	public $parametros;

	/**
	 * Colecoes de negocios com os resultados da pesquisa
	 * @var array
	 */
	public static $colecoes;

	/**
	 * Método inicial do controle
	 */
	public function inicial() {
		$this->definirPagina();
		$this->definirParametros();
		$this->montarFiltro();
		if (controle::tipoResposta() == controle::xml)
			controle::responderXml($this->definirColecao()->xml());
		if (controle::tipoResposta() == controle::json)
			controle::responderJson($this->definirColecao()->json());
		$this->registrarInternacionalizacao($this, $this->visualizacao);
		$this->gerarMenus();
		$this->montarListagem($this->visualizacao, $this->definirColecao(), $this->pegarPagina());
		$this->montarApresentacaoPesquisa($this->negocio, $this->visualizacao,$this->parametros);
		parent::inicial();
		$this->finalizar();
	}

	/**
	 * Método executado para realizar finalização da pesquisa
	 */
	public function finalizar() {
		if ($this->sessao->tem('negocio'))
			$this->sessao->retirar('negocio');
	}

	/**
	 * Preenche os itens da propriedade menuPrograma
	 * @return colecaoPadraoMenu do menu do programa
	 */
	function montarMenuPrograma() {
		$menu = parent::montarMenuPrograma();
		$this->montarBotaoNovo($menu);
		$this->montarBotaoPesquisar($menu);
		$this->montarBotaoImpressao($menu);
		return $menu;
	}

	public function montarBotaoNovo($menu) {
		$novo = $this->inter->pegarTexto('botaoNovo');
		$menu->$novo = new VMenu($novo, sprintf("?c=%s", definicaoEntidade::controle($this, 'verEdicao')), 'icon-plus-sign icon-white');
		$menu->$novo->passar_classeLink('btn btn-success');
	}

	public function montarBotaoPesquisar($menu) {
		$pesquisar = $this->inter->pegarTexto('botaoPesquisar');
		$menu->$pesquisar = new VMenu($pesquisar, 'javascript:document.formulario.submit();', 'icon-search');
	}

	public function montarBotaoImpressao($menu) {
		try {
			$impressao = $this->inter->pegarTexto('botaoImpressao');
			arquivoClasse(definicaoEntidade::controle($this, 'verListagemPdf'));
			$menu->$impressao = new VMenu($impressao, sprintf('?c=%s', definicaoEntidade::controle($this, 'verListagemPdf')), 'icon-print');
		} catch (erroInclusao $e) {
			
		}
	}

	/**
	 * Retorna o nome da variável que irá segurar a página na sessão
	 * @return string
	 */
	protected function nomeDaPagina() {
		return 'pagina';
	}

	/**
	 * Retorna o nome da variável que irá segurar o filtro na sessão
	 * @return string
	 */
	protected function nomeDoFiltro() {
		return 'dadosPostagem';
	}

	/**
	 * Método que define a página que será exibida na pesquisa
	 */
	protected function definirPagina() {
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->pagina = ($this->sessao->tem($this->nomeDaPagina())) ? $this->sessao->pegar($this->nomeDaPagina()) : new pagina($mapeador['tamanhoPaginaListagem']);
		if (isset($_GET['pagina']))
			$this->pagina->passarPagina($_GET['pagina']);
		if (isset($_GET['tamanhoPagina']))
			$this->pagina->passarTamanhoPagina($_GET['tamanhoPagina']);
		$this->sessao->registrar($this->nomeDaPagina(), $this->pagina);
	}

	/**
	 * Método que define os parametros da pesquisa
	 */
	protected function definirParametros() {
		if ($_POST) {
			$this->parametros = $_POST;
			$this->sessao->registrar('parametros', $this->parametros);
		} else {
			$this->parametros = ($this->sessao->tem('parametros')) ? $this->sessao->pegar('parametros') : array();
		}
	}
	
	/**
	 * Método que monta o filtro para a pesquisa
	 */
	protected function montarFiltro(){
		$this->filtro = new coletor();
		$obNegocio = definicaoEntidade::negocio($this);
		$this->negocio = new $obNegocio();
		$this->montarNegocioFiltro($this->negocio);
		$this->filtro->coletar($this->negocio);
	}

	/**
	 * Monta o filtro do negocio passado com os dados postados
	 * @param negocioPadrao $negocio
	 */
	protected function montarNegocioFiltro(negocioPadrao $negocio) {
		$cEstrutura = $this->pegarEstrutura($negocio);
		$atributos = $negocio->__atributos();
		foreach ($this->parametros as $campo => $valor) {
			if (in_array($campo, $atributos) && isset($cEstrutura['campos'][$campo])) {
				$this->montarAtributoFiltro($negocio, $cEstrutura['campos'][$campo], $this->parametros[$campo]);
			}
		}
	}

	/**
	 * Monta o filtro do atributo do negócio
	 * @param negocioPadrao $negocio
	 * @param type $campo
	 * @param type $valor
	 */
	protected function montarAtributoFiltro(negocioPadrao $negocio, $campo, &$valor) {
		if (isset($valor['ini']) && isset($valor['fim'])) {
			$arCampo = $campo;
			$arCampo['operadorDeBusca'] = operador::maiorOuIgual;
			$this->montarAtributoFiltro($negocio, $arCampo, $valor['ini']);
			$arCampo['operadorDeBusca'] = operador::menorOuIgual;
			$this->montarAtributoFiltro($negocio, $arCampo, $valor['fim']);
		} else {
			$valor = $this->obterValorDoComponenteHtmlPadrao($campo, $valor);
			$valor = $this->tratarValorDoAtributo($campo, $valor);
			$operador = new operador();
			$operador->passarOperador($campo['operadorDeBusca']);
			$operador->passarRestricao(operador::restricaoE);
			$operador->passarValor($valor);
			$this->adicionarFiltro($negocio, $campo, $operador);
		}
	}

	/**
	 * Realiza o tratamento necessário do valor do atributo de negócio
	 * @param array $campo
	 * @param mixed $valor
	 * @return mixed
	 */
	protected function tratarValorDoAtributo($campo, $valor) {
		if (!$valor)
			return;
		switch (strtolower($campo['tipo'])) {
			case 'tmoeda':
				$valor = (new TMoeda($valor));
				break;
			case 'tnumerico':
				$valor = (new TNumerico($valor));
				break;
			case 'data':
				$valor = (new TData($valor));
				break;
			case 'ttelefone':
				$valor = (new TTelefone($valor));
				break;
			case 'tcep':
				$valor = (new TCep($valor));
				break;
			case 'tcpf':
				$valor = (new TCpf($valor));
				break;
			case 'tcnpj':
				$valor = (new TCnpj($valor));
				break;
		}
		return $valor;
	}

	/**
	 * Método que adiciona o filtro no objeto de negócio
	 * @param negocio $negocio
	 * @param array $campo
	 * @param operador $operador
	 */
	protected function adicionarFiltro(negocio $negocio, $campo, $operador) {
		$negocio->{'filtrar' . ucfirst($campo['atributo'])}($operador);
	}

	/**
	 * Método de criação da coleção a ser listada
	 * @return colecaoPadraoNegocio coleção a ser listada
	 */
	protected function definirColecao() {
		$this->filtro->executar($this->pegarPagina());
		return $this->filtro->pegar(get_class($this->negocio));
	}

	/**
	 * Método de registro da internacionalização
	 * @param controle $entidade
	 * @param visualizacao $visualizacao
	 */
	public static function registrarInternacionalizacao($entidade, $visualizacao) {
		parent::registrarInternacionalizacao($entidade, $visualizacao);
		$inter = definicaoEntidade::internacionalizacao($entidade);
		$inter = new $inter();
		$visualizacao->descricaoDeAjuda = $inter->pegarTexto('ajudaPesquisa');
		$visualizacao->tituloListagem = $inter->pegarTexto('tituloListagem');
		$inter = definicaoEntidade::internacionalizacao($entidade);
		$estrutura = self::pegarEstrutura($entidade);
		$inter = new $inter();
		$internacionalizacao = $inter->pegarInternacionalizacao();
		if (isset($internacionalizacao['propriedade']))
			foreach ($internacionalizacao['propriedade'] as $indice => $propriedade) {
				if (in_array($estrutura['campos'][$indice]['tipo'], array('data', 'tmoeda', 'tnumerico'))) {
					if (isset($propriedade['nome'])) {
						$var = 'nome' . ucfirst($indice);
						$visualizacao->{$var . '-ini'} = strval($propriedade['nome']);
					}
					if (isset($propriedade['abreviacao'])) {
						$var = 'abreviacao' . ucfirst($indice);
						$visualizacao->{$var . '-ini'} = $propriedade['abreviacao'];
					}
					if (isset($propriedade['descricao'])) {
						$var = 'descricao' . ucfirst($indice);
						$visualizacao->{$var . '-ini'} = $propriedade['descricao'];
					}
				}
			}
	}

	/**
	 * metodo de apresentação do negocio
	 * @param negocio objeto para a apresentação
	 * @param visualizacao template de registro para edição
	 * @param grupo que sera montada a visualizacao
	 */
	public static function montarApresentacaoPesquisa(negocio $negocio, visualizacao $visualizacao, $dados = array(), $grupo = null) {
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach ($estrutura['campos'] as $nome => $opcoes) {
			$pegarPropriedade = 'pegar' . ucfirst($nome);
			$dataHtml = 'data-val-' . $nome;
			$valor = isset($dados[$nome]) ? $dados[$nome] : null;
			$grupoNome = $grupo ? $grupo . '_' . $nome : $nome;
			$name = $grupo ? "{$grupo}[{$nome}]" : $nome;
			if ($opcoes['componente']) {
				switch (true) {
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa'], $opcoes['metodoLeitura']);
						$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'], $name, $valor, null, $array);
						break;
					case($opcoes['classeAssociativa']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa']);
						$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'], $name, $valor, null, $array);
						break;
					default:
						if (in_array($opcoes['tipo'], array('data', 'tmoeda', 'tnumerico'))) {
							$visualizacao->$grupoNome = VComponenteIntervalo::montar($opcoes['componente'], $name, $valor, null, $opcoes['valores']);
						} else {
							$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'], $name, $valor, null, $opcoes['valores']);
						}
				}
				if ($visualizacao->$grupoNome instanceof VInput && $opcoes['tamanho']) {
					$visualizacao->$grupoNome->passarMaxlength($opcoes['tamanho']);
				}
				$visualizacao->$grupoNome->passarTitle($negocio->pegarInter()->pegarPropriedade($nome, 'descricao'));
				$visualizacao->$grupoNome->propriedades[$dataHtml] = null;
				if ($visualizacao->$grupoNome instanceof VInput) {
					if (($opcoes['tamanho'] + 2) > 60) {
						$visualizacao->$grupoNome->passarSize(60);
					} else {
						$visualizacao->$grupoNome->passarSize(($opcoes['tamanho'] + 2));
					}
				}
				if ($opcoes['atributo'] != $negocio->nomeChave()) {
					if ($visualizacao->{' pesquisa '} && $opcoes['pesquisa']) {
						$visualizacao->_tpl_vars['componentes padroes'][] = $visualizacao->$grupoNome;
					}
				}
			}
		}
	}

	/**
	 * Método de apresentação da listagem
	 * @param visualizacao $visualizacao
	 * @param colecao $colecao
	 * @param pagina $pagina
	 */
	public static function montarListagem(visualizacao $visualizacao, colecao $colecao, pagina $pagina, $entidade = null) {
		$visualizacao->listagem = new VListaPaginada($colecao, $pagina, $entidade);
	}

}

?>