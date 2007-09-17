<?php
/**
* Classe de definição da camada de internacionalização
* @package Infra-estrutura
* @subpackage Internacionalização
*/
class internacionalizacao extends objeto{
	/**
	* @var [array] array com a estrutura de internacionalização
	* criado para a execução de cache
	*/
	private static $estrutura;
	/**
	* Metodo criado para especificar a estrutura da internacionalizacao
	* @param [st] caminho do arquivo
	*/
	public function mapearInternacionalizacaoGeral(&$estrutura){
		$xml = simplexml_load_file(definicaoArquivo::pegarXmlInternacionalizacaoDoSistema());
		$estrutura['nome'] = strval($xml->entidade->nome);
		$estrutura['titulo'] = strval($xml->controles->titulo);
		$estrutura['tituloSistema'] = strval($xml->sistema->titulo);
		$estrutura['subtituloSistema'] = strval($xml->sistema->subtitulo);
		if(isset($xml->controles->textos))
		foreach($xml->controles->textos->texto as $texto){
			if(isset($texto['id'])) $estrutura['texto'][strval($texto['id'])] = strval($texto);
		}
		if(isset($xml->mensagens))
		foreach($xml->mensagens->mensagem as $mensagem){
			if(isset($mensagem['id'])) $estrutura['mensagem'][strval($mensagem['id'])] = strval($mensagem);
		}
	}
	/**
	* Metodo criado para especificar a estrutura da internacionalizacao
	* @param [st] caminho do arquivo
	*/
	public function mapearInternacionalizacao($arquivoXML){
		try{
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
					$this->mapearInternacionalizacaoGeral($estrutura);
					$estrutura['nome'] = strval($xml->entidade->nome);
					$estrutura['titulo'] = strval($xml->controles->titulo);
					if(isset($xml->entidade->propriedades))
					foreach($xml->entidade->propriedades->propriedade as $propriedade){
						if(isset($propriedade['nome'])) {
							$estrutura['propriedade'][strval($propriedade['nome'])]['nome'] = strval($propriedade->nome);
							$estrutura['propriedade'][strval($propriedade['nome'])]['abreviacao'] = strval($propriedade->abreviacao);
							$estrutura['propriedade'][strval($propriedade['nome'])]['descricao'] = strval($propriedade->descricao);
						}
						if(isset($propriedade->dominio)){
							$dominio = array();
							foreach($propriedade->dominio->opcao as $opcao){
								$dominio[strval($opcao['id'])] = strval($opcao);
							}
							$estrutura['propriedade'][strval($propriedade['nome'])]['dominio'] = $dominio;
						}
					}
					if(isset($xml->controles->textos))
					foreach($xml->controles->textos->texto as $texto){
						if(isset($texto['id'])) $estrutura['texto'][strval($texto['id'])] = strval($texto);
					}
					if(isset($xml->mensagens))
					foreach($xml->mensagens->mensagem as $mensagem){
						if(isset($mensagem['id'])) $estrutura['mensagem'][strval($mensagem['id'])] = strval($mensagem);
					}
				break;
			}
			return isset($estrutura) ? $estrutura : array();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que retorna a internacionalização
	* @return [vetor] internacionalização .
	*/
	public function pegarInternacionalizacao($arquivoXML = null){
		try{
			if(!isset(internacionalizacao::$estrutura[get_class($this)])){
				return internacionalizacao::$estrutura[get_class($this)] = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this,$arquivoXML));
			}else{
				return internacionalizacao::$estrutura[get_class($this)];
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que retorna o nome da entidade
	* @return [string] nome da entidade internacionalizada
	*/
	public function pegarNome(){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['nome']))
		return $estrutura['nome'];
	}
	/**
	* Método que retorna o título de apresentação da entidade
	* @return [string] título de apresentação da entidade internacionalizada
	*/
	public function pegarTituloSistema(){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['tituloSistema']))
		return $estrutura['tituloSistema'];
	}
	/**
	* Método que retorna o título de apresentação da entidade
	* @return [string] título de apresentação da entidade internacionalizada
	*/
	public function pegarSubtituloSistema(){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['subtituloSistema']))
		return $estrutura['subtituloSistema'];
	}
	/**
	* Método que retorna o título de apresentação da entidade
	* @return [string] título de apresentação da entidade internacionalizada
	*/
	public function pegarTitulo(){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['titulo']))
		return $estrutura['titulo'];
	}
	/**
	* Método que retorna o valor de uma propriedade
	* @param [string] Nome da propriedade a ser buscada
	* @param [string] Tipo do retorno da propriedade a ser buscada
	* @return [string] texto internacionalizado da propriedade
	*/
	public function pegarPropriedade($propriedade,$tipo = 'nome'){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['propriedade'][$propriedade][$tipo]))
		return $estrutura['propriedade'][$propriedade][$tipo];
	}
	/**
	* Método que retorna o valor de uma propriedade
	* @param [string] Nome da propriedade a ser buscada
	* @param [string] Indice da opcao a ser buscada
	* @return [string] texto internacionalizado da propriedade
	*/
	public function pegarOpcao($propriedade,$indice){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['propriedade'][$propriedade]['dominio'][$indice]))
		return $estrutura['propriedade'][$propriedade]['dominio'][$indice];
	}
	/**
	* Método que retorna o valor de um texto
	* @param [string] Identificador do texto
	* @return [string] texto internacionalizado
	*/
	public function pegarTexto($identificador){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['texto'][$identificador]))
		return $estrutura['texto'][$identificador];
	}
	/**
	* Método que retorna o valor de uma mensagem
	* @param [string] Identificador da mensagem
	* @return [string] mensagem internacionalizada
	*/
	public function pegarMensagem($identificador){
		$estrutura = $this->pegarInternacionalizacao();
		if(isset($estrutura['mensagem'][$identificador]))
		return $estrutura['mensagem'][$identificador];
	}
}
?>