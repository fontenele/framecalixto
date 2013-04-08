<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadrao extends controle{
	private static $estrutura;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		try{
			if($this->requisicaoAjax()){
				$this->visualizacao->mostrarParaAjax();
			}else{
				$this->visualizacao->mostrar();
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de criação da visualizacao
	*/
	public function criarVisualizacaoPadrao(){
		$this->visualizacao = new visualizacaoPadrao($this);
		$this->visualizacao->baseUri            = false;
		//$this->visualizacao->baseUri            = $_SERVER['SCRIPT_NAME'];
		$this->visualizacao->nomeLogado			= sessaoSistema::tem('usuario') ? sessaoSistema::pegar('usuario')->valorDescricao() : 'Visitante';
		$this->visualizacao->estaLogado			= sessaoSistema::tem('usuario') ? true : false;
		$this->visualizacao->comunicacaoSistema = sessaoSistema::tem('comunicacao') ? new VComunicacao(sessaoSistema::retirar('comunicacao')) : '';
		$this->visualizacao->requisicaoAjax		= controle::requisicaoAjax();
		$this->visualizacao->action				= '';
		$this->visualizacao->mensagemErroData	= '';
		$this->visualizacao->mensagemErroDia	= '';
		$this->visualizacao->mensagemErroMes	= '';
		$this->visualizacao->mensagemErroAno	= '';
		$this->visualizacao->mensagemErroHora	= '';
		$this->visualizacao->mensagemErroEmail	= '';
		$this->visualizacao->menuModulo			= '';
		$this->visualizacao->menuPrincipal		= '';
		$this->visualizacao->menuPrograma		= '';
		$this->visualizacao->titulo				= '';
		$this->visualizacao->tituloEspecifico	= '';
		$this->visualizacao->descricaoDeAjuda	= '';
		$this->visualizacao->cssExtra			= '';
		$this->visualizacao->jsExtra			= '';
		$this->visualizacao->cssGlobal			= is_file($arquivo = definicaoPasta::tema().'/principal.css')?$arquivo:'';
		$this->visualizacao->cssEntidade		= is_file($arquivo = definicaoPasta::css($this).'principal.css')?$arquivo:'';
		$this->visualizacao->cssLocal			= is_file($arquivo = definicaoPasta::css($this).get_class($this).'.css')?$arquivo:'';
		$this->visualizacao->jsTema				= is_file($arquivo = definicaoPasta::tema().'/configurador.js')?$arquivo:'';
		$this->visualizacao->jsEntidade			= is_file($arquivo = definicaoPasta::js($this).'principal.js')?$arquivo:'';
		$this->visualizacao->jsLocal			= is_file($arquivo = definicaoPasta::js($this).get_class($this).'.js')?$arquivo:'';
		$this->visualizacao->dirTema			= definicaoPasta::tema();
		$this->visualizacao->{' pesquisa '}		= ($this instanceof controlePadraoPesquisa);
		$this->visualizacao->{' edicao '}		= ($this instanceof controlePadraoVerEdicao);
	}
	/**
	* Método que retorna o negócio referente ao controle
	*/
	public function pegarNegocio(){
		try{
			$negocio = definicaoEntidade::negocio($this);
			return new $negocio();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que retorna o nome do controle atual
	* @return string
	*/
	public static function pegarNomeControle(){
		return controle::controleAcessado();
	}
	/**
	* Retorna a estrutura do controle definido em seu xml
	* @param controle $entidade
	* @return array
	*/
	public static function pegarEstrutura($entidade){
		try{
			$entidadeInternacionalizacao = definicaoEntidade::internacionalizacao($entidade);
			$entidade = definicaoEntidade::entidade($entidade);
			$inter = new $entidadeInternacionalizacao();
			$arquivoXML = definicaoArquivo::pegarXmlEntidade($inter);
			if(isset(controlePadrao::$estrutura[$entidade])){
				return controlePadrao::$estrutura[$entidade];
			}else{
				$mapeador = array();
				$xml = simplexml_load_file($arquivoXML);
				$mapeador['tamanhoPaginaListagem'] = strval($xml['tamanhoPaginaListagem']) ? (int) strval($xml['tamanhoPaginaListagem']) : 10;
				foreach($xml->propriedades->propriedade as $propriedade){
					$arValores = array();
					$idPropriedade = strval($propriedade['id']);
					if(isset($propriedade->dominio->opcao)){
						$arValores[''] = '&nbsp;';
						foreach($propriedade->dominio->opcao as $opcao){
							$arValores[strval($opcao['id'])] = $inter->pegarOpcao($idPropriedade,strval($opcao['id']));
						}
					}
					$mapeador['campos'][$idPropriedade] = array(
						'componente'	=> strval($propriedade->apresentacao['componente']	),
						'tamanho'		=> strval($propriedade['tamanho']	),
						'tipo'			=> strval($propriedade['tipo']	),
						'obrigatorio'	=> strval($propriedade['obrigatorio']	),
						'pesquisa'		=> (caracteres::RetiraAcentos(strtolower(strval($propriedade->apresentacao['pesquisa']))) == 'nao') ? false : true ,
						'edicao'		=> (caracteres::RetiraAcentos(strtolower(strval($propriedade->apresentacao['edicao']))) == 'nao') ? false : true ,
						'valores'		=> $arValores,
						'classeAssociativa'	=> strval($propriedade['classeAssociativa']		),
						'metodoLeitura'		=> strval($propriedade['metodoLeitura']		)
					);
					$mapeador['campos'][$idPropriedade]['listagem'] = false;
					if(isset($propriedade->apresentacao->listagem)){
						$mapeador['campos'][$idPropriedade]['listagem'] = true;
						$mapeador['campos'][$idPropriedade]['titulo']	= $inter->pegarPropriedade($idPropriedade,'abreviacao');
						$mapeador['campos'][$idPropriedade]['hyperlink'] = strval($propriedade->apresentacao->listagem['hyperlink']);
						$mapeador['campos'][$idPropriedade]['largura'] = strval($propriedade->apresentacao->listagem['tamanho']);
						$mapeador['campos'][$idPropriedade]['ordem'] = strval($propriedade->apresentacao->listagem['ordem']	);
						$mapeador['campos'][$idPropriedade]['campoPersonalizado'] = strval($propriedade->apresentacao->listagem['campoPersonalizado'] );
					}
				}
				return controlePadrao::$estrutura[$entidade] = $mapeador;
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de registro da internacionalização
	* @param controle $entidade
	* @param visualizacao $visualizacao
	*/
	public static function registrarInternacionalizacao($entidade,$visualizacao){
		$inter = definicaoEntidade::internacionalizacao($entidade);
		$entidade = definicaoEntidade::entidade($entidade);
		$inter = new $inter();
		
		$visualizacao->titulo		= $inter->pegarTituloSistema();
		$visualizacao->subtitulo	= $inter->pegarSubtituloSistema();
        if($inter->pegarTitulo()){
		$visualizacao->tituloEspecifico =
			sprintf(
                '%s - %s',
                $inter->pegarTitulo(),
                $inter->pegarTexto(	definicaoEntidade::funcionalidade(controle::controleAcessado()) )
            );
        }else{
    		$visualizacao->tituloEspecifico = $inter->pegarTexto(definicaoEntidade::funcionalidade(controle::controleAcessado()));
        }
		$internacionalizacao = $inter->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade']))
		foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
			if(isset($propriedade['nome'])){
				$var = 'nome'.ucfirst($indice);
				$visualizacao->$var = strval($propriedade['nome']);
			}
			if(isset($propriedade['abreviacao'])){
				$var = 'abreviacao'.ucfirst($indice);
				$visualizacao->$var = $propriedade['abreviacao'];
			}
			if(isset($propriedade['descricao'])){
				$var = 'descricao'.ucfirst($indice);
				$visualizacao->$var = $propriedade['descricao'];
			}
			if(isset($propriedade['dominio'])){
				$var = 'dominio'.ucfirst($indice);
				$visualizacao->$var = $propriedade['dominio'];
			}
		}
		if(isset($internacionalizacao['texto']))
		foreach($internacionalizacao['texto'] as $indice => $texto){
			$var = 'texto'.ucfirst($indice);
			$visualizacao->$var = $texto;
		}
		if(isset($internacionalizacao['mensagem']))
		foreach($internacionalizacao['mensagem'] as $indice => $mensagem){
			$var = 'mensagem'.ucfirst($indice);
			$visualizacao->$var = $mensagem;
		}
	}
	/**
	* Método de registro da internacionalização
	* @param controle $entidade
	* @param visualizacao $visualizacao
	* @param string $grupo
	*/
	public static function registrarInternacionalizacaoAgrupada($entidade,$visualizacao,$grupo){
		$inter = definicaoEntidade::internacionalizacao($entidade);
		$entidade = definicaoEntidade::entidade($entidade);
		$inter = new $inter();

		$visualizacao->{"{$grupo}_titulo"}		= $inter->pegarTituloSistema();
		$visualizacao->{"{$grupo}_subtitulo"}	= $inter->pegarSubtituloSistema();
        if($inter->pegarTitulo()){
		$visualizacao->{"{$grupo}_tituloEspecifico"} =
			sprintf(
                '%s - %s',
                $inter->pegarTitulo(),
                $inter->pegarTexto(definicaoEntidade::funcionalidade(controle::controleAcessado()))
            );
        }else{
    		$visualizacao->{"{$grupo}_tituloEspecifico"} =
				$inter->pegarTexto(definicaoEntidade::funcionalidade(controle::controleAcessado()));
        }
		$internacionalizacao = $inter->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade']))
		foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
			if(isset($propriedade['nome'])){
				$var = "{$grupo}_nome".ucfirst($indice);
				$visualizacao->$var = strval($propriedade['nome']);
			}
			if(isset($propriedade['abreviacao'])){
				$var = "{$grupo}_abreviacao".ucfirst($indice);
				$visualizacao->$var = $propriedade['abreviacao'];
			}
			if(isset($propriedade['descricao'])){
				$var = "{$grupo}_descricao".ucfirst($indice);
				$visualizacao->$var = $propriedade['descricao'];
			}
			if(isset($propriedade['dominio'])){
				$var = "{$grupo}_dominio".ucfirst($indice);
				$visualizacao->$var = $propriedade['dominio'];
			}
		}
		if(isset($internacionalizacao['texto']))
		foreach($internacionalizacao['texto'] as $indice => $texto){
			$var = "{$grupo}_texto".ucfirst($indice);
			$visualizacao->$var = $texto;
		}
		if(isset($internacionalizacao['mensagem']))
		foreach($internacionalizacao['mensagem'] as $indice => $mensagem){
			$var = "{$grupo}_mensagem".ucfirst($indice);
			$visualizacao->$var = $mensagem;
		}
	}
	/**
	* Preenche os menus do controle
	*/
	public function gerarMenus(){
		$this->gerarMenuPrincipal();
		$this->gerarMenuModulo();
		$this->gerarMenuPrograma();
	}
	/**
	* Utiliza os itens montados para o menu principal e registra na visualização
	*/
	public function gerarMenuprincipal(){
		$arMenu = $this->montarMenuPrincipal();
		if($arMenu){
			$this->visualizacao->menuPrincipal = "<div class='fc-menu-sistema'>{$arMenu}</div>";
		}else{
			$this->visualizacao->menuPrincipal = '';
		}
	}
	/**
	* Utiliza os itens montados para o menu do módulo e registra na visualização
	*/
	public function gerarMenuModulo(){
		// $this->visualizacao->menuModulo = null;//new VMenu($this->montarMenuModulo(),'fc-menu-modulo','9998');
        $arMenu = $this->montarMenuModulo();
		if($arMenu){
			$this->visualizacao->menuModulo = "<div class='fc-menu-modulo' >{$arMenu}</div>";
		}else{
			$this->visualizacao->menuModulo = '';
		}
	}
	/**
	* Utiliza os itens montados para o menu do programa e registra na visualização
	*/
	public function gerarMenuPrograma(){
		$menu = $this->montarMenuPrograma();
		
		if($menu && $menu instanceof colecaoPadraoMenu) {
			while($menuItem = $menu->avancar()) {
				$menuItem->passar_classe('fc-menu-item');
			}
		}
		switch(true){
			case is_array($menu):
				$menu = new VMenu($menu);
				$this->visualizacao->menuPrograma = "<div class='fc-menu-programa'>{$menu->_coMenu}</div>";
			break;
			case $menu instanceof colecaoPadraoMenu:
				$this->visualizacao->menuPrograma = "<div class='fc-menu-programa'>{$menu}</div>";
			break;
			default:
				$this->visualizacao->menuPrograma = '';
		}
	}
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return colecaoPadraoMenu do menu principal
	*/
	public function montarMenuPrincipal(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeMenu = $definicoes->xpath('//controleDeMenu');
			$classe = strval($controleDeMenu[0]['classe']);
			$metodo = strval($controleDeMenu[0]['metodoMenuSite']);
			if($classe && $metodo){
				$classe = new $classe();
				$return = $classe->$metodo(get_class($this));
				return $return ;
			}
			return new colecaoPadraoMenu();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuModulo
	* @return colecaoPadraoMenu do menu do modulo
	*/
	public function montarMenuModulo(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeMenu = $definicoes->xpath('//controleDeMenu');
			$classe = strval($controleDeMenu[0]['classe']);
			$metodo = strval($controleDeMenu[0]['metodoMenuSistema']);
			if($classe && $metodo){
				$classe = new $classe();
				return $classe->$metodo(get_class($this));
			}
			return new colecaoPadraoMenu();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return colecaoPadraoMenu do menu do programa
	*/
	public function montarMenuPrograma(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeMenu = $definicoes->xpath('//controleDeMenu');
			$classe = strval($controleDeMenu[0]['classe']);
			$metodo = strval($controleDeMenu[0]['metodoMenuPrograma']);
			if($classe && $metodo){
				$classe = new $classe();
				return $classe->$metodo(get_class($this));
			}
			return new colecaoPadraoMenu();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	 * Adiciona um VMenu na colecaoPadraoMenu
	 * @param colecaoPadraoMenu $menu
	 * @param string $item nome do item no menu
	 * @param string $link apontamento do item
	 * @param string $img icone do item no menu
	 */
	public static function adicionarItemMenu($menu, $item, $link = '#', $img = null){
		$menu->$item = new VMenu($item, $link, $img);
	}
	
	/**
	* Método montador de array descritivo
	* Monta um array [chave]=>descricao de uma coleção de objetos de negocio
	*/
	public static function montarVetorDescritivo($classe,$metodo = 'lerTodos'){
		if(is_subclass_of($classe,'negocio')){
			$classe = new $classe();
			$colecao = $classe->$metodo();
			return $colecao->gerarVetorDescritivo('&nbsp;');
		}else{
			return $array [''] = '&nbsp;';
		}
	}
	/**
	 * Retorna o valor descritivo de uma instancia de negocio
	 * @param string $classe
	 * @param string $chave
	 * @return string
	 */
	public static function pegarDescricaoNegocio($classe,$chave){
		$negocio = new $classe();
		return $negocio->ler($chave)->valorDescricao();
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para visualizacao
	*/
	public static function montarApresentacaoVisual(negocio $negocio, visualizacao $visualizacao){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura['campos'] as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa']):
						$visualizacao->$nome = controlePadrao::pegarDescricaoNegocio($opcoes['classeAssociativa'], $valor);
					break;
					default:
						if(count($opcoes['valores'])){
							$visualizacao->$nome = $opcoes['valores'][$negocio->$pegarPropriedade()];
						}else{
							$visualizacao->$nome = $valor;
						}
				}
			}
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para visualizacao
	* @param grupo que sera montada a visualizacao
	*/
	public static function montarApresentacaoAgrupadaVisual(negocio $negocio, visualizacao $visualizacao, $grupo){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura['campos'] as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			$grupoNome = $grupo.'_'.$nome;
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa']):
						$visualizacao->$grupoNome = controlePadrao::pegarDescricaoNegocio($opcoes['classeAssociativa'], $valor);
					break;
					default:
						if(count($opcoes['valores'])){
							$visualizacao->$grupoNome = $opcoes['valores'][$negocio->$pegarPropriedade()];
						}else{
							$visualizacao->$grupoNome = $valor;
						}
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
	public static function montarApresentacaoAgrupadaDeEdicao(negocio $negocio, visualizacao $visualizacao, $grupo){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura['campos'] as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			$grupoNome = $grupo.'_'.$nome;
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
						$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'],"{$grupo}[{$nome}]",$valor,null,$array);
					break;
					case($opcoes['classeAssociativa']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa']);
						$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'],"{$grupo}[{$nome}]",$valor,null,$array);
					break;
					default:
						$visualizacao->$grupoNome = VComponente::montar($opcoes['componente'],"{$grupo}[{$nome}]",$valor,null,$opcoes['valores']);
				}
				if ($visualizacao->$grupoNome instanceof VInput && $opcoes['tamanho']) {
					$visualizacao->$grupoNome->passarMaxlength($opcoes['tamanho']);
				}
				$visualizacao->$grupoNome->passarTitle($negocio->pegarInter()->pegarPropriedade($nome,'descricao'));
				if($visualizacao->$grupoNome instanceof VInput ){
					if(($opcoes['tamanho'] + 2) > 60){
						$visualizacao->$grupoNome->passarSize(60);
					}else{
						$visualizacao->$grupoNome->passarSize(($opcoes['tamanho'] + 2));
					}
				}
			}
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para edição
	*/
	public static function montarApresentacaoEdicao(negocio $negocio, visualizacao $visualizacao){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura['campos'] as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
						$visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$array);
					break;
					case($opcoes['classeAssociativa']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa']);
						$visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$array);
					break;
					default:
						$visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$opcoes['valores']);
				}
				if ($visualizacao->$nome instanceof VInput && $opcoes['tamanho']) {
					$visualizacao->$nome->passarMaxlength($opcoes['tamanho']);
				}
				$visualizacao->$nome->passarTitle($negocio->pegarInter()->pegarPropriedade($nome,'descricao'));
				if($visualizacao->$nome instanceof VInput ){
					if(($opcoes['tamanho'] + 2) > 60){
						$visualizacao->$nome->passarSize(60);
					}else{
						$visualizacao->$nome->passarSize(($opcoes['tamanho'] + 2));
					}
				}
				if($nome != $negocio->nomeChave()) {
					if($visualizacao->{' pesquisa '} && $opcoes['pesquisa']){
						$visualizacao->_tpl_vars['componentes padroes'][] = $visualizacao->$nome;
					}
					if($visualizacao->{' edicao '} && $opcoes['edicao']){
						$visualizacao->_tpl_vars['componentes padroes'][] = $visualizacao->$nome;
					}
				}
			}
		}
		$visualizacao->enviar = VComponente::montar('enviar','enviar', $negocio->pegarInter()->pegarTexto('enviar'));
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param string tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		try{
			//$this->visualizacao->tituloEspecifico = sprintf('%s - %s',$this->inter->pegarTitulo(),$this->inter->pegarTexto(definicaoEntidade::funcionalidade($this)));
			switch($tipo){
				case('edicao'):
					$this->montarApresentacaoEdicao($negocio,$this->visualizacao);
				break;
				case('visual'):
					$this->montarApresentacaoVisual($negocio,$this->visualizacao);
				break;
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de utilização dos dados postados para a montagem do negocio
	* @param negocio objeto para preenchimento
	* @param array $dados
	*/
	public static function montarNegocio(negocio $negocio,$dados = null){
		try{
			$dados = $dados ? $dados : $_POST;
			$estrutura = controlePadrao::pegarEstrutura($negocio);
			$atributos = $negocio->__atributos();
			foreach($dados as $campo => $valor){
				if(in_array($campo,$atributos) && isset($estrutura['campos'][$campo])){
					$metodo = 'passar'.ucfirst($campo);
					$valor = self::obterValorDoComponenteHtmlPadrao($estrutura['campos'][$campo],$valor);
					self::passarValorPostadoParaNegocio($negocio, $metodo, $estrutura['campos'][$campo], $valor);
				}
			}
		}
		catch(erro $e){
			throw($e);
		}
	}
	/**
	* Método que obtém o valor do componente que possui o mesmo nome do atributo de negócio
	* @param array array de definição do atributo de negócio
	* @param mixed valor postado para o atributo de negócio
	* @return mixed valor a ser utilizado pelo atributo de negócio
	*/
	public static function obterValorDoComponenteHtmlPadrao($campo,$valor){
		switch(strtolower($campo['componente'])){
			case 'data':
				$valor = new TData($valor);
			break;
			case 'hora':
				$valor = TData::hoje($valor);
			break;
			case 'data e hora':
				switch(true){
					case($valor['data'] && $valor['hora']) : $valor = new TDataHora(implode(' ',$valor)); break;
					case($valor['data']) : $valor = new TDataHora($valor['data']); break;
					case($valor['hora']) : $valor = new TDataHora(TData::hoje($valor['hora'])); break;
					default:
						$valor = null;
				}
			break;
		}
		return $valor;
	}
	/**
	* Método que passa o valor postado para o atributo da classe de negócio
	* @param negocio classe de negocio a ser montada
	* @param string nome do método para passar o valor do campo
	* @param array array de definição do atributo de negócio
	* @param mixed valor definido para o atributo de negócio
	*/
	public static function passarValorPostadoParaNegocio(negocio $negocio, $metodo, $campo, $valor){
		if(!$valor) return;
		switch(strtolower($campo['tipo'])){
			case 'tmoeda':
				$negocio->$metodo(new TMoeda($valor));
			break;
			case 'tnumerico':
				$negocio->$metodo(new TNumerico($valor));
			break;
			case 'data':
				$negocio->$metodo(new TData($valor));
			break;
			case 'ttelefone':
				$negocio->$metodo(new TTelefone($valor));
			break;
			case 'tcep':
				$negocio->$metodo(new TCep($valor));
			break;
			case 'tdocumentopessoal':
				$negocio->$metodo(new TDocumentoPessoal($valor));
			break;
			case 'tcnpj':
				$negocio->$metodo(new TCnpj($valor));
			break;
			default:
				$negocio->$metodo($valor);
		}
	}
}
?>