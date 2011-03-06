<?php
/**
 * Classe de representação de uma camada de negócio com lógica de árvore
 *
 * Neste objeto a definição de chave esquerda e direita faz com que funcione a lógica de árvore
 * e o deslocamento posicional de um registro é definido na chave esquerda do objeto
 * <b>caso a chave esquerda deste objeto seja igual a outra chave faz com que este seja um objeto filho </b><br/>
 * <b>caso a chave direita deste objeto seja igual a outra chave faz com que este objeto seja o irmão mais novo </b><br/>
 *
 * @package FrameCalixto
 * @subpackage Negocio
 */
abstract class negocioPadraoArvore extends negocioPadrao{
	/**
	 * Indice da chave identificador do objeto da árvore
	 */
	const idxIdentificador = 'id';
	/**
	 * Indice da chave esquerda no retorno da árvore
	 */
	const idxEsquerda = 'e';
	/**
	 * Indice da chave direita no retorno da árvore
	 */
	const idxDireita = 'd';
	/**
	 * Indice da chave descrição no retorno da árvore
	 */
	const idxDescricao = 'ds';
	/**
	 * Indice da chave dos filhos no retorno da árvore
	 */
	const idxFilhos = 'filhos';
	/**
	 * Coleção com os filhos do objeto
	 * @var colecaoPadraoNegocio 
	 */
	protected $colecaoFilhos;
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public function  __construct(conexao $conexao = null) {
		parent::__construct($conexao);
		$this->colecaoFilhos = new colecaoPadraoNegocio(null, $this->pegarConexao());
	}
	/**
	 * Método que define o nome da chave esquerda da arvore
	 */
	abstract public function nomeChaveEsquerda();
	/**
	 * Método que define o nome da chave direita da arvore
	 */
	abstract public function nomeChaveDireita();
	/**
	 * Método de retorno da coleção de filtro para agrupamento lógico de árvores
	 * @return colecaoPadraoFiltro
	 */
	protected function filtroDeAgrupamentoDaArvore(){
		return new colecaoPadraoFiltro();
	}
	/**
	 * Método que retorna o valor da chave esquerda da arvore
	 * @return integer
	 */
	public function valorChaveEsquerda(){
		return $this->{$this->nomeChaveEsquerda()};
	}
	/**
	 * Método que retorna o valor da chave direita da arvore
	 * @return integer
	 */
	public function valorChaveDireita(){
		return $this->{$this->nomeChaveDireita()};
	}
	protected function atualizarDadosArvore($valor, $chave, $posicaoInicial, $posicaoFinal){
		$estrutura = $this->pegarMapeamento();
		$persistente = $this->pegarPersistente();
		$persistente->atualizarArvore(
			$valor,
			$estrutura[$chave]['campo'],
			$posicaoInicial,
			$posicaoFinal,
			$this->montarFiltroParaPersistente($this->filtroDeAgrupamentoDaArvore())
		);
	}
	/**
	 * Método que ajusta o espaço para caber mais um registro na arvore
	 * @param integer $posicao
	 */
	protected function abrirEspaco($posicao,$tamanho = '2'){
		if(persistente::imprimindoComandos()) echo ("\n<br/> --ABRINDO ESPACO COM TAMANHO {$tamanho} NA POSICAO {$posicao}<br/>");
		$this->atualizarDadosArvore("+{$tamanho}", $this->nomeChaveEsquerda(), $posicao, null);
		$this->atualizarDadosArvore("+{$tamanho}", $this->nomeChaveDireita(), $posicao, null);
	}
	/**
	 * Método que ajusta o espaço para remover um registro da arvore
	 * @param integer $posicao
	 */
	protected function fecharEspaco($posicao,$tamanho = '2'){
		if(persistente::imprimindoComandos()) echo ("\n<br/> --FECHANDO ESPACO COM TAMANHO {$tamanho} NA POSICAO {$posicao}<br/>");
		$this->atualizarDadosArvore("-{$tamanho}", $this->nomeChaveEsquerda(), $posicao, null);
		$this->atualizarDadosArvore("-{$tamanho}", $this->nomeChaveDireita(), $posicao, null);
	}
	/**
	 * Método que ajusta um bloco de registros para mover a direita
	 * @param integer $posicaoInicial
	 * @param integer $posicaoFinal
	 * @param integer $distancia 
	 */
	protected function moverBlocoDireita($posicaoInicial, $posicaoFinal, $distancia){
		if(persistente::imprimindoComandos()) echo ("\n<br/> --MOVENDO BLOCO [{$posicaoInicial}-{$posicaoFinal}] PARA DIREITA {$distancia} CASAS<br/>");
		$this->atualizarDadosArvore("+{$distancia}", $this->nomeChaveEsquerda(), $posicaoInicial-1, $posicaoFinal+1);
		$this->atualizarDadosArvore("+{$distancia}", $this->nomeChaveDireita(), $posicaoInicial-1, $posicaoFinal+1);
	}
	/**
	 * Método que ajusta um bloco de registros para mover a esquerda
	 * @param integer $posicaoInicial
	 * @param integer $posicaoFinal
	 * @param integer $distancia
	 */
	protected function moverBlocoEsquerda($posicaoInicial, $posicaoFinal, $distancia){
		if(persistente::imprimindoComandos()) echo ("\n<br/> --MOVENDO BLOCO [{$posicaoInicial}-{$posicaoFinal}] PARA ESQUERDA {$distancia} CASAS<br/>");
		$this->atualizarDadosArvore("-{$distancia}", $this->nomeChaveEsquerda(), $posicaoInicial-1, $posicaoFinal+1);
		$this->atualizarDadosArvore("-{$distancia}", $this->nomeChaveDireita(), $posicaoInicial-1, $posicaoFinal+1);
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a alteração
	* @param negocio objeto antes da alteração .
	*/
	public function  verificarAntesAlterar($negocio) {
		if(persistente::imprimindoComandos()){
			echo ("\n<br/><strong> --MOVIMENTAÇÃO DE TRECHO (Alteração)</strong>");
			$trecho = "[".$negocio->valorChaveEsquerda()."-".$negocio->valorChaveDireita()."] PARA POSICAO ".$this->valorChaveEsquerda()."<br/>";
		}
		switch (true) {
			case !$this->valorChaveEsquerda() :
				if(persistente::imprimindoComandos()) echo ("\n<br/> --MOVENDO COMO PRIMEIRO {$trecho}");
				//moveu como primeiro
				$this->{$this->nomeChaveEsquerda()} = '0';
				$tamanho = $negocio->valorChaveDireita() - $negocio->valorChaveEsquerda() + 1;
				$diferenca = ($negocio->valorChaveEsquerda() - $this->valorChaveEsquerda())*2;
				$this->abrirEspaco($this->valorChaveEsquerda(),$tamanho);
				$this->moverBlocoEsquerda($negocio->valorChaveEsquerda()+$tamanho, $negocio->valorChaveDireita()+$tamanho, $negocio->valorChaveEsquerda()+$tamanho-1);
				$this->fecharEspaco($negocio->valorChaveEsquerda()+$tamanho,$tamanho);
			break;
			case $negocio->valorChaveEsquerda() == $this->valorChaveEsquerda()+1 :
			case $negocio->valorChaveEsquerda() === $this->valorChaveEsquerda() :
				//Não moveu
				if(persistente::imprimindoComandos()) echo ("\n<br/> --NÃO MOVEU POSICIONAMENTO IGUAL {$trecho} ou ".$this->valorChaveEsquerda()+1);
			break;
			case $this->valorChaveEsquerda() > $negocio->valorChaveEsquerda() :
				//Moveu pra direita
				if(persistente::imprimindoComandos()) echo ("\n<br/> --MOVENDO PARA DIREITA {$trecho}");
				$tamanho = $negocio->valorChaveDireita() - $negocio->valorChaveEsquerda() + 1;
				$this->abrirEspaco($this->valorChaveEsquerda(),$tamanho);
				$diferenca = $this->valorChaveEsquerda() - $negocio->valorChaveEsquerda() +1;
				$this->moverBlocoDireita($negocio->valorChaveEsquerda(), $negocio->valorChaveDireita(), $diferenca);
				$this->fecharEspaco($negocio->valorChaveEsquerda(),$tamanho);
			break;
			case $this->valorChaveEsquerda() < $negocio->valorChaveEsquerda() :
				//Moveu pra esquerda
				if(persistente::imprimindoComandos()) echo ("\n<br/> --MOVENDO PARA ESQUERDA {$trecho}");
				$tamanho = $negocio->valorChaveDireita() - $negocio->valorChaveEsquerda() + 1;
				$diferenca = ($negocio->valorChaveEsquerda() + $tamanho - $this->valorChaveEsquerda() -1);
				$this->abrirEspaco($this->valorChaveEsquerda(),$tamanho);
				$this->moverBlocoEsquerda($negocio->valorChaveEsquerda()+$tamanho, $negocio->valorChaveDireita()+$tamanho, $diferenca);
				$this->fecharEspaco($negocio->valorChaveEsquerda()+$tamanho,$tamanho);
			break;
		}
		//if (negocioPadraoArvore::$debug) die();
		parent::verificarAntesAlterar($negocio);
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function  verificarAntesInserir() {
		if(persistente::imprimindoComandos()) echo ("\n<br/> <strong>--MOVIMENTAÇÃO DE TRECHO (Inclusão)</strong>");
		$this->{$this->nomeChaveEsquerda()} += 1;
		$this->{$this->nomeChaveDireita()} = $this->valorChaveEsquerda() +1;
		parent::verificarAntesInserir();
		$this->abrirEspaco($this->valorChaveEsquerda()-1);
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a exclusão
	*/
	public function  verificarAntesExcluir() {
		if(persistente::imprimindoComandos()) echo ("\n<br/> <strong>--MOVIMENTAÇÃO DE TRECHO (Exclusão)</strong>");
		parent::verificarAntesExcluir();
		$objeto = definicaoEntidade::negocio($this);
		$negocio = new $objeto($this->conexao);
		$negocio->{"filtrar".ucfirst($this->nomeChaveEsquerda())}(operador::maiorQue($this->valorChaveEsquerda(),  operador::restricaoE));
		$negocio->{"filtrar".ucfirst($this->nomeChaveDireita())}(operador::menorQue($this->valorChaveDireita()));
		$colecao = $negocio->pesquisar();
		if($colecao->contarItens())
			throw new erroNegocio ('Este registro possui '.$colecao->contarItens().' filhos');
		$this->fecharEspaco($this->valorChaveEsquerda());
	}
	/**
	* Executa o comando de gravação do objeto
	* @param bolean caso verdadeiro irá incluir com a chave de negócio passada caso falso irá verificar, se foi passada a chave irá alterar senão irá incluir
	*/
	public function gravar($gravarComChavePassada = false){
		try{
			$persistente = $this->pegarPersistente();
			switch(true){
				case $gravarComChavePassada:
					$this->verificarAntesInserir();
					$persistente->inserir($this->negocioPraVetor(), $gravarComChavePassada);
				break;
				case $this->valorChave():
					$negocio = get_class($this);
					$negocio = new $negocio();
					$negocio->ler($this->valorChave());
					$this->verificarAntesAlterar($negocio);
					$arNegocio = $this->negocioPraVetor();
					$estrutura = $this->pegarMapeamento();
					unset ($arNegocio[$estrutura[$this->nomeChaveDireita()]['campo']]);
					unset ($arNegocio[$estrutura[$this->nomeChaveEsquerda()]['campo']]);
					$persistente->alterar($arNegocio,$this->valorChave());
				break;
				default:
					$this->valorChave($persistente->gerarSequencia());
					$this->verificarAntesInserir();
					$persistente->inserir($this->negocioPraVetor());
				break;
			}
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	 * Método de aninhamento de coleção de objetos de negócio padrao árvore
	 * @param colecaoPadraoNegocio $colecao
	 * @param integer $ate parâmetro de limite para percorrer a coleção
	 */
	protected function aninhar(colecaoPadraoNegocio $colecao, $ate = null){
		while($negocio = $colecao->arrancar()){
			if(($negocio->valorChaveDireita() - $negocio->valorChaveEsquerda()) > 1 ){
				$negocio->aninhar($colecao, $negocio->valorChaveDireita() -1);
			}
			$this->colecaoFilhos->passar($negocio->valorChave(),$negocio);
			if($negocio->valorChaveDireita() == $ate) return;
		}
	}
	/**
	 * Método de pesquisa com retorno da coleção aninhada
	 * @param pagina $pagina
	 * @param filtro $filtro
	 * @return colecaoPadraoNegocio
	 */
	public function pesquisarAninhado(pagina $pagina = null, $filtro = null) {
		$this->colecaoFilhos = new colecaoPadraoNegocio(null, $this->pegarConexao());
		$this->aninhar($this->pesquisar($pagina, $filtro));
		return $this->colecaoFilhos;
	}
	/**
	 * Método de ler todos os registros com retorno da coleção aninhada
	 * @param pagina $pagina
	 * @param filtro $filtro
	 * @return colecaoPadraoNegocio
	 */
	public function lerTodosAninhado(){
		$this->colecaoFilhos = new colecaoPadraoNegocio(null, $this->pegarConexao());
		$this->aninhar($this->lerTodos());
		return $this->colecaoFilhos;
	}
	/**
	 * Método de visualização da coleção aninhada
	 * @param integer $nivel nível de repasse para a mostragem
	 */
	public function mostrarArvore($nivel = 0){
		while($negocio = $this->colecaoFilhos->avancar()){
			echo str_repeat("|-----", $nivel).'['.$negocio->valorDescricao().']<br/>';
			if($negocio->pegarColecaoFilhos()->possuiItens()){
				$negocio->mostrarArvore($nivel+1);
			}
		}
	}
	/**
	 * Método de leitura dos pais do objeto
	 * @return colecaoPadraoNegocio
	 */
	public function lerPais(){
		$negocio = get_class($this);
		$negocio = new $negocio($this->pegarConexao());
		$negocio->{"filtrar".ucfirst($this->nomeChaveEsquerda())}(operador::menorQue($this->valorChaveEsquerda()));
		$negocio->{"filtrar".ucfirst($this->nomeChaveDireita())}(operador::maiorQue($this->valorChaveDireita()));
		return $negocio->pesquisar();
	}
	/**
	 * Método que retorna um array com a representação do aninhamento contido no objeto
	 * @return array
	 */
	public function arvore(){
		if($this->colecaoFilhos->contarItens()){
			while($negocio = $this->colecaoFilhos->avancar()){
					$filhos[$negocio->valorChave()]= $negocio->arvore();
			}
			if(!$this->valorChave()) return $filhos;
			return array(
				self::idxIdentificador=>$this->valorChave(),
				self::idxEsquerda=>$this->valorChaveEsquerda(),
				self::idxDireita=>$this->valorChaveDireita(),
				self::idxDescricao=>$this->valorDescricao(),
			self::idxFilhos=>$filhos
				);
		}else{
			return array(
				self::idxIdentificador=>$this->valorChave(),
				self::idxEsquerda=>$this->valorChaveEsquerda(),
				self::idxDireita=>$this->valorChaveDireita(),
				self::idxDescricao=>$this->valorDescricao(),
			);

		}
	}
	/**
	 * Método de ajuste posicional na arvore de acordo com o id passado
	 * @param string $idReferencia identificador do objeto de referência
	 * @param boolean $filho flag de configuração para posicionar como filho da referência
	 */
	public function ajustarPosicao($idReferencia = null,$filho = false){
		$referencia = clone $this;
		switch(true){
			case (!$idReferencia):
				//Envia como primeiro
				$this->{$this->nomeChaveEsquerda()} = null;
			break;
			case ($filho):
				//Envia como primeiro filho
				$referencia->ler($idReferencia);
				$this->{$this->nomeChaveEsquerda()} = $referencia->valorChaveEsquerda();
			break;
			case (!$filho):
				//Envia como após
				$referencia->ler($idReferencia);
				$this->{$this->nomeChaveEsquerda()} = $referencia->valorChaveDireita();
			break;
		}
	}
}
?>