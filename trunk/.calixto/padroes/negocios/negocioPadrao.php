<?php
/**
* Classe de representação de uma camada de negócio
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package FrameCalixto
* @subpackage Negocio
*/
abstract class negocioPadrao extends negocio{
	/**
	 * Objeto padrão de acesso aos dados
	 * @var persistente
	 */
	protected $__persistente;
	/**
	 * fila de filtro de pesquisa
	 * @var array
	 */
	protected $__filtroDePesquisa = array();
	/**
	 * Cache de objetos encontrados para a pesquisa geral
	 * @var array 
	 */
	//private static $cachePesquisaGeral;
	/**
	* objeto de conexão com o banco de dados
	* @var conexao
	*/
	protected $conexao;
	/**
	* @var array array com a estrutura do mapeamento  entre persistente e negócio
	* criado para a execução de cache
	*/
	private static $estrutura;
	/**
	* @var internacionalizacaoPadrao internacionalização do negócio
	*/
	protected $inter;
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public function __construct(conexao $conexao = null){
		$this->__filtroDePesquisa = new colecaoPadraoFiltro();
		try{
			if($conexao){
				$this->conexao = $conexao;
			}else{
				$this->conexao = conexao::criar();
			}
			$this->inter = $this->internacionalizacao();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Metodo construtor
	* @param conexao (opcional) conexão com o banco de dados
	*/
	public final function conectar(conexao $conexao = null){
		try{
			switch(true){
				case($conexao):
					$this->passarConexao($conexao);
				break;
				case(is_resource($this->pegarConexao()->pegarConexao())):
				break;
				default:
					$this->passarConexao(conexao::criar());
			}
			$props = array_keys(get_object_vars($this));
			foreach($props as $prop){
				if(is_object($this->$prop) && method_exists($this->$prop,'conectar') && !($this->$prop instanceof conexao))
					$this->$prop->conectar($this->conexao);
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	public function __call($metodo, $parametros){
		try{
			if (preg_match('/(pegar|passar|filtrar)(.*)/', $metodo, $resultado)) {
				$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
				$r = new ReflectionProperty(get_class($this), $var);
				if(!$r->getName()) throw new erro();
				switch($resultado[1]){
					case 'filtrar':
						$this->adicionarFiltro($var, $parametros[0]);
						return;
					break;
					case 'passar':
						$this->$var = $parametros[0];
						return;
					break;
					default;
						if($r->isStatic()) throw new erro('Atributo statico protegido.');
						return $this->$var;
				}
			}
			$persistente = $this->pegarPersistente();
			$r = new ReflectionClass(definicaoEntidade::persistente($this));
			$a = null;
			foreach($r->getMethods(ReflectionMethod::IS_PUBLIC) as $obMetodo){
				if($obMetodo->class == definicaoEntidade::persistente($this) && $metodo == $obMetodo->name){
					$chamada = "\$persistente->{$metodo}( ";
					foreach($parametros as $index =>$parametro){
						$chamada .="\$parametros[{$index}],";
					}
					$chamada = substr($chamada,0,-1).')';
					eval("\$a = {$chamada};");
					return $a;
				}
			}
			throw new erro('Chamada inexistente!');
		}
		catch (ReflectionException $e){
			$propriedade = get_class($this).'::'.$var;
			throw new erro("Propriedade [{$propriedade}] inexistente!");
		}
		catch(erro $e){
			$ar = debug_backtrace();
			$m = get_class($this).'::'.$metodo;
			throw new erro("Método inexistente [{$m}] chamado no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}");
		}
    }
	/**
	 * @return array Retorna os atributos do objeto
	 */
	public function __atributos() {
		$vars = parent::__atributos();
		unset($vars['conexao']);
		unset($vars['__persistente']);
		unset($vars['__filtroDePesquisa']);
		return $vars;
	}
    public function vetor(){
        $vars = get_object_vars($this);
		unset($vars['conexao']);
		unset($vars['inter']);
		unset($vars['__persistente']);
		unset($vars['__filtroDePesquisa']);
		unset($vars['__filtroDePesquisa']);
        return $vars;
    }
	/**
	* retorna um array de mapeamento da internacionalização do negocio
	* @return array mapeamento de internacionalização
	*/
	public function internacionalizacao(){
		$internacionalizacao = definicaoEntidade::internacionalizacao($this);
		return new $internacionalizacao();
	}
	/**
	* retorna um array de mapeamento entre persistente e negócio
	* @return array mapeamento persistente e negocio
	*/
	public function pegarMapeamento($arquivoXML = null){
		try{
			if(!isset(negocioPadrao::$estrutura[get_class($this)])){
				$arquivoXML = definicaoArquivo::pegarXmlEntidade($this,$arquivoXML);
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
							if(isset($propriedade->dominio)){
								$dominio = array();
								foreach($propriedade->dominio->opcao as $opcao){
									$dominio[strval($opcao['id'])] = strval($opcao);
								}
							}else{
								$dominio = false;
							}
							$mapeador[strval($propriedade['id'])] = array(
								'propriedade'		=> strval($propriedade['id']		),
								'tipo'				=> strval($propriedade['tipo']			),
								'campo'				=> strtolower(strval($propriedade->banco['nome']	)),
								'obrigatorio'		=> strval($propriedade['obrigatorio']	),
								'indiceUnico'		=> strtolower(strval($propriedade['indiceUnico'])) == 'sim',
								'dominio'			=> $dominio,
								'descritivo'		=> strval($propriedade['descritivo']		),
								'classeAssociativa'	=> strval($propriedade['classeAssociativa']		),
								'metodoLeitura'		=> strval($propriedade['metodoLeitura']		)
							);
						}
					break;
				}
				negocioPadrao::$estrutura[get_class($this)] = $mapeador;
			}
			return negocioPadrao::$estrutura[get_class($this)];
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	abstract function nomeChave();
	/**
	* Retorna o valor da propriedade chave de negócio
	* @param string nome da chave de negocio
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
	* @return string descrição do objeto
	*/
	public function valorDescricao(){
		try{
			$mapeador = $this->pegarMapeamento();
			$descricao = array();
			foreach($mapeador as $valor){
				if($valor['descritivo']){
					$metodo = "pegar{$valor['propriedade']}";
					if($valor['classeAssociativa']){
						$classe = new $valor['classeAssociativa']();
						$colecao = $classe->$valor['metodoLeitura']();
						if($this->$metodo()){
							$objetoPai = $colecao->pegar($this->$metodo());
							// ATENÇÃO ESTA CHAMADA RECURSIVA PODE SE TORNAR INIFINITA !!!!
							// CASO ISTO OCORRA ESPECIALISE O MÉTODO ...
							$descricao[$valor['descritivo']] = $objetoPai->valorDescricao();
						}else{
							$descricao[$valor['descritivo']] = null;
						}
					}else{
						$descricao[$valor['descritivo']] = is_object($this->$metodo()) ? $this->$metodo()->__toString() : $this->$metodo();
					}
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
	* @param SimpleXMLElement objeto xml para a estruturação
	*/
	public function xmlPraNegocio(SimpleXMLElement $xml){
		foreach($xml as $index => $propriedade){
			$metodo = 'passar'.ucFirst($index);
			$this->$metodo(strval($propriedade));
		}
	}
	/**
	* Método que instrui ao negócio como se estruturar através de um vetor
	* @param array correlativa entre campo e valor
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
			$variaveisClasse = $this->__atributos();
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
	public function limparFiltro(){
		$this->__filtroDePesquisa = new colecaoPadraoFiltro();
	}
	protected function adicionarFiltro($campo, operador $operador){
		$this->__filtroDePesquisa->$campo = $operador;
	}
	/**
	 * Metodo que preenche todos os atributos de negocio com um valor
	 * @param string $valorDePreenchimento
	 * @return colecaoPadraoNegocio
	 */
	public function pesquisaGeral($valorDePreenchimento,pagina $pagina = null,$recursividade = 1){
		//if(isset(negocioPadrao::$cachePesquisaGeral[get_class($this)])) return negocioPadrao::$cachePesquisaGeral[get_class($this)];
		$recursividade--;
		$mapeador = $this->pegarMapeamento();
		$filtrou = false;
		foreach($mapeador as $valor){
			$valorPassado = null;
			if($valor['propriedade'] == $this->nomeChave()) continue ;
			if(!empty($valor['classeAssociativa']) && ($valor['classeAssociativa'] != get_class($this))){
				if(!$recursividade) continue ;
				$associativa = new $valor['classeAssociativa']();
				$colecaoAssociativa = $associativa->pesquisaGeral($valorDePreenchimento,new pagina(0), $recursividade);
				$chaves = $colecaoAssociativa->gerarVetorDeAtributo($associativa->nomeChave());
				if($chaves) $valorPassado = operador::dominio($chaves,operador::restricaoOU);
			}else{
				switch(true){
					case($valor['tipo'] == 'texto'):
						$valorPassado = operador::generico($valorDePreenchimento,operador::restricaoOU);
					break;
					case($valor['tipo'] == 'data'):
						$data = new TData($valorDePreenchimento);
						if($data->validar()) $valorPassado = operador::igual($data,operador::restricaoOU);
					break;
					default:
						if(is_numeric($valorDePreenchimento)) $valorPassado = operador::igual($valorDePreenchimento,operador::restricaoOU);
					break;
				}
			}
			if($valorPassado){
				$filtrou = true;
				$this->adicionarFiltro($valor['propriedade'],$valorPassado);
			}
		}
		if(!$filtrou) return new colecaoPadraoNegocio();
		if(!$pagina) $pagina = new pagina(0);
		//negocioPadrao::$cachePesquisaGeral[get_class($this)] = $this->vetorPraColecao($arResultadoLeitura);
		//return negocioPadrao::$cachePesquisaGeral[get_class($this)];
		return $this->pesquisar($pagina);
	}
	/**
	* Método que retorna a persistente referente ao negócio
	*/
	public function pegarPersistente(){
		try{
			if($this->__persistente) return $this->__persistente;
			$persistente = definicaoEntidade::persistente($this);
			return $this->__persistente = new $persistente($this->pegarConexao());
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Verifica a existência de do objeto no banco de dados pela chave
	* @param string $identificador 
	*/
	public function existe($identificador){
		$clone = clone $this;
		return (boolean) $clone->ler($identificador)->valorChave();
	}
	/**
	* Executa o comando de leitura do objeto
	* @param string chave nica de identificação do registro
	*/
	public function ler($identificador){
		try{
			$estrutura = $this->pegarMapeamento();
			if(!$identificador && ($estrutura[$this->nomeChave()]['obrigatorio'] == 'sim')){
				return $this;
			}
			$persistente = $this->pegarPersistente();
            $array = $persistente->ler($identificador);
			if(is_array($array))
			$this->vetorPraNegocio($array);
			return $this;
		}
		catch(erro $e){
			throw $e;
		}
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
					$persistente->alterar($this->negocioPraVetor(),$this->valorChave());
				break;
				default:
					$this->valorChave($persistente->gerarSequencia());
					$this->verificarAntesInserir();
					$persistente->inserir($this->negocioPraVetor());
					if(($id = $persistente->pegarUltimaSequencia())) $this->valorChave($id);
				break;
			}
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Executa o comando de importação do objeto
	*/
	public function importar(){
		$classe = get_class($this);
		$valorChave = null;
		$indiceUnico = $this->indiceUnico();
		if($indiceUnico){
			$negocio = new $classe($this->conexao);
			foreach ($indiceUnico as $propriedade) {
				$propriedade = ucfirst($propriedade);
				$metodoPassar = "passar{$propriedade}";
				$metodoPegar = "pegar{$propriedade}";
				$negocio->$metodoPassar($this->$metodoPegar());
			}
			$colecao = $negocio->pesquisar(new pagina(0));
			if($colecao->possuiItens())	$valorChave = $colecao->pegar()->valorChave();
		}
		$this->valorChave($valorChave);
		$this->gravar();
	}
	/**
	* Retorna um array com os campos que formam o índice único de negócio
	* @return array
	*/
	public function indiceUnico(){
		$mapeador = $this->pegarMapeamento();
		$arIndiceUnico = array();
		foreach ($mapeador as $campo) {
			if($campo['indiceUnico']){
				$arIndiceUnico[] = $campo['propriedade'];
			}
		}
		return $arIndiceUnico;
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		$mapeador = $this->pegarMapeamento();
		$variaveisClasse = array_keys(get_class_vars(get_class($this)));
		$indicesUnicos = array();
		foreach($mapeador as $valor){
			// Testa campos obrigatórios
			if(($valor['propriedade'] != $this->nomeChave()) && ($valor['obrigatorio'] == 'sim') && in_array($valor['propriedade'], $variaveisClasse)){
				$metodo = "pegar{$valor['propriedade']}";
				$conteudo = $this->$metodo();
				$conteudo = trim("{$conteudo}");
				if(empty($conteudo)){
					throw new erroNegocio(sprintf($this->inter->pegarMensagem('obrigatorio'),$this->inter->pegarPropriedade($valor['propriedade'])));
				}
			}
			if($valor['indiceUnico']) $indicesUnicos[] = $valor['propriedade'];
		}
		if($indicesUnicos){
			$classe = get_class($this);
			$negocio = new $classe($this->pegarConexao());
			foreach($indicesUnicos as $propriedade){
				$negocio->{"passar{$propriedade}"}(operador::igual($this->{"pegar{$propriedade}"}()));
			}
			$colecao = $negocio->pesquisar();//'Registro já cadastrado!'
			if($colecao->contarItens()) throw new erroNegocio($this->inter->pegarMensagem('repetido'));
		}
		return true;
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a alteração
	* @param negocio objeto antes da alteração .
	*/
	public function verificarAntesAlterar($negocio){
		$mapeador = $this->pegarMapeamento();
		$variaveisClasse = array_keys(get_class_vars(get_class($this)));
		$indicesUnicos = array();
		foreach($mapeador as $valor){
			$campo = $valor['campo'];
			if(($valor['obrigatorio'] == 'sim') && in_array($valor['propriedade'], $variaveisClasse)){
				$metodo = "pegar{$valor['propriedade']}";
				$conteudo = $this->$metodo();
				$conteudo = trim("{$conteudo}");
				if(empty($conteudo)){
					throw new erroNegocio(sprintf($this->inter->pegarMensagem('obrigatorio'),$this->inter->pegarPropriedade($valor['propriedade'])));
				}
			}
			if($valor['indiceUnico']) $indicesUnicos[] = $valor['propriedade'];
		}
		if($indicesUnicos){
			$classe = get_class($this);
			$negocio = new $classe($this->pegarConexao());
			foreach($indicesUnicos as $propriedade){
				$negocio->{"passar{$propriedade}"}(operador::igual($this->{"pegar{$propriedade}"}()));
			}
			$colecao = $negocio->pesquisar();//'Registro já cadastrado!'
			$colecao->removerItem($this->valorChave());
			if($colecao->contarItens()) throw new erroNegocio($this->inter->pegarMensagem('repetido'));
		}
	}
    /**
    * Executa o comando de exclusão do objeto
    */
    public function excluir(){
        try{
            $this->ler($this->valorChave());
            $this->verificarAntesExcluir();
            $persistente = $this->pegarPersistente();
			if($this->pegarConexao()->pegarTipo() == conexao::sqlite){
				if($persistente->possuiDependentes($this->valorChave()))
					throw new erroNegocio(sprintf($this->inter->pegarMensagem('dependente'),$this->valorDescricao()));
			}
            $persistente->excluir($this->valorChave());
        }
        catch(Erro $e){
			if($this->pegarConexao()->pegarTipo() == conexao::sqlite){
				throw $e;
			}
            $persistente = $this->pegarPersistente();
            if($persistente->possuiDependentes($this->valorChave()))
                throw new erroNegocio(sprintf($this->inter->pegarMensagem('dependente'),$this->valorDescricao()));
            throw $e;
        }
    }
	/**
	* Método utilizado para efetuar as verificações antes de executar a exclusão
	*/
	public function verificarAntesExcluir(){
		try{
			return;
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	 * Monta uma colecaoPadraoFiltro para a persistente
	 * @param colecaoPadraoFiltro $filtro
	 * @return colecaoPadraoFiltro 
	 */
	protected function montarFiltroParaPersistente(colecaoPadraoFiltro $filtro){
		$arMapeamento = $this->pegarMapeamento();
		$novoFiltro = new colecaoPadraoFiltro();
		foreach($filtro->itens as $campo => $item ){
			list($campo,$operador) = each($item);
			$novoFiltro->$arMapeamento[$campo]['campo'] = $operador;
		}
		return $novoFiltro;
	}
	/**
	* Retorna uma coleção com os negócios pesquisados
	* @param pagina pagina referente
	* @param filtro dados de pesquisa (não obrigatorio)
	* @return colecaoPadraoNegocio
	*/
	public function pesquisar(pagina $pagina = null, $filtro = null){
		try{
			if(!$pagina) $pagina = new pagina(0);
			$persistente = $this->pegarPersistente();
			if($this->__filtroDePesquisa->possuiItens()){
				$arResultadoLeitura = $persistente->pesquisar(
						$this->montarFiltroParaPersistente($filtro ? $filtro : $this->__filtroDePesquisa)
						,$pagina);
			}else{
				$arResultadoLeitura = $persistente->pesquisar($this->negocioPraVetor(),$pagina);
			}
			return $this->vetorPraColecao($arResultadoLeitura);
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Retorna a quantidade de objetos que o metodo pesquisar irá retornar
	* @param filtro dados de pesquisa (não obrigatorio)
	* @return integer
	*/
	public function totalDePesquisar($filtro = null){
		try{
			$persistente = $this->pegarPersistente();
			return $persistente->totalDePesquisar(
				$this->montarFiltroParaPersistente($filtro ? $filtro : $this->__filtroDePesquisa));
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Retorna uma coleção com todos os negócios
	* @return colecaoPadraoNegocio
	*/
	public function lerTodos($pagina = null){
		try{
			$persistente = $this->pegarPersistente();
			if($pagina){
				$arResultadoLeitura = $persistente->lerTodosPaginado($pagina);
			}else{
				$arResultadoLeitura = $persistente->lerTodos();
			}
			return $this->vetorPraColecao($arResultadoLeitura);
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	 * Converte um array lido da persistente para uma coleção de negócios padronizados
	 *
	 * @param array $arResultadoLeitura
	 * @return colecaoPadraoNegocio
	 */
	public function vetorPraColecao($arResultadoLeitura){
		$itens = array();
		if(is_array($arResultadoLeitura)){
			$classe = get_class($this);
			if($this->nomeChave()){
				foreach($arResultadoLeitura as $array){
					$negocio = new $classe($this->conexao);
					$negocio->vetorPraNegocio($array);
					$itens[$negocio->valorChave()] = $negocio;
				}
			}else{
				foreach($arResultadoLeitura as $array){
					$negocio = new $classe($this->conexao);
					$negocio->vetorPraNegocio($array);
					$itens[] = $negocio;
				}
			}
		}
		return new colecaoPadraoNegocio($itens,$this->pegarConexao());
	}
	/**
	 * Retorna um array com as propriedades e a classes associativas com os métodos de leitura
	 *
	 * @param negocioPadrao $negocio
	 */
	public function mapearClassesAssociativas(negocioPadrao $negocio){
		$retorno = array();
		foreach ($this->pegarMapeamento() as $campo) {
			if ($campo['classeAssociativa'] == get_class($negocio)) {
				$retorno[] = $campo;
			}
		}
		return $retorno;
	}
}
?>