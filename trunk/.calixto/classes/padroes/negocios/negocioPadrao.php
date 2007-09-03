<?php
/**
* Classe de representação de uma camada de negócio
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Infra-estrutura
* @subpackage Negocio
*/
abstract class negocioPadrao extends negocio{
	/**
	* @var [array] array com a estrutura do mapeamento  entre persistente e negócio
	* criado para a execução de cache
	*/
	private static $estrutura;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		parent::__construct($conexao);
		$this->inter = $this->internacionalizacao();
	}
	/**
	* retorna um array de mapeamento da internacionalização do negocio
	* @return [array] mapeamento de internacionalização
	*/
	public function internacionalizacao(){
		$internacionalizacao = definicaoEntidade::internacionalizacao($this);
		return new $internacionalizacao();
	}
	/**
	* retorna um array de mapeamento entre persistente e negócio
	* @return [array] mapeamento persistente e negocio
	*/
	public function mapearNegocio($arquivoXML){
		try{
			$mapeador = array();
			switch(true){
				case !($arquivoXML):
				break;
				case !(is_file($arquivoXML)):
					throw new erroInclusao("Arquivo [$arquivoXML] inexistente!");
				break;
				case !(is_readable($arquivoXML)):
					throw new erroInclusao("Arquivo [$arquivoXML] sem permissï¿½ de leitura!");
				break;
				default:
					$xml = simplexml_load_file($arquivoXML);
					foreach($xml->propriedades->propriedade as $propriedade){
						if(isset($propriedade->dominio)){
							$dominio = array();
							foreach($propriedade->dominio->opcao as $opcao){
								$dominio[caracteres($opcao['id'])] = caracteres($opcao);
							}
						}else{
							$dominio = false;
						}
						$mapeador[] = array(
							'propriedade'		=> caracteres($propriedade['id']		),
							'tipo'				=> caracteres($propriedade['tipo']			), 
							'campo'				=> strtolower(caracteres($propriedade->banco['nome']	)),
							'obrigatorio'		=> caracteres($propriedade['obrigatorio']	),
							'dominio'			=> $dominio,
							'descritivo'		=> caracteres($propriedade['descritivo']		),
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
	* Método que retorna o array com o mapeamento entre persistente e negócio
	* este método pode ser sobrescrito para efetuar um cache de arquivo e evitar processamento de leitura
	* @return [vetor] de mapeamento  entre persistente e negócio
	*/
	public function pegarMapeamento($arquivoXML = null){
		try{
			if(!isset(negocioPadrao::$estrutura[get_class($this)])){
				return negocioPadrao::$estrutura[get_class($this)] = $this->mapearNegocio(definicaoArquivo::pegarXmlEntidade($this,$arquivoXML));
			}else{
				return negocioPadrao::$estrutura[get_class($this)];
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	abstract function nomeChave();
	/**
	* Retorna o valor da propriedade chave de negócio
	* @param [string] nome da chave de negocio
	*/
	public function valorChave($chave = null){
		try{
			if($chave){
				$metodo = 'passar'.ucfirst($this->nomeChave());
				$this->$metodo($chave);
				return;
			}
			$metodo = 'pegar'.$this->nomeChave();
			return $this->$metodo();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de apresentação simplificada do objeto de negócio
	* @return [string] descrição do objeto
	*/
	public function valorDescricao(){
		try{
			$mapeador = $this->pegarMapeamento();
			$descricao = array();
			foreach($mapeador as $valor){
				if($valor['descritivo']){
					$metodo = "pegar{$valor['propriedade']}";
					$descricao[$valor['descritivo']] = $this->$metodo();
				}
			}
			ksort($descricao);
			return implode('-',$descricao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que instrui ao negócio como se estruturar através de um vetor
	* @param [SimpleXMLElement] objeto xml para a estruturação
	*/
	public function xmlPraNegocio(SimpleXMLElement $xml){
		foreach($xml as $index => $propriedade){
			$metodo = 'passar'.ucFirst($index);
			$this->$metodo(caracteres($propriedade));
		}
	}
	/**
	* Método que instrui ao negócio como se estruturar através de um vetor
	* @param [array] correlativa entre campo e valor
	*/
	public function vetorPraNegocio(array $vetor){
		try{
			array_change_key_case($vetor);
			$mapeador = $this->pegarMapeamento();
			foreach($mapeador as $valor){
				if(isset($vetor[$valor['campo']])){
					$metodo = "passar{$valor['propriedade']}";
					$this->$metodo($vetor[$valor['campo']]);
				}
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que instrui ao negócio como se estruturar para um vetor
	*/
	public function negocioPraVetor(){
		try{
			$vetor = array();
			$mapeador = $this->pegarMapeamento();
			$variaveisClasse = array_keys(get_class_vars(get_class($this)));
			foreach($mapeador as $valor){
				$campo = $valor['campo'];
				if(in_array($valor['propriedade'], $variaveisClasse)){
					$metodo = "pegar{$valor['propriedade']}";
					$vetor[$valor['campo']] = $this->$metodo();
				}
			}
			return $vetor;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método que retorna a persistente referente ao negócio
	*/
	public function pegarPersistente(){
		try{
			$persistente = definicaoEntidade::persistente($this);
			return new $persistente($this->pegarConexao());
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de leitura do objeto 
	* @param [string] chave nica de identificação do registro
	*/
	public function ler($identificador){
		try{
			$persistente = $this->pegarPersistente();
			if(is_array($array = $persistente->ler($identificador)))
			$this->vetorPraNegocio($array);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de gravação do objeto
	* @param [boleano] caso verdadeiro irá incluir com a chave de negócio passada caso falso irá verificar, se foi passada a chave irá alterar senão irá incluir
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
					$persistente->alterar($this->negocioPraVetor(),$this->valorChave());
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
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		try{
			$mapeador = $this->pegarMapeamento();
			$variaveisClasse = array_keys(get_class_vars(get_class($this)));
			foreach($mapeador as $valor){
				// Testa campos obrigatórios
				if(($valor['propriedade'] != $this->nomeChave()) && ($valor['obrigatorio'] == 'sim') && in_array($valor['propriedade'], $variaveisClasse)){
					$metodo = "pegar{$valor['propriedade']}";
					if($this->$metodo() == null){
						throw new erroNegocio(sprintf($this->inter->pegarMensagem('obrigatorio'),$this->inter->pegarPropriedade($valor['propriedade'])));
					}
				}
			}
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a alteração
	* @param [negocio] objeto antes da alteração .
	*/
	public function verificarAntesAlterar($negocio){
		try{
			$mapeador = $this->pegarMapeamento();
			$variaveisClasse = array_keys(get_class_vars(get_class($this)));
			foreach($mapeador as $valor){
				$campo = $valor['campo'];
				if(($valor['obrigatorio'] == 'sim') && in_array($valor['propriedade'], $variaveisClasse)){
					$metodo = "pegar{$valor['propriedade']}";
					if($this->$metodo() == null){
						throw new erroNegocio(sprintf($this->inter->pegarMensagem('obrigatorio'),$this->inter->pegarPropriedade($valor['propriedade'])));
					}
				}
			}
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de exclusão do objeto
	*/
	public function excluir(){
		try{
			$persistente = $this->pegarPersistente();
			$persistente->excluir($this->valorChave());
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Retorna uma coleção com os negócios pesquisados
	* @param [pagina] pagina referente
	* @param [filtro] dados de pesquisa (nï¿½ obrigatï¿½io)
	* @return [colecao]
	*/
	public function pesquisar(pagina $pagina, $filtro = null){
		try{
			$persistente = $this->pegarPersistente();
			if(is_subclass_of($filtro, 'filtro')){
				$arResultadoLeitura = $persistente->pesquisar($filtro,$pagina);
			}else{
				$arResultadoLeitura = $persistente->pesquisar($this->negocioPraVetor(),$pagina);
			}
			$itens = array();
			if(is_array($arResultadoLeitura)){
				$classe = get_class($this);
				if($this->nomeChave()){
					foreach($arResultadoLeitura as $array){
						$negocio = new $classe();
						$negocio->vetorPraNegocio($array);
						$itens[$negocio->valorChave()] = $negocio;
					}
				}else{
					foreach($arResultadoLeitura as $array){
						$negocio = new $classe();
						$negocio->vetorPraNegocio($array);
						$itens[] = $negocio;
					}
				}
			}
			$colecao = new colecaoPadraoNegocio($itens,$this->pegarConexao());
			return $colecao;
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Retorna uma coleção com todos os negócios
	* @return [colecao]
	*/
	public function lerTodos($pagina = null){
		try{
			$persistente = $this->pegarPersistente();
			if($pagina){
				$arResultadoLeitura = $persistente->lerTodosPaginado($pagina);
			}else{
				$arResultadoLeitura = $persistente->lerTodos();
			}
			$itens = array();
			if(is_array($arResultadoLeitura)){
				$classe = get_class($this);
				if($this->nomeChave()){
					foreach($arResultadoLeitura as $array){
						$negocio = new $classe();
						$negocio->vetorPraNegocio($array);
						$itens[$negocio->valorChave()] = $negocio;
					}
				}else{
					foreach($arResultadoLeitura as $array){
						$negocio = new $classe();
						$negocio->vetorPraNegocio($array);
						$itens[] = $negocio;
					}
				}
			}
			$colecao = new colecaoPadraoNegocio($itens,$this->pegarConexao());
			return $colecao;
		}
		catch(Erro $e){
			throw $e;
		}
	}
}
?>