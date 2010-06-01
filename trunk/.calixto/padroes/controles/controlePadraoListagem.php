<?php
/**
* Classe de definição da camada de controle
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoListagem extends controlePadrao{
	/**
	* @var controle Utilizado para linkar os endereços e paginaçoes
	*/
	public $controle;
	/**
	* @var pagina Utilizada para listagem dos dados
	*/
	public $pagina;
	/**
	* @var colecao Utilizada para listagem dos dados
	*/
	public $colecao;
	/**
	* @var array Utilizado para definição dos campos da listagem
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
	* Método de validação do controle de acesso
	* @return boolean resultado da validação
	*/
	public function validarAcessoAoControle(){
		return true;
	}
	/**
	* Método de adição de um campo a listagem
	* @param string título do campo
	* @param string nome da propriedade da classe de negócio a ser listada na coluna
	* @param string tamanho ou largura da coluna
	* @param string alinhamento da coluna
	* @param integer posição ou ordem de apresentação da coluna
	*/
	function adicionarColuna($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		switch(strtolower($alinhamento)){
			case('centro'): $alinhamento = 'center'; break;
			case('direita'):$alinhamento = 'right'; break;
			case('esquerda'):$alinhamento = 'left'; break;
		}
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campo'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
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
	* Método de adição de um campo a listagem
	* @param string título do campo
	* @param string nome da propriedade da classe de negócio a ser listada na coluna
	* @param string tamanho ou largura da coluna
	* @param string alinhamento da coluna
	* @param integer posição ou ordem de apresentação da coluna
	*/
	function adicionarColunaLink($titulo, $campo, $tamanho = null, $alinhamento = null,$posicao = null){
		switch(strtolower($alinhamento)){
			case('centro'): $alinhamento = 'center'; break;
			case('direita'):$alinhamento = 'right'; break;
			case('esquerda'):$alinhamento = 'left'; break;
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
			case('centro'): $alinhamento = 'center'; break;
			case('direita'):$alinhamento = 'right'; break;
			case('esquerda'):$alinhamento = 'left'; break;
		}
		if($posicao){
			$this->campos[$posicao] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}else{
			$this->campos[] = array('titulo'=>$titulo,'campoPersonalizado'=>$campo,'tamanho'=>$tamanho,'alinhamento'=>$alinhamento);
		}
	}
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		$estrutura = controlePadrao::pegarEstrutura($this->pegarControle());
		foreach($estrutura['campos'] as $campo => $coluna){
			if($coluna['listagem']){
				$titulo = '';
				$alinhamento = '';
				$ordem = $coluna['ordem'] ? $coluna['ordem'] : null ;
				$tamanho = $coluna['largura'] ? $coluna['largura'] : null ;
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
		
		$this->adicionarColunaPersonalizada(' ', 'colunaExcluir', '2%', 'D', 999999);
		$this->adicionarColunaPersonalizada(' ', 'colunaEditar', '2%', 'D', 999998);
	}
	/**
	* Montar listagem
	* @return string retorno da listagem
	*/
	function montarListagem(){
		if(!$this->colecao->possuiItens()){
			$mensagem = $this->inter->pegarMensagem('registrosNaoEncontrados');
			return "<div class='ui-state-highlight ui-corner-bottom' style='width:90%;margin:auto;text-align:center;'>{$mensagem}</div>";
		}
		if(is_array($this->campos)){
			$conexao = conexao::criar();
			$chaves = array_keys($this->campos);
			sort($chaves);
			$retorno = "\n<table summary='text' class=\"tabela0 ui-widget-content ui-corner-all\">\n";
			$retorno.= "<thead class='ui-state-default'><tr class='ui-widget-header'>\n";
			foreach($chaves as $chave){
				$campo = $this->campos[$chave];
				$tamanho = ($campo['tamanho']) ? "width='{$campo['tamanho']}'" : '' ;
				$alinhamento = ($campo['alinhamento']) ? "align='{$campo['alinhamento']}'" : '' ;
				$retorno.="<th {$tamanho} {$alinhamento} >{$campo['titulo']}</th>\n";
			}
			$retorno.= "</tr></thead><tbody>\n";
			$x = 0;
			if($this->colecao->possuiItens()){
				$item = $this->colecao->retornarItem();
				$mapeador  = controlePadrao::pegarEstrutura($item);
				while($item = $this->colecao->avancar()){
					$retorno.= $this->abrirLinha($item,++$x);
					foreach($chaves as $chave){
						$campo = $this->campos[$chave];
						$classeHTML = null;
						$alinhamento = ($campo['alinhamento']) ? "align='{$campo['alinhamento']}'" : '' ;
						switch(true){
							case(isset($campo['campoPersonalizado'])):
								$retorno.="\t\t<td {$alinhamento}>".$this->$campo['campoPersonalizado']($item)."</td>\n";
							break;
							case(isset($campo['campoLink'])):
								$controle = definicaoEntidade::controle($item,'verEdicao');
								$pegar = 'pegar'.ucfirst($campo['campoLink']);
								$link = sprintf("?c=%s&amp;chave=%s",$controle,$item->valorChave());
								$classeHTML = '';
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
											switch(true){
												case(($valorDoCampo instanceof TData)):
													$classeHTML = "class='data'";
												break;
												case(($valorDoCampo instanceof TNumerico)):
													$classeHTML = "class='numerico'";
												break;
											}
											$valorDoCampo = $valorDoCampo->__toString();
										}
								}
								$retorno.="\t\t<td {$alinhamento} {$classeHTML}><a href='{$link}' >{$valorDoCampo}</a></td>\n";
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
											switch(true){
												case(($valorDoCampo instanceof TData)):
													$classeHTML = "class='data'";
												break;
												case(($valorDoCampo instanceof TNumerico)):
													$classeHTML = "class='numerico'";
												break;
											}
											$valorDoCampo = $valorDoCampo->__toString();
										}
								}
								$retorno.="\t\t<td {$alinhamento} {$classeHTML}>{$valorDoCampo}</td>\n";
							break;
						}
					}
					$retorno.= "\t</tr>\n";
				}
				$retorno.="</tbody></table>\n";
				return $retorno;
			}else{
				$largura = count($this->campos);
				$mensagem = $this->inter->pegarMensagem('registrosNaoEncontrados');
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
	* Método de abertura da linha da listagem
	* @param mixed item a ser apresentado na listagem
	* @param integer número da linha a ser apresentada
	*/
	public function abrirLinha($item,$nrLinha){
		if($nrLinha%2){
			return "\t<tr class='linhaListagem1'>\n";
		}else{
			return "\t<tr class='linhaListagem2'>\n";
		}
	}
	/**
	* Monta o paginador da listagem
	* @return string paginador da listagem
	*/
	public function montarPaginador(){
		$retorno = '';
		$paginas = $this->inter->pegarTexto('paginas');
		if($this->pagina->pegarTamanhoGeral() > $this->pagina->pegarTamanhoPagina()){
			$retorno.="<div class='container3 ui-state-default ui-corner-bottom'>\n";
			$retorno.="	<div class='a'></div>\n";
			$retorno.="	<div class='b'></div>\n";
			$retorno.="	<div class='c'></div>\n";
			$retorno.="	<div class='d'></div>\n";
			$retorno.="	<div class='e'></div>\n";
			$retorno.="	<div class='f'></div>\n";
			$retorno.="	<div class='g'></div>\n";
			$retorno.="	<div class='h'></div>\n";
			$retorno.="	<div class='texto'>\n";
			$retorno.="	<p>&nbsp;\n";
			$paginas = ($this->pagina->pegarTamanhoGeral()/$this->pagina->pegarTamanhoPagina() +1);
			$paginas = (($this->pagina->pegarTamanhoGeral()%$this->pagina->pegarTamanhoPagina()) == 0) ? $paginas -1 : $paginas;

			$linkPrimeiro = sprintf('?c=%s&amp;pagina=%s',$this->controle, 1);
			$linkAnterior = sprintf('?c=%s&amp;pagina=%s',$this->controle, $this->pagina->pegarPagina() - 1);
			$linkProximo = sprintf('?c=%s&amp;pagina=%s',$this->controle, $this->pagina->pegarPagina() + 1);
			$linkUltimo = sprintf('?c=%s&amp;pagina=%s',$this->controle, (int)$paginas);

			$classe = "class='ui-widget-content ui-corner-all'";
			
			$retorno.= $this->pagina->pegarPagina() == 1 ? "<span {$classe}>Primeiro</span>" : "<span class='ui-state-default ui-corner-all'><a href='{$linkPrimeiro}'>Primeiro</a></span>";
			$retorno.= $this->pagina->pegarPagina() == 1 ? "<span {$classe}>Anterior</span>" : "<span class='ui-state-default ui-corner-all'><a href='{$linkAnterior}'>Anterior</a></span>";

			$retorno.= '<select id="seletorDePagina">';

			for($i=1;$i <= $paginas;$i++){

				$link = sprintf('?c=%s&amp;pagina=%s',$this->controle, $i);
				if($i == $this->pagina->pegarPagina()){
					$retorno.="<option value='{$i}' selected='selected'>{$i}</option>";
				}else{
					$retorno.="<option value='{$i}'>{$i}</option>";
				}
			}
			
			$retorno.="</select>";

			$retorno.= $this->pagina->pegarPagina() == (int)$paginas ? "<span {$classe}>Próximo</span>" : "<span class='ui-state-default ui-corner-all'><a href='{$linkProximo}'>Próximo</a></span>";
			$retorno.= $this->pagina->pegarPagina() == (int)$paginas ? "<span {$classe}>Último</span>" : "<span class='ui-state-default ui-corner-all'><a href='{$linkUltimo}'>Último</a></span>";

			$retorno.="		</p>\n	</div>\n";
			$retorno.="</div>\n";
		}

		return $retorno;
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return string texto de saída da classe
	*/
	function __toString(){
		try{
			$classe = definicaoEntidade::internacionalizacao($this->controle);
			$this->inter = new $classe();
			$this->definirListagem();
			$retorno = $this->montarListagem();
			$retorno.= $this->montarPaginador();
			return $retorno;
		}
		catch(erro $e){
			return $e->getMessage();
		}
	}
	
	public function colunaExcluir( $negocio )
	{
		$controle = definicaoEntidade::controle($negocio,'excluir');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());		
		return "<a href='javascript:if(confirm(\"Deseja mesmo excluir este item?\")){window.location=\"{$link}\";}' title='Excluir registro'><img src='.sistema/icones/delete.png' border='0' /></a>";
	}
	
	public function colunaEditar( $negocio )
	{
		$controle = definicaoEntidade::controle($negocio,'verEdicao');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());		
		return "<a href='{$link}' title='Alterar registro.'><img src='.sistema/icones/pencil.png' border='0' /></a>";
	}
}
?>
