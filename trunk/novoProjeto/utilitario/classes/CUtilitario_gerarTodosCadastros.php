<?php
class CUtilitario_gerarTodosCadastros extends controle{
	protected $tabelas;
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'listarEntidade'));
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$this->tabelas = $persistente->lerTabelas();
		foreach($this->tabelas as $tabela){
			$arTabela = $persistente->lerTabela($tabela);
			$arDadosGerador = $this->prepararDados($tabela,$arTabela);
			CUtilitario_geradorGerarFonte::gerarFonte($this->visualizacao,$arDadosGerador);
		}
	}
	protected function prepararDados($nome,$dados){
		$res = array();
		if(strpos($nome, '.')){
			list($squema, $nome) = explode('.',$nome);
		}
		$arNomes = explode('_',$nome);
		$res['entidade'] = null;
		foreach($arNomes as $str){
			$res['entidade'] .=ucfirst($str);
		}
		$sequence = $this->nomeDaSequence($squema,$nome);
		foreach($dados as $idx => $dado){

			$indice = $idx+1;
			$res['en_nome'][$indice] = $this->nomePropriedade($dado);
			$res['en_abreviacao'][$indice] = $this->nomePropriedade($dado);
			$res['en_descricao'][$indice] = $dado['descricao'];
			$res['ng_nome'][$indice] = $this->atributoNegocio($dado);
			$res['ng_tamanho'][$indice] = $dado['tamanho'];
			$res['ng_tipo'][$indice] = $dado['tipo_de_dado'];
			if($dado['campo_pk']) $res['ng_chave_pk'] = $indice;
			$res['ng_dominio'][$indice] = null;
			$res['ng_associativa'][$indice] = $this->classeNegocio($dado['tabela_fk']);
			$res['ng_metodo'][$indice] = null;
			if($dado['obrigatorio']	== 'not null') $res['ng_nn'][$indice] = 'true';
			if(false) $res['ng_uk'][$indice] = 'true';
			if($dado['tabela_fk']	) $res['ng_fk'][$indice] = $dado['tabela_fk'];
			$res['nomeTabela'] = $nome;
			$res['nomeSequence'] = $sequence;
			$res['bd_campo'][$indice] = $dado['campo'];
			$res['bd_ordem'][$indice] = $indice == 1 ? $indice : null;
			$res['bd_referencia_tabela'][$indice] = $dado['tabela_fk'];
			$res['bd_referencia_campo'][$indice] = $dado['campo_fk'];
			$res['vi_componente'][$indice] = $dado['tabela_fk'] ? 'caixa de combinacao' : ($dado['campo_pk'] ? 'oculto' : 'caixa de entrada');
			$res['vi_ordem'][$indice] = $indice;
			$res['vi_ordemDescritivo'][$indice] = null;
			$res['vi_largura'][$indice] = null;
		}
		$pasta = $res['entidade'];
		$pasta{0} = strtolower($pasta{0});
		$res['arquivo'] = array(
			"{$pasta}/classes/C{$res['entidade']}_excluir.php"		=>	  "{$pasta}/classes/C{$res['entidade']}_excluir.php"
			,"{$pasta}/classes/C{$res['entidade']}_gravar.php"		=>	  "{$pasta}/classes/C{$res['entidade']}_gravar.php"
			,"{$pasta}/classes/C{$res['entidade']}_verEdicao.php"	=>	  "{$pasta}/classes/C{$res['entidade']}_verEdicao.php"
			,"{$pasta}/classes/C{$res['entidade']}_verPesquisa.php"	=>	  "{$pasta}/classes/C{$res['entidade']}_verPesquisa.php"
			,"{$pasta}/classes/I{$res['entidade']}.php"				=>	  "{$pasta}/classes/I{$res['entidade']}.php"
			,"{$pasta}/classes/N{$res['entidade']}.php"				=>	  "{$pasta}/classes/N{$res['entidade']}.php"
			,"{$pasta}/classes/P{$res['entidade']}.mysql.php"		=>	  "{$pasta}/classes/P{$res['entidade']}.mysql.php"
			,"{$pasta}/classes/P{$res['entidade']}.postgres.php"	=>	  "{$pasta}/classes/P{$res['entidade']}.postgres.php"
			,"{$pasta}/classes/P{$res['entidade']}.oracle.php"		=>	  "{$pasta}/classes/P{$res['entidade']}.oracle.php"
			,"{$pasta}/html/C{$res['entidade']}_verEdicao.html"		=>	  "{$pasta}/html/C{$res['entidade']}_verEdicao.html"
			,"{$pasta}/html/C{$res['entidade']}_verPesquisa.html"	=>	  "{$pasta}/html/C{$res['entidade']}_verPesquisa.html"
			,"{$pasta}/xml/entidade.xml"							=>	  "{$pasta}/xml/entidade.xml"
			,"{$pasta}/xml/pt_BR.xml"								=>	  "{$pasta}/xml/pt_BR.xml"
		);
		return $res;
	}
	protected function nomeDaSequence($squema,$tabela){
		return "{$squema}.sq_{$tabela}";
	}
	protected function nomePropriedade($dado){
		$prop = explode('_',$dado['campo']);
		$res = null;
		foreach($prop as $str){
			$res.=' '.ucfirst($str);
		}
		return $res;
	}
	protected function classeNegocio($tabela){
		if(!$tabela) return null;
		$res = null;
		$arNomes = explode('_',$tabela);
		foreach($arNomes as $str){
			$res .=ucfirst($str);
		}
		return "N{$res}";
	}
	protected function atributoNegocio($dado){
		$arNomes = explode('_',$dado['campo']);
		$atributo = null;
		foreach($arNomes as $str){
			$atributo .=ucfirst($str);
		}
		$atributo{0} = strtolower($atributo{0});

		return $atributo;
	}
}
?>
