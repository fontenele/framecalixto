<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_geradorDefinirEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->sessao->limpar();
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$this->visualizacao->entidade = VComponente::montar('input','entidade',null);
		$this->visualizacao->recriarBase = VComponente::montar('checkbox','recriarBase',null);
		$adicionar = VComponente::montar('botao','adicionar', $this->inter->pegarTexto('adicionar'));
		$adicionar->adicionarOnClick('teste(document.formulario.novaPropriedade);');
		$this->visualizacao->adicionar = $adicionar;
		$this->visualizacao->action = '?c=CUtilitario_geradorGerarFonte';
		$this->visualizacao->menuPrograma = VComponente::montar('menu de programa',null,null,null, array('definir'=>'javascript:validar();','entidades'=>'?c=CUtilitario_listarEntidade'));
		if(isset($_GET['entidade'])) $this->montarEntidade();
		$this->visualizacao->mostrar();
	}

	/**
	* Método de montagem da entidade
	*/
	function montarEntidade(){
		$negocio = 'N'.ucfirst($_GET['entidade']);
		$persistente = 'P'.ucfirst($_GET['entidade']);
		$internacionalizacao = 'I'.ucfirst($_GET['entidade']);
		$controle = 'C'.ucfirst($_GET['entidade']).'_verPesquisa';
		$json = new json();
		$negocio = new $negocio();
		$persistente = new $persistente($negocio->pegarConexao());
		$internacionalizacao = new $internacionalizacao();
		$mapNegocio['negocio'] = $negocio->pegarMapeamento();
		$mapNegocio['bd'] = $persistente->pegarEstrutura();
		$mapNegocio['inter'] = $internacionalizacao->pegarInternacionalizacao();
		$mapNegocio['controle'] = $this->mapearControle(definicaoArquivo::pegarXmlEntidade(definicaoEntidade::controle($negocio,'verPesquisa')));
		foreach($mapNegocio['negocio'] as $i => $map){
			$mapEntidade[$i]['negocio'] = $map;
			$mapEntidade[$i]['controle'] = $mapNegocio['controle'][$map['propriedade']];
			$mapEntidade[$i]['persistente'] = $mapNegocio['bd']['campo'][$map['campo']];
			$mapEntidade[$i]['inter'] = $mapNegocio['inter']['propriedade'][$map['propriedade']];
 			if(!isset($mapNegocio['bd']['campo'][$map['campo']]['chaveEstrangeira']))
				$mapEntidade[$i]['persistente']['chaveEstrangeira'] = false;
			$mapEntidade[$i]['persistente']['ordem'] = '';
			foreach($mapNegocio['bd']['ordem'] as $iOrdem => $ordem){
				$ordem = explode(' ',$ordem);
				if($map['campo'] == $ordem[0]){
					$mapEntidade[$i]['persistente']['ordem'] = $iOrdem;
					$mapEntidade[$i]['persistente']['tipoOrdem'] = isset($ordem[1]) ? true : false;
				}
			}
			$res = '';
			if(isset($mapEntidade[$i]['inter']['dominio'])){
				foreach($mapEntidade[$i]['inter']['dominio'] as $id => $valor){
					$res .= "[$id,$valor]";
				}
			}
			$mapEntidade[$i]['inter']['dominio'] = $res;
		}
		unset($mapNegocio['negocio']);
		unset($mapNegocio['controle']);
		unset($mapNegocio['inter']['propriedade']);
		unset($mapNegocio['inter']['mensagem']);
		unset($mapNegocio['inter']['texto']);
		unset($mapNegocio['bd']['campo']);
		$mapNegocio['entidade'] = $mapEntidade;
		$this->visualizacao->dados = $json->pegarJson(array($mapNegocio));
		$this->visualizacao->campos = $mapNegocio;
	}
}
?>