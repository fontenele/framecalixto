<?php
/**
* Classe de definição da camada de controle
* Formação especialista para montar um relatório em PDF com uma listagem de uma coleção de objetos de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPDFListagem extends controlePadraoPDF{
	/**
	* @var negocioPadrao objeto de negócio que será utilizado para gerar a pesquisa
	*/
	public $filtro;
	/**
	* @var VListaPaginadaPDF listagem
	*/
	public $listagem;
	/**
	* @var array Campos do topo do relatorio
	*/
	public $campos;
	/**
	* @var string Titulo do relatorio
	*/
	public $titulo;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$this->margens(10, 10, 10);
			$this->passarCampos(array());
			$this->definirFiltro();
			$this->registrarInternacionalizacao();
			$this->criarVisualizacaoPadrao();
			$this->montarTopo();
			$this->montarListagem($this->definirColecao());
			$this->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que monta o topo do relatório
	*/
	public function montarTopo($mostrarTodos = true){
		$estrutura = $this->pegarEstrutura($this);
		$negocio = $this->pegarFiltro();
		$this->visualizacao->SetFont('Times','B',8);
		$this->ln(3);
		$this->visualizacao->SetFillColor(220,220,220);
		$this->celula(190,5,$this->titulo,0,1,'C',1);
		$this->visualizacao->SetFont('Times','',8);
		$this->visualizacao->SetFont('Times','B',8);
		$campos = false;
		foreach($estrutura['campos'] as $nomeCampo => $dadosCampo){
		//foreach($this->campos as $campo => $label){
			$metodo = 'pegar'.ucfirst($nomeCampo);
			$valor = $negocio->$metodo();
			if($mostrarTodos || $valor){
				if($dadosCampo['pesquisa']){
					$this->celula(100,7,"{$dadosCampo['label']}: {$valor}",0,0);
					$this->ln(4);
					$campos = true;
				}
			}
		}
		if($campos) $this->ln(4);
	}
	/**
	 * Retorna o nome da variável que irá segurar o filtro na sessão
	 * @return string
	 */
	protected function nomeDoFiltro() {
		return 'filtro';
	}

	/**
	 * Método que define como se comporta um filtro novo ou limpo
	 * @return negocioPadrao
	 */
	protected function filtroNovo() {
		$negocio = definicaoEntidade::negocio($this);
		return new $negocio();
	}
	/**
	* Método que define o objeto de negócio que executará a pesquisa
	*/
	public function definirFiltro(){
		$negocio = definicaoEntidade::negocio($this);
		if($_POST){
			$this->filtro = $this->filtroNovo();
			$this->montarNegocio($this->filtro);
			$this->sessao->registrar($this->nomeDoFiltro(),$this->filtro);
		}else{
			$this->filtro = ($this->sessao->tem($this->nomeDoFiltro())) ? $this->sessao->pegar($this->nomeDoFiltro()): $this->filtroNovo();
		}
	}
	/**
	* Método de criação da coleção a ser listada
	* @return colecaoPadraoNegocio coleção a ser listada
	*/
	public function definirColecao(){
		$metodo = ($this->sessao->tem($this->nomeDoFiltro())) ? 'pesquisar' : 'lerTodos';
		return $this->filtro->$metodo();
	}
	/**
	 * Método de apresentação da listagem
	 * @param colecao $colecao
	 */
	public function montarListagem(colecao $colecao){
		$this->listagem = new VListaPaginadaPDF($this,$colecao);
	}
	/**
	* Método de registro da internacionalização
	* @param controle $entidade
	* @param visualizacao $visualizacao
	*/
	public function registrarInternacionalizacao(){
		$inter = definicaoEntidade::internacionalizacao($this);
		$entidade = definicaoEntidade::entidade($this);
		$inter = new $inter();
		$this->passarTitulo((isset($_GET['c']) && $inter->pegarTexto(definicaoEntidade::funcionalidade($_GET['c']))) ? $inter->pegarTexto(definicaoEntidade::funcionalidade($_GET['c'])) : 'Relatório');
		$internacionalizacao = $inter->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade'])){
			foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
				if(isset($propriedade['nome'])){
					$this->campos[$indice] = strval($propriedade['nome']);
				}
			}
		}
	}
}
?>