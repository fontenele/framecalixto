<?php
/**
* Classe de definição da camada de controle
* Formação especialista para gravar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
abstract class controlePadraoGravarColecao extends controlePadrao{
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
		try{
			$this->definirSubClasse();
			$nmSubNegocio = $this->pegarSubClasse();
			$this->passarProximoControle(definicaoEntidade::controle($this,'verColecao'.ucfirst(definicaoEntidade::entidade($this->subClasse))));
			$negocio = definicaoEntidade::negocio($this);
			$conexao = conexao::criar();
			$conexao->iniciarTransacao();
			$negocio = new $negocio($conexao);
			$idNegocio = $_POST[$negocio->nomeChave()];
			$negocio->valorChave($idNegocio);
			$this->definirSubColecao($negocio,$this->subClasse);
			$this->subColecao->excluir();
			$colecaoParaInclusao = new colecaoPadraoNegocio($conexao);
			foreach($_POST['subNegocio'] as $index => $idSubNegocio){
				$subNegocio = new $nmSubNegocio($conexao);
				$this->montarSubNegocioParaInclusao($subNegocio,$idNegocio,$idSubNegocio);
				$colecaoParaInclusao->$index = $subNegocio;
			}
			$colecaoParaInclusao->gravar();
			$this->sessao->registrar('negocio',$negocio);
			$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
			$conexao->validarTransacao();
			$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		}
		catch(erro $e){
			$conexao->desfazerTransacao();
			throw $e;
		}
	}
	/**
	 * Método que define a instância da subClasse a ser utilizada no cadastro
	 */
	public function definirSubClasse(){
		list($controle,$acao) = explode('_',get_class($this));
		preg_match('/gravarColecao(.*)/',$acao,$resultado);
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
	 * Monta o objeto de subClasse para inclusão no banco de dados
	 *
	 * @param negocioPadrao $subNegocio
	 * @param string $idNegocio
	 * @param string $idSubNegocio
	 */
	abstract function montarSubNegocioParaInclusao($subNegocio,$idNegocio,$idSubNegocio);
}
?>
