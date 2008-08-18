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
	* Metodo criado para especificar a estrutura da persistente
	* @param [st] caminho do arquivo
	*/
	public function mapearControle($arquivoXML){
		try{
			$entidade = definicaoEntidade::entidade($this);
			if(!isset(controlePadrao::$estrutura[$entidade])){
				$mapeador = array();
				switch(true){
					case !($arquivoXML):
					break;
					case !(is_file($arquivoXML)):
						throw new erroInclusao("Arquivo [$arquivoXML] inexistente!");
					break;
					case !(is_readable($arquivoXML)):
						throw new erroInclusao("Arquivo [$arquivoXML] sem permissão de leitura!");
					break;
					default:
						$xml = simplexml_load_file($arquivoXML);
						foreach($xml->propriedades->propriedade as $propriedade){
							$arValores = array();
							$idPropriedade = strval($propriedade['id']);
							if(isset($propriedade->dominio->opcao)){
								$arValores[''] = '&nbsp;';
								foreach($propriedade->dominio->opcao as $opcao){
									$arValores[strval($opcao['id'])] = $this->inter->pegarOpcao($idPropriedade,strval($opcao['id']));
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
								$mapeador[$idPropriedade]['titulo']	= $this->inter->pegarPropriedade($idPropriedade,'abreviacao');
								$mapeador[$idPropriedade]['hyperlink'] = strval($propriedade->apresentacao->listagem['hyperlink']);
								$mapeador[$idPropriedade]['largura'] = strval($propriedade->apresentacao->listagem['tamanho']);
								$mapeador[$idPropriedade]['ordem'] = strval($propriedade->apresentacao->listagem['ordem']	);
								$mapeador[$idPropriedade]['campoPersonalizado'] = strval($propriedade->apresentacao->listagem['campoPersonalizado'] );
							}
						}
					break;
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
	*/
	public function registrarInternacionalizacao(){
		$this->visualizacao->titulo		= $this->inter->pegarTituloSistema();
		$this->visualizacao->subtitulo	= $this->inter->pegarSubtituloSistema();
		$this->visualizacao->tituloEspecifico =
			sprintf('%s - %s',$this->inter->pegarTitulo(),$this->inter->pegarTexto(definicaoEntidade::funcionalidade($this)));
		$internacionalizacao = $this->inter->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade']))
		foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
			if(isset($propriedade['nome'])){
				$var = 'nome'.ucfirst($indice);
				$this->visualizacao->$var = strval($propriedade['nome']);
			}
			if(isset($propriedade['abreviacao'])){
				$var = 'abreviacao'.ucfirst($indice);
				$this->visualizacao->$var = $propriedade['abreviacao'];
			}
			if(isset($propriedade['descricao'])){
				$var = 'descricao'.ucfirst($indice);
				$this->visualizacao->$var = $propriedade['descricao'];
			}
			if(isset($propriedade['dominio'])){
				$var = 'dominio'.ucfirst($indice);
				$this->visualizacao->$var = $propriedade['dominio'];
			}
		}
		if(isset($internacionalizacao['texto']))
		foreach($internacionalizacao['texto'] as $indice => $texto){
			$var = 'texto'.ucfirst($indice);
			$this->visualizacao->$var = $texto;
		}
		if(isset($internacionalizacao['mensagem']))
		foreach($internacionalizacao['mensagem'] as $indice => $mensagem){
			$var = 'mensagem'.ucfirst($indice);
			$this->visualizacao->$var = $mensagem;
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
	public function montarVetorDescritivo($classe,$metodo = 'lerTodos'){
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
	*/
	public function montarApresentacaoVisual(negocio $negocio = null){
		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
		foreach($estrutura as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = $this->montarVetorDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
						$this->visualizacao->$nome = $array[$valor];
					break;
					case($opcoes['classeAssociativa']):
						$array = $this->montarVetorDescritivo($opcoes['classeAssociativa']);
						$this->visualizacao->$nome = $array[$valor];
					break;
					default:
						if(count($opcoes['valores'])){
							$this->visualizacao->$nome = $opcoes['valores'][$negocio->$pegarPropriedade()];
						}else{
							$this->visualizacao->$nome = $valor;
						}
				}
			}
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacaoEdicao(negocio $negocio){
		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
		foreach($estrutura as $nome => $opcoes){
			$pegarPropriedade = 'pegar'.ucfirst($nome);
			$valor = $negocio->$pegarPropriedade();
			if($opcoes['componente']){
				switch(true){
					case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
						$array = $this->montarVetorDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
						$this->visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$array);
					break;
					case($opcoes['classeAssociativa']):
						$array = $this->montarVetorDescritivo($opcoes['classeAssociativa']);
						$this->visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$array);
					break;
					default:
						$this->visualizacao->$nome = VComponente::montar($opcoes['componente'],$nome,$valor,null,$opcoes['valores']);
				}
			}
		}
		$this->visualizacao->enviar = VComponente::montar('enviar','enviar', $this->inter->pegarTexto('enviar'));
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
					$this->montarApresentacaoEdicao($negocio);
				break;
				case('visual'):
					$this->montarApresentacaoVisual($negocio);
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
	*/
	public function montarNegocio(negocio $negocio){
		try{
			$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
			$atributos = array_keys(get_class_vars(get_class($negocio)));
			foreach($_POST as $campo => $valor){
				if(in_array($campo,$atributos)){
					$metodo = 'passar'.ucfirst($campo);
					$valor = $this->obterValorDoComponenteHtmlPadrao($estrutura[$campo],$valor);
					$this->passarValorPostadoParaNegocio($negocio, $metodo, $estrutura[$campo], $valor);
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
	public function obterValorDoComponenteHtmlPadrao($campo,$valor){
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
	public function passarValorPostadoParaNegocio(negocio $negocio, $metodo, $campo, $valor){
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