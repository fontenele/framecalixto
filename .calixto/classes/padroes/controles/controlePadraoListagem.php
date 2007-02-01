<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoListagem extends controlePadrao{
	/**
	* @var [controle] Utilizado para linkar os endereços e paginaçoes
	*/
	public $controle;
	/**
	* @var [pagina] Utilizada para listagem dos dados
	*/
	public $pagina;
	/**
	* @var [colecao] Utilizada para listagem dos dados
	*/
	public $colecao;
	/**
	* @var [vetor] Utilizado para definição dos campos da listagem
	*/
	public $campos;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->pagina = new pagina();
		$this->pagina->passarPagina();
		$this->colecao = new colecao();
		$this->campos = array();
	}
	/**
	* Método de criação da visualizacao
	*/
	public function criarVisualizacaoPadrao(){}
	/**
	* Método de criação da visualizacao
	*/
	public function criarInternacionalizacaoPadrao(){}
	/**
	* Método de adição de um campo a listagem
	* @param [string] título do campo
	* @param [string] nome da propriedade da classe de negócio a ser listada na coluna
	* @param [string] tamanho ou largura da coluna
	* @param [string] alinhamento da coluna
	* @param [numerico] posição ou ordem de apresentação da coluna
	*/
	function adicionarColuna($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	/**
	* Método de adição de um campo a listagem
	* @param [string] título do campo
	* @param [string] nome da propriedade da classe de negócio a ser listada na coluna
	* @param [string] tamanho ou largura da coluna
	* @param [string] alinhamento da coluna
	* @param [numerico] posição ou ordem de apresentação da coluna
	*/
	function adicionarColunaLink($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campoLink'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campoLink'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	/**
	* Método de adição de um campo personalizado a listagem
	* @param [string] título do campo
	* @param [string] nome do metodo da classe de listagem que será executado para ser listado na coluna
	* @param [string] tamanho ou largura da coluna
	* @param [string] alinhamento da coluna
	* @param [numerico] posição ou ordem de apresentação da coluna
	*/
	function adicionarColunaPersonalizada($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
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
						$arValores = false;
						if(isset($propriedade->dominio->opcao))
						foreach($propriedade->dominio->opcao as $opcao){
							$arValores[caracteres($opcao['id'])] = $this->internacionalizacao->pegarOpcao(caracteres($propriedade['id']),caracteres($opcao['id']));
						}
						if(isset($propriedade->apresentacao->listagem))
						$mapeador[caracteres($propriedade['id'])] = array(
							'titulo'			=> $this->internacionalizacao->pegarPropriedade(caracteres($propriedade['id']),'abreviacao'),
							'hyperlink'			=> caracteres($propriedade->apresentacao->listagem['hyperlink']	),
							'tamanho'			=> caracteres($propriedade->apresentacao->listagem['tamanho']	),
							'ordem'				=> caracteres($propriedade->apresentacao->listagem['ordem']	), 
							'campoPersonalizado'=> caracteres($propriedade->apresentacao->listagem['campoPersonalizado']	),
							'dominio'			=> $arValores,
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
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this->pegarControle(),$arquivoXML));
		foreach($estrutura as $campo => $coluna){
			$titulo = '';
			$alinhamento = '';
			$ordem = $coluna['ordem'] ? $coluna['ordem'] : null ;
			$tamanho = $coluna['tamanho'] ? $coluna['tamanho'] : null ;
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
	/**
	* Montar listagem
	* @return [string] retorno da listagem
	*/
	function montarListagem(){
		if(is_array($this->campos)){
			$chaves = array_keys($this->campos);
			sort($chaves);
			$retorno = "\n<table class=\"tabela0\">\n";
			$retorno.= "<tr>\n";
			foreach($chaves as $chave){
				$campo = $this->campos[$chave];
				$tamanho = ($campo['tamanho']) ? "width='{$campo['tamanho']}'" : '' ;
				$alinhamento = ($campo['alinhamento']) ? "align='{$campo['alinhamento']}'" : '' ;
				$retorno.="<th {$tamanho} {$alinhamento} >{$campo['titulo']}</th>\n";
			}
			$retorno.= "</tr>\n";
			$x;
			if($this->colecao->possuiItens()){
				$item = $this->colecao->retornarItem();
				$mapeador = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($item));
				while($item = $this->colecao->avancar()){
					if(++$x%2){
						$retorno.= "\t<tr class='linhaListagem1'>\n";
					}else{
						$retorno.= "\t<tr class='linhaListagem2'>\n";
					}
					foreach($chaves as $chave){
						$campo = $this->campos[$chave];
						switch(true){
							case(isset($campo['campoPersonalizado'])):
								$retorno.="\t\t<td>".$this->$campo['campoPersonalizado']($item)."</td>\n";
							break;
							case(isset($campo['campoLink'])):
								$controle = definicaoEntidade::controle($item,'verEdicao');
								$pegar = 'pegar'.ucfirst($campo['campoLink']);
								$link = sprintf("?c=%s&amp;chave=%s",$controle,$item->valorChave());
								switch(true){
									case($mapeador[$campo['campoLink']]['classeAssociativa']):
										$classeAssociativa = new $mapeador[$campo['campoLink']]['classeAssociativa']();
										$classeAssociativa->ler($item->$pegar());
										$valorDoCampo = $classeAssociativa->valorDescricao();
									break;
									case($mapeador[$campo['campoLink']]['dominio']):
										$valorDoCampo = $mapeador[$campo['campoLink']]['dominio'][$item->$pegar()];
									break;
									default:
										$valorDoCampo = $item->$pegar();
								}
								$retorno.="\t\t<td><a href='{$link}' >".$valorDoCampo."</a></td>\n";
							break;
							default:
								$pegar = 'pegar'.ucfirst($campo['campo']);
								switch(true){
									case($mapeador[$campo['campo']]['classeAssociativa']):
										$classeAssociativa = new $mapeador[$campo['campo']]['classeAssociativa']();
										$classeAssociativa->ler($item->$pegar());
										$valorDoCampo = $classeAssociativa->valorDescricao();
									break;
									case($mapeador[$campo['campo']]['dominio']):
										$valorDoCampo = $mapeador[$campo['campo']]['dominio'][$item->$pegar()];
									break;
									default:
										$valorDoCampo = $item->$pegar();
								}
								$retorno.="\t\t<td>".$valorDoCampo."</td>\n";
							break;
						}
					}
					$retorno.= "\t</tr>\n";
				}
				$retorno.="</table>\n";
				return $retorno;
			}else{
				$largura = count($this->campos);
				$mensagem = $this->internacionalizacao->pegarMensagem('registrosNaoEncontrados');
				$retorno.= "\t<tr class='linhaListagem1'>\n";
				$retorno.= "<td colspan='{$largura}'>{$mensagem}</td>";
				$retorno.= "\t</tr>\n";
				return $retorno.= "</table>\n";
			}
		}else{
			return '';
		}
	}
	/**
	* Monta o paginador da listagem
	* @return [string] paginador da listagem
	*/
	function montarPaginador(){
		$retorno = '';
		if($this->pagina->pegarTamanhoGeral() > $this->pagina->pegarTamanhoPagina()){
			$retorno.="<div class='container3'>\n";
			$retorno.="	<div class='a'></div>\n";
			$retorno.="	<div class='b'></div>\n";
			$retorno.="	<div class='c'></div>\n";
			$retorno.="	<div class='d'></div>\n";
			$retorno.="	<div class='e'></div>\n";
			$retorno.="	<div class='f'></div>\n";
			$retorno.="	<div class='g'></div>\n";
			$retorno.="	<div class='h'></div>\n";
			$retorno.="	<div class='texto'>Paginas:\n";
			$retorno.="		<p>&nbsp;\n";
			$paginas = ($this->pagina->pegarTamanhoGeral()/$this->pagina->pegarTamanhoPagina() +1);
			$paginas = (($this->pagina->pegarTamanhoGeral()%$this->pagina->pegarTamanhoPagina()) == 0) ? $paginas -1 : $paginas;
			for($i=1;$i <= $paginas;$i++){
				$link = sprintf('?c=%s&amp;pagina=%s',$this->controle, $i);
				if($i == $this->pagina->pegarPagina()){
					$retorno.="{$i},\n";
				}else{
					$retorno.="<a href='{$link}'>$i</a>,\n";
				}
			}
			$retorno.="		</p>\n";
			$retorno.="	</div>\n";
			$retorno.="</div>\n";
		}
		return $retorno;
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	function __toString(){
		$classe = definicaoEntidade::internacionalizacao($this->controle);
		$this->internacionalizacao = new $classe();
		$this->definirListagem();
		$retorno = $this->montarListagem();
		$retorno.= $this->montarPaginador();
		return $retorno;
	}
}
?>
