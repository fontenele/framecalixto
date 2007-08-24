<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Acesso do usuario
* @package Sistema
* @subpackage usuario
*/
class CUsuario_verSelecionarAcessos extends CUsuario_verEdicao{
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacaoEdicao(negocio $negocio){
		$negocio->carregarAcessos();
		$controlesUsuario = array_flip($negocio->coAcessos->gerarVetorDeAtributo('controle'));
		$sistema = dir(".");
		while (false !== ($diretorio = $sistema->read())) {
			if (preg_match('/^[^\.].*/', $diretorio, $res) && is_dir($diretorio = "{$diretorio}/classes")){
				$classes = dir($diretorio);
				while (false !== ($classe = $classes->read())) {
					if (preg_match('/^[C].*/', $classe, $res) && is_file($classe = "{$diretorio}/{$classe}")){
					    $controlesSistema[] = $classe;
					}
			    }
		    }
		}
		$sistema->close();
		//$controlesSistema = explode("\n",shell_exec('find ./*/classes/C*.php'));
		$entidadeControle = '';
		$listagem = '';
		foreach($controlesSistema as $controle){
			if($controle){
				$controle = substr(basename($controle),0,-4);
				$arControle = explode('_',$controle);
				if($arControle[0] != $entidadeControle ){
					$entidadeControle = $arControle[0];
					$entidade = definicaoEntidade::entidade($controle).'<br/>';
					$listagem.= "\t\t\t<tr><td colspan='3' ><input type='checkbox' onclick='javascript:marcar(this.checked,\"{$arControle[0]}\");' />&nbsp;$entidade</td></tr>\n";
				}
				$vCheckBox = VComponente::montar('checkbox','controle[]',$controle);
				if(isset($controlesUsuario[$controle])) $vCheckBox->passarChecked();
				$vCheckBox = $vCheckBox->__toString();
				$listagem .= "\t\t\t<tr><td></td><td>{$vCheckBox}</td><td>{$arControle[1]}</td></tr>\n";
			}
		}
		if($negocio->pegarIdUsuario()){
			$nUsuario = new NUsuario();
			$nUsuario->ler($negocio->pegarIdUsuario());
			$this->visualizacao->usuario = $nUsuario->pegarNmUsuario();
		}
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'selecionarAcessos'));
		$this->visualizacao->idUsuario = VComponente::montar('oculto','idUsuario',$negocio->pegarIdUsuario());
		$this->visualizacao->nmUsuario = $negocio->pegarNmUsuario();
		$this->visualizacao->listagem = $listagem;
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		unset($menu[$this->inter->pegarTexto('botaoExcluir')]);
		return $menu;
	}

}
?>