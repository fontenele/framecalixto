<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VListaPaginadaPDF extends objeto{
	/**
	* @var colecaoPadraoNegocio Utilizada para listagem dos dados
	*/
	protected $colecao;
	/**
	* @var array Utilizado para definição dos campos da listagem
	*/
	protected $campos;
	/**
	* @var string Nome do controle utilizado para a apresentação
	*/
	protected $controle;
	/**
	* Método construtor
	* @param colecaoPadraoNegocio $colecao
	*/
	public function __construct(controlePadraoPDFListagem $controlePDF, colecaoPadraoNegocio $colecao){
		$this->colecao = $colecao;
		$this->colecao->resetar();
		$this->controle = controlePadrao::pegarNomeControle();
		$classe = definicaoEntidade::internacionalizacao($this->controle);
		$this->inter = new $classe();
		$this->definirListagem();
		$this->montarListagem($controlePDF);
	}
	/**
	* Método de adição de um campo a listagem
	* @param string título do campo
	* @param string nome da propriedade da classe de negócio a ser listada na coluna
	* @param string tamanho ou largura da coluna
	* @param string alinhamento da coluna
	* @param integer posição ou ordem de apresentação da coluna
	*/
	function adicionarColuna($titulo, $campo, $tamanho = null, $alinhamento = null, $posicao = null){
		switch(strtolower($alinhamento)){
			case('centro'): $alinhamento = 'C'; break;
			case('direita'):$alinhamento = 'R'; break;
			case('esquerda'):$alinhamento = 'L'; break;
		}
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	/**
	* Método de adição de um campo a listagem
	* @param string título do campo
	* @param string nome da propriedade da classe de negócio a ser listada na coluna
	* @param string tamanho ou largura da coluna
	* @param string alinhamento da coluna
	* @param integer posição ou ordem de apresentação da coluna
	*/
	function adicionarColunaLink($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		switch(strtolower($alinhamento)){
			case('centro'): $alinhamento = 'C'; break;
			case('direita'):$alinhamento = 'R'; break;
			case('esquerda'):$alinhamento = 'L'; break;
		}
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campoLink'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campoLink'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	/**
	* Método de adição de um campo personalizado a listagem
	* @param string título do campo
	* @param string nome do metodo da classe de listagem que será executado para ser listado na coluna
	* @param string tamanho ou largura da coluna
	* @param string alinhamento da coluna
	* @param integer posição ou ordem de apresentação da coluna
	*/
	function adicionarColunaPersonalizada($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		switch(strtolower($alinhamento)){
			case('centro'): $alinhamento = 'C'; break;
			case('direita'):$alinhamento = 'R'; break;
			case('esquerda'):$alinhamento = 'L'; break;
		}
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	function removerColuna($posicao){
		unset($this->campos[$posicao]);
	}
	function alterarPosicao($antiga,$nova){
		$this->campos[$nova] = $this->campos[$antiga];
		unset($this->campos[$antiga]);
	}
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		$estrutura = controlePadrao::pegarEstrutura($this->pegarControle());
		foreach($estrutura['campos'] as $campo => $coluna){
			if($coluna['listagem']){
				$ordem = $coluna['ordem'] ? $coluna['ordem'] : null;
				$tamanho = $coluna['largura'] ? $coluna['largura'] : null;
				$alinhamento = isset($coluna['alinhamento']) ? $coluna['alinhamento'] : null;
				switch(true){
					case ($coluna['campoPersonalizado']):
						$this->adicionarColunaPersonalizada($coluna['titulo'], $coluna['campoPersonalizado'], $tamanho, $alinhamento, $ordem);
					break;
					case ($coluna['hyperlink'] == "sim"):
						$this->adicionarColunaLink($coluna['titulo'], $campo, $tamanho, $alinhamento, $ordem);
					break;
					case (!$coluna['campoPersonalizado']):
						$this->adicionarColuna($coluna['titulo'], $campo, $tamanho, $alinhamento, $ordem);
					break;
				}
			}
		}
	}
	/**
	 * @param type $nrValorPorcentagem
	 * @return type
	 */
	public static function calcularLargura($nrValorPorcentagem) {
		return ($nrValorPorcentagem * 190)/100;
	}
	/**
	* Montar listagem
	* @return string retorno da listagem
	*/
	function montarListagem(controlePadraoPDF $controlePDF){
		if($this->colecao->possuiItens() && is_array($this->campos)){
			$conexao = conexao::criar();
			$chaves = array_keys($this->campos);
			sort($chaves);
			$alinhamentos = $tamanhos = $campos = array();
			foreach($chaves as $chave){
				$campo = $this->campos[$chave];
				$tamanhos[] = $campo['tamanho'] ? self::calcularLargura($campo['tamanho']) : '30';
				$campos[] = $campo['titulo'];
				$alinhamentos = $campo['alinhamento'];
			}
			$controlePDF->Linha($campos,$tamanhos,4,$alinhamentos);
			$controlePDF->visualizacao->SetFillColor(239,239,239);
			$x = 0;
			if($this->colecao->possuiItens()){
				$item = $this->colecao->retornarItem();
				$mapeador  = controlePadrao::pegarEstrutura($item);
				$controlePDF->visualizacao->SetFont('Arial','',6);
				while($item = $this->colecao->avancar()){
					$alinhamentos = $tamanhos = $campos = array();
					foreach($chaves as $chave){
						$campo = $this->campos[$chave];
						switch(true){
							case(isset($campo['campoPersonalizado'])):
								$valorDoCampo  = call_user_func($campo['campoPersonalizado'],$item);
							break;
							case(isset($campo['campoLink'])):
								$controle = definicaoEntidade::controle($item,'verEdicao');
								$pegar = 'pegar'.ucfirst($campo['campoLink']);
								$link = sprintf("?c=%s&chave=%s",$controle,$item->valorChave());
								switch(true){
									case($mapeador['campos'][$campo['campoLink']]['classeAssociativa']):
										$classeAssociativa = new $mapeador['campos'][$campo['campoLink']]['classeAssociativa']($conexao);
										$classeAssociativa->ler($item->$pegar());
										$valorDoCampo = $classeAssociativa->valorDescricao();
									break;
									case($mapeador['campos'][$campo['campoLink']]['valores']):
										$valorDoCampo = $mapeador['campos'][$campo['campoLink']]['valores'][$item->$pegar()];
									break;
									default:
										$valorDoCampo = $item->$pegar();
										if(is_object($valorDoCampo)) {
											$valorDoCampo = $valorDoCampo->__toString();
										}
								}
							break;
							default:
								$pegar = 'pegar'.ucfirst($campo['campo']);
								switch(true){
									case($mapeador['campos'][$campo['campo']]['classeAssociativa']):
										$classeAssociativa = new $mapeador['campos'][$campo['campo']]['classeAssociativa']($conexao);
										$classeAssociativa->ler($item->$pegar());
										$valorDoCampo = $classeAssociativa->valorDescricao();
									break;
									case($mapeador['campos'][$campo['campo']]['valores']):
										$valorDoCampo = $mapeador['campos'][$campo['campo']]['valores'][$item->$pegar()];
									break;
									default:
										$valorDoCampo = $item->$pegar();
										if(is_object($valorDoCampo)){
											$valorDoCampo = $valorDoCampo->__toString();
										}
								}
							break;
						}
						$tamanhos[] = $campo['tamanho'] ? self::calcularLargura($campo['tamanho']) : '30';
						$alinhamentos[]  = $campo['alinhamento'];
						$campos[] = $valorDoCampo;
					}
					$controlePDF->Linha($campos,$tamanhos,4,$alinhamentos, false, ++$x%2);
				}
			}else{
				$mensagem = $this->inter->pegarMensagem('registrosNaoEncontrados');
				$controlePDF->celula(190,4,$mensagem,1,0,'C');
			}
		}
	}
	/**
	* Método de abertura da linha da listagem
	* @param mixed item a ser apresentado na listagem
	* @param integer número da linha a ser apresentada
	*/
	public function definirCorFundo(controlePadraoPDF $controlePDF,$nrLinha){
		if($nrLinha%2){
		}else{
			$controlePDF->visualizacao->SetFillColor(255,255,255);
		}
	}
}
?>