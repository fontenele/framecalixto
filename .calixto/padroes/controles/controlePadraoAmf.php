<?php
/**
* Classe de definição da camada de controle para Amf
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoAmf extends controlePadrao {
	/**
	* @var pagina pagina a ser listada
	*/
	public $pagina;
	/**
	* @var negocioPadrao objeto de negócio que será utilizado para gerar a pesquisa
	*/
	public $filtro;
	/**
	* @var internacionalizacao textos para a página
	*/
	public $inter;
	
	public function inicial(){
		$this->visualizacao = null;
		$this->inter = array();
		$this->definirPaginaAtual();
		$this->definirFiltro();
		$this->registrarInternacionalizacao($this,$this->inter);
	}
	public function pegarEstruturaTela(){
		$negocio = $this->pegarFiltro();
		$estrutura = controlePadrao::pegarEstrutura($negocio);
		return $estrutura;
	}
	public function lerInternacionalizacao(){
		return $this->inter;
	}
	/**
	* Método que define o objeto de negócio que executará a pesquisa
	*/
	public function definirFiltro(){
		$negocio = definicaoEntidade::negocio($this);
		$this->filtro = ($this->sessao->tem('filtro')) ? $this->sessao->pegar('filtro'): new $negocio();
	}
	/**
	* Método que define a página atual a ser exibida
	*/
	protected function definirPaginaAtual(){
		$mapeador = controlePadrao::pegarEstrutura($this);
		if(isset($_GET['pagina'])){
			$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
			$this->pagina->passarPagina($_GET['pagina']);
			$this->sessao->registrar('pagina',$this->pagina);
		}else{
			$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
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
		
		$visualizacao['titulo']		= $inter->pegarTituloSistema();
		$visualizacao['subtitulo']	= $inter->pegarSubtituloSistema();
        if($inter->pegarTitulo()){
		$visualizacao['tituloEspecifico'] =
			sprintf(
                '%s - %s',
                $inter->pegarTitulo(),
                $inter->pegarTexto(	isset($_GET['c']) ? definicaoEntidade::funcionalidade($_GET['c']):	null)
            );
        }else{
    		$visualizacao['tituloEspecifico'] = $inter->pegarTexto(	isset($_GET['c']) ? definicaoEntidade::funcionalidade($_GET['c']):	null);
        }
		$internacionalizacao = $inter->pegarInternacionalizacao();
		if(isset($internacionalizacao['propriedade']))
		foreach($internacionalizacao['propriedade'] as $indice => $propriedade){
			if(isset($propriedade['nome'])){
				$var = 'nome'.ucfirst($indice);
				$visualizacao[$var] = strval($propriedade['nome']);
			}
			if(isset($propriedade['abreviacao'])){
				$var = 'abreviacao'.ucfirst($indice);
				$visualizacao[$var] = $propriedade['abreviacao'];
			}
			if(isset($propriedade['descricao'])){
				$var = 'descricao'.ucfirst($indice);
				$visualizacao[$var] = $propriedade['descricao'];
			}
			if(isset($propriedade['dominio'])){
				$var = 'dominio'.ucfirst($indice);
				$visualizacao[$var] = $propriedade['dominio'];
			}
		}
		if(isset($internacionalizacao['texto']))
		foreach($internacionalizacao['texto'] as $indice => $texto){
			$var = 'texto'.ucfirst($indice);
			$visualizacao[$var] = $texto;
		}
		if(isset($internacionalizacao['mensagem']))
		foreach($internacionalizacao['mensagem'] as $indice => $mensagem){
			$var = 'mensagem'.ucfirst($indice);
			$visualizacao[$var] = $mensagem;
		}
	}
	/**
	* Método para ler todos os registros
	*/
	public function lerTodos($negocio = null){
		if($negocio) return 123;
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio;
		
		$arRetorno = array();
		$coLerTodos = $negocio->lerTodos();
		
		if($coLerTodos->possuiItens()){
			while($negocio = $coLerTodos->avancar()){
				$arLinha = $negocio;
				$arRetorno[$negocio->valorChave()] = $arLinha;
			}
		}
		return $arRetorno;
	}
}
?>