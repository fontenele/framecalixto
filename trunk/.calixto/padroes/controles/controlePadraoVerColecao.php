<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
abstract class controlePadraoVerColecao extends controlePadrao{
	/**
	 * Nome da subClasse de negocio
	 *
	 * @var string
	 */
	protected $subClasse;
	/**
	 * SubColecao de negocioPadrao
	 *
	 * @var colecaoNegocioPadrao
	 */
	protected $subColecao;
	/**
	 * SubColecao de negocioPadrao oposta a classe de negócio atual
	 *
	 * @var string
	 */
	protected $colecaoOposta;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirNegocio();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->montarApresentacao($this->negocio);
		parent::inicial();
	}
	/**
	* Método criado para definir o objeto de negócio a ser apresentado
	*/
	public function definirNegocio(){
		$this->negocio = $this->pegarNegocio();
		switch(true){
			case isset($_GET['chave']):
				$this->sessao->registrar('negocio',$this->negocio->ler($_GET['chave']));
			break;
			case $this->sessao->tem('negocio'):
				$this->negocio = $this->sessao->pegar('negocio');
			break;
		}
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$gravar = $this->inter->pegarTexto('botaoGravar');
		$listagem = $this->inter->pegarTexto('botaoListagem');
		$menu->$gravar = new VMenu($gravar,'javascript:document.formulario.submit();','.sistema/imagens/botao_gravar.png');
		$menu->$listagem = new VMenu($listagem,sprintf("?c=%s",definicaoEntidade::controle($this,'verPesquisa')),'.sistema/imagens/botao_listagem.png');
		return $menu;
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param visualizacao template de registro para edição
	*/
	public function montarApresentacao(negocio $negocio,$tipo = 'edicao'){
		parent::montarApresentacao($negocio,$tipo);
		$this->definirSubClasse();
		$this->definirSubColecao($negocio,$this->subClasse);
		$this->definirColecaoOposta();
		if($this->colecaoOposta->possuiItens()){
			$this->definirListagemDados(
				$this->pegarColecaoOposta()->gerarVetorDescritivo(),
				$this->pegarSubColecao()->gerarVetorDeAtributo(
					$this->definirAssociacaoOposta($this->colecaoOposta->pegar(),$this->subClasse)
				)
			);
		}else{
			$this->definirListagemDados(
				$this->pegarColecaoOposta()->gerarVetorDescritivo(), array());
		}
		$this->visualizacao->action = 
			sprintf('?c=%s',
				definicaoEntidade::controle(
					$this,
					'gravarColecao'.ucfirst(definicaoEntidade::entidade($this->subClasse))
					)
				);
		$this->visualizacao->chave = VComponente::montar('oculto',$negocio->nomeChave(),$negocio->valorChave());
		$this->visualizacao->entidade = $this->inter->pegarNome();
		$this->visualizacao->descricao = $negocio->valorDescricao();
	}
	/**
	 * Método que define a instância da subClasse a ser utilizada no cadastro
	 */
	public function definirSubClasse(){
		list($controle,$acao) = explode('_',get_class($this));
		preg_match('/verColecao(.*)/',$acao,$resultado);
		$subClasse = 'N'.ucfirst($resultado[1]);
		$this->subClasse = new $subClasse();
	}
	/**
	 * Método que define a subColeção a ser utilizada no cadastro
	 *
	 * @param negocioPadrao $negocio objeto principal do cadastro
	 * @param negocioPadrao $subNegocio objeto secundário que será gravado no banco
	 */
	public function definirSubColecao($negocio,$subNegocio){
		$arMapeamento = $subNegocio->mapearClassesAssociativas($negocio);
		if(count($arMapeamento) == 1){
			$passarPropriedade = 'passar'.ucfirst($arMapeamento[0]['propriedade']);
			$subNegocio->$passarPropriedade($negocio->valorChave());
			$this->subColecao = $subNegocio->pesquisar(new pagina(0));
		}else {
			throw new erro('Não foi possível determinar a propriedade de negocio associada. Sobrescreva o método carregarNegocio para definir a coleção a ser utilizada.');
		}
	}
	/**
	 * Método que define a subColeção a ser utilizada no cadastro
	 *
	 * @param negocioPadrao $negocio objeto principal do cadastro
	 * @param negocioPadrao $subNegocio objeto secundário que será gravado no banco
	 */
	public function definirAssociacaoOposta($negocio,$subNegocio){
		$arMapeamento = $subNegocio->mapearClassesAssociativas($negocio);
		if(count($arMapeamento) == 1){
			return $arMapeamento[0]['propriedade'];
		}else {
			throw new erro('Não foi possível determinar a propriedade de negocio associada. Sobrescreva o método definirAssociacaoOposta para definir o campo de associação oposta.');
		}
	}
	/**
	 * Método que define a listagem dos dados a serem apresentadas no cadastro
	 * @param array $arDescricaoOposta listagem descritiva completa objetos opostos
	 * @param array $arDescricaoMarcada valores que estão marcados no banco de dados
	 */
	public function definirListagemDados($arDescricaoOposta,$arDescricaoMarcada){
		if($arDescricaoOposta){
			$opcoes['legend'] = $this->colecaoOposta->pegar()->inter->pegarNome();
			$this->visualizacao->listagem = VComponente::montar('checks','subNegocio',$arDescricaoMarcada,$opcoes,$arDescricaoOposta);
		}else {
			$fieldset = new VEtiquetaHtml('fieldset');
			$fieldset->passarConteudo('Não existem registros cadastrados.');
			$this->visualizacao->listagem = $fieldset;
		}
	}
	/**
	 * Método que define a coleção oposta a ser apresentada na listagem de dados
	 */
	abstract function definirColecaoOposta();
}
?>