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
			$this->visualizacao->mostrar();
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
		$this->visualizacao->menuPrincipal = '';
		$this->visualizacao->menuModulo = '';
		$this->visualizacao->menuPrograma = '';
		$this->visualizacao->descricaoDeAjuda = '';
		$this->visualizacao->action = '';
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
			if(!isset(controlePadrao::$estrutura[$entidade])){
				$mapeador = array();
				$xml = simplexml_load_file($arquivoXML);
				foreach($xml->propriedades->propriedade as $propriedade){
					$arValores = array();
					$idPropriedade = strval($propriedade['id']);
					if(isset($propriedade->dominio->opcao)){
						$arValores[''] = '&nbsp;';
						foreach($propriedade->dominio->opcao as $opcao){
							$arValores[strval($opcao['id'])] = $inter->pegarOpcao($idPropriedade,strval($opcao['id']));
						}
					}
					$mapeador[$idPropriedade] = array(
						'componente'	=> strval($propriedade->apresentacao['componente']	),
						'tamanho'		=> strval($propriedade['tamanho']	),
						'tipo'			=> strval($propriedade['tipo']	),
						'obrigatorio'	=> strval($propriedade['obrigatorio']	),
						'pesquisa'		=> strval($propriedade->apresentacao['pesquisa']	),
						'valores'		=> $arValores,
						'classeAssociativa'	=> strval($propriedade['classeAssociativa']		),
						'metodoLeitura'		=> strval($propriedade['metodoLeitura']		)
					);
					$mapeador[$idPropriedade]['listagem'] = false;
					if(isset($propriedade->apresentacao->listagem)){
						$mapeador[$idPropriedade]['listagem'] = true;
						$mapeador[$idPropriedade]['titulo']	= $inter->pegarPropriedade($idPropriedade,'abreviacao');
						$mapeador[$idPropriedade]['hyperlink'] = strval($propriedade->apresentacao->listagem['hyperlink']);
						$mapeador[$idPropriedade]['largura'] = strval($propriedade->apresentacao->listagem['tamanho']);
						$mapeador[$idPropriedade]['ordem'] = strval($propriedade->apresentacao->listagem['ordem']	);
						$mapeador[$idPropriedade]['campoPersonalizado'] = strval($propriedade->apresentacao->listagem['campoPersonalizado'] );
					}
				}
				controlePadrao::$estrutura[$entidade] = $mapeador;
			}
			return controlePadrao::$estrutura[$entidade];
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
		$visualizacao->tituloEspecifico =
			sprintf('%s - %s',$inter->pegarTitulo(),$inter->pegarTexto(	isset($_GET['c']) ? definicaoEntidade::funcionalidade($_GET['c']):	null));
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
		$this->visualizacao->menuPrincipal = new VMenu($this->montarMenuPrincipal(),'menu1','9999');
	}
	/**
	* Utiliza os itens montados para o menu do módulo e registra na visualização
	*/
	public function gerarMenuModulo(){
		$this->visualizacao->menuModulo = new VMenu($this->montarMenuModulo(),'menu2','9998');
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return array itens do menu do programa
	*/
	public function gerarMenuPrograma(){
		$this->visualizacao->menuPrograma = new VMenu($this->montarMenuPrograma(),'menu3','9997');
	}
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return array itens do menu principal
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
			return array();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuModulo
	* @return array itens do menu do modulo
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
			return array();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return array itens do menu do modulo
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
			return array();
		}
		catch(erro $e){
			throw $e;
		}
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
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	* @param visualizacao template de registro para visualizacao
	*/
	public static function montarApresentacaoVisual(negocio $negocio = null, visualizacao $visualizacao){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
						$visualizacao->$nome = $array[$valor];
					break;
					case($opcoes['classeAssociativa']):
						$array = controlePadrao::montarVetorDescritivo($opcoes['classeAssociativa']);
						$visualizacao->$nome = $array[$valor];
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
	* @param visualizacao template de registro para edição
	*/
	public static function montarApresentacaoEdicao(negocio $negocio, visualizacao $visualizacao){
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		foreach($estrutura as $nome => $opcoes){
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
			}
		}
		$visualizacao->enviar = VComponente::montar('enviar','enviar', $negocio->inter->pegarTexto('enviar'));
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	* @param [string] tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		try{
			$this->visualizacao->tituloEspecifico = sprintf('%s - %s',$this->inter->pegarTitulo(),$this->inter->pegarTexto(definicaoEntidade::funcionalidade($this)));
			switch($tipo){
				case('edicao'):
					self::montarApresentacaoEdicao($negocio,$this->visualizacao);
				break;
				case('visual'):
					self::montarApresentacaoVisual($negocio,$this->visualizacao);
				break;
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de utilização dos dados postados para a montagem do negocio
	* @param [negocio] objeto para preenchimento
	* @param array $dados
	*/
	public static function montarNegocio(negocio $negocio,$dados = null){
		try{
			$dados = $dados ? $dados : $_POST;
			$estrutura = controlePadrao::pegarEstrutura($negocio);
			$atributos = array_keys(get_class_vars(get_class($negocio)));
			foreach($dados as $campo => $valor){
				if(in_array($campo,$atributos)){
					$metodo = 'passar'.ucfirst($campo);
					$valor = self::obterValorDoComponenteHtmlPadrao($estrutura[$campo],$valor);
					self::passarValorPostadoParaNegocio($negocio, $metodo, $estrutura[$campo], $valor);
				}
			}
		}
		catch(erro $e){
			throw($e);
		}
	}
	/**
	* Método que obtém o valor do componente que possui o mesmo nome do atributo de negócio
	* @param [array] array de definição do atributo de negócio
	* @param [mixed] valor postado para o atributo de negócio
	* @return [mixed] valor a ser utilizado pelo atributo de negócio
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
					case($valor['data'] && $valor['hora']) : $valor = new TData(implode(' ',$valor)); break;
					case($valor['data']) : $valor = new TData($valor['data']); break;
					case($valor['hora']) : $valor = new TData(TData::hoje($valor['hora'])); break;
					default:
						$valor = null;
				}
			break;
		}
		return $valor;
	}
	/**
	* Método que passa o valor postado para o atributo da classe de negócio
	* @param [negocio] classe de negocio a ser montada
	* @param [string] nome do método para passar o valor do campo
	* @param [array] array de definição do atributo de negócio
	* @param [mixed] valor definido para o atributo de negócio
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
			default:
				$negocio->$metodo($valor);
		}
	}
}
?>