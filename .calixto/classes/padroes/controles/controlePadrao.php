<?php
/**
* Classe de definição da camada de controle
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadrao extends controle{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		try{
			$this->visualizacaoPadrao->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
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
						if(isset($propriedade->dominio->opcao)){
							$arValores[''] = '&nbsp;';
							foreach($propriedade->dominio->opcao as $opcao){
								$arValores[caracteres($opcao['id'])] = $this->internacionalizacao->pegarOpcao(caracteres($propriedade['id']),caracteres($opcao['id']));
							}
						}
						$mapeador[caracteres($propriedade['id'])] = array(
							'componente'	=> caracteres($propriedade->apresentacao['componente']	),
							'tamanho'		=> caracteres($propriedade['tamanho']	), 
							'tipo'			=> caracteres($propriedade['tipo']	),
							'obrigatorio'	=> caracteres($propriedade['obrigatorio']	),
							'pesquisa'		=> caracteres($propriedade->apresentacao['pesquisa']	),
							'valores'		=> $arValores,
							'classeAssociativa'	=> caracteres($propriedade['classeAssociativa']		),
							'metodoLeitura'		=> caracteres($propriedade['metodoLeitura']		)
						);
					}
				break;
			}
			return $mapeador;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de registro da internacionalização
	*/
	public function registrarInternacionalizacao(){
		$this->visualizacaoPadrao->titulo		= $this->internacionalizacao->pegarTituloSistema();
		$this->visualizacaoPadrao->subtitulo	= $this->internacionalizacao->pegarSubtituloSistema();
		$this->visualizacaoPadrao->tituloEspecifico = 
			sprintf('%s - %s',$this->internacionalizacao->pegarTitulo(),$this->internacionalizacao->pegarTexto(definicaoEntidade::funcionalidade($this)));
		$internacionalizacao = $this->internacionalizacao->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade']))
		foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
			if(isset($propriedade['nome'])){
				$var = 'nome'.ucfirst($indice);
				$this->visualizacaoPadrao->$var = strval($propriedade['nome']);
			}
			if(isset($propriedade['abreviacao'])){
				$var = 'abreviacao'.ucfirst($indice);
				$this->visualizacaoPadrao->$var = $propriedade['abreviacao'];
			}
			if(isset($propriedade['descricao'])){
				$var = 'descricao'.ucfirst($indice);
				$this->visualizacaoPadrao->$var = $propriedade['descricao'];
			}
			if(isset($propriedade['dominio'])){
				$var = 'dominio'.ucfirst($indice);
				$this->visualizacaoPadrao->$var = $propriedade['dominio'];
			}
		}
		if(isset($internacionalizacao['texto']))
		foreach($internacionalizacao['texto'] as $indice => $texto){
			$var = 'texto'.ucfirst($indice);
			$this->visualizacaoPadrao->$var = $texto;
		}
		if(isset($internacionalizacao['mensagem']))
		foreach($internacionalizacao['mensagem'] as $indice => $mensagem){
			$var = 'mensagem'.ucfirst($indice);
			$this->visualizacaoPadrao->$var = $mensagem;
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
		$this->visualizacaoPadrao->menuPrincipal = new VMenu($this->montarMenuPrincipal(),'menu1','9999');
	}
	/**
	* Utiliza os itens montados para o menu do módulo e registra na visualização
	*/
	public function gerarMenuModulo(){
		$this->visualizacaoPadrao->menuModulo = new VMenu($this->montarMenuModulo(),'menu2','9998');
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return [array] itens do menu do programa
	*/
	public function gerarMenuPrograma(){
		$this->visualizacaoPadrao->menuPrograma = new VMenu($this->montarMenuPrograma(),'menu3','9997');
	}
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return [array] itens do menu principal
	*/
	public function montarMenuPrincipal(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeMenu = $definicoes->xpath('//controleDeMenu');
			$classe = strval($controleDeMenu[0]['classe']);
			$metodo = strval($controleDeMenu[0]['metodoMenuSite']);
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
	* Preenche os itens da propriedade menuModulo
	* @return [array] itens do menu do modulo
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
	* @return [array] itens do menu do modulo
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
	public function montarArrayDescritivo($classe,$metodo = 'lerTodos'){
		$array = array();
		$array [''] = '&nbsp;';
		$classe = new $classe();
		if(is_subclass_of($classe,'negocio')){
			$colecao = $classe->$metodo();
			while($negocio = $colecao->avancar()){
				$array[$negocio->valorChave()] = $negocio->valorDescricao();
			}
		}
		return $array;
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao($negocio){
		try{
			$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
//			$this->visualizacaoPadrao->tituloEspecifico = sprintf('%s - %s',$this->internacionalizacao->pegarTitulo(),$this->internacionalizacao->pegarTexto(definicaoEntidade::funcionalidade($this)));
			foreach($estrutura as $nome => $opcoes){
				$pegarPropriedade = 'pegar'.ucfirst($nome);
				if($opcoes['componente']){ 
					switch(true){
						case($opcoes['classeAssociativa'] && $opcoes['metodoLeitura']):
							$array = $this->montarArrayDescritivo($opcoes['classeAssociativa'],$opcoes['metodoLeitura']);
							$this->visualizacaoPadrao->$nome = VComponente::montar($opcoes['componente'],$nome,$negocio->$pegarPropriedade(),null,$array);
						break;
						case($opcoes['classeAssociativa']):
							$array = $this->montarArrayDescritivo($opcoes['classeAssociativa']);
							$this->visualizacaoPadrao->$nome = VComponente::montar($opcoes['componente'],$nome,$negocio->$pegarPropriedade(),null,$array);
						break;
						default:
							$this->visualizacaoPadrao->$nome = VComponente::montar($opcoes['componente'],$nome,$negocio->$pegarPropriedade(),null,$opcoes['valores']);
					}
				}
			}
			$this->visualizacaoPadrao->enviar = VComponente::montar('enviar','enviar', $this->internacionalizacao->pegarTexto('enviar'));
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
