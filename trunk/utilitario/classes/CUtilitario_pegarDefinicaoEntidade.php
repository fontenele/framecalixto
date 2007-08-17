<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_pegarDefinicaoEntidade extends controlePadrao{
	public $nomeEntidade;
	public $nomeNegocio;
	public $nomeTabela;
	public $entidade;
	
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->entidade = $_GET;
		$arNome = explode(' ',strtolower($this->entidade['entidade']));
		$nome = array_shift($arNome);
		$arNome = array_map("ucFirst", $arNome) ;
		array_unshift($arNome,$nome);
		$this->nomeEntidade = implode('',$arNome);
		$this->nomeNegocio = "N{$this->nomeEntidade}";
		$xml = array();
		if(arquivo::legivel("{$this->nomeEntidade}/xml/entidade.xml")){
			$xml['entidade'] = simplexml_load_file("{$this->nomeEntidade}/xml/entidade.xml");
		}
		if(arquivo::legivel("{$this->nomeEntidade}/xml/pt_BR.xml")){
			$xml['inter'] = simplexml_load_file("{$this->nomeEntidade}/xml/pt_BR.xml");
		}
		x2($this);
		$j = new json();
		$a = $j->pegarJson($xml);
		echo "
			<script language='javascript'>
			eval(res = $a);
			alert(res.@attributes.nomeBanco);
			</script>
		";
	}
}
?>