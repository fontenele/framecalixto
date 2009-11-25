<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Acesso do Perfil
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_verSelecionarAcessos extends controlePadraoVerEdicaoUmPraMuitos{
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	* @param string tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		if($negocio->valorChave()) {$negocio->carregarAcessos();}
		$controlesPerfil = array_flip($negocio->coAcessos->gerarVetorDeAtributo('nmAcesso'));
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
		$entidadeControle = '';
		$listagem = '';
		foreach($controlesSistema as $controle){
			if($controle){
				$controle = substr(basename($controle),0,-4);
				$arControle = explode('_',$controle);
				if($arControle[0] != $entidadeControle ){
					$entidadeControle = $arControle[0];
					$entidade = definicaoEntidade::entidade($controle).'<br/>';
					$listagem.= "\t\t\t<tr><td colspan='3' >
					<img alt='marcar' src='perfil/imagens/marcar.png' onclick='javascript:marcar(true,\"{$arControle[0]}\");' />
					<img alt='desmarcar' src='perfil/imagens/desmarcar.png' onclick='javascript:marcar(false,\"{$arControle[0]}\");' />
					&nbsp;<b>$entidade</b></td></tr>\n";
				}
				$vCheckBox = VComponente::montar('checkbox','controle[]',$controle);
				$vCheckBox->passarChecked(isset($controlesPerfil[$controle]));
				$vCheckBox = $vCheckBox->__toString();
				$listagem .= "\t\t\t<tr><td></td><td>{$vCheckBox}</td><td>{$arControle[1]}</td></tr>\n";
			}
		}
		if($negocio->pegarIdPerfil()){
			$nPerfil = new NPerfil();
			$nPerfil->ler($negocio->pegarIdPerfil());
			$this->visualizacao->perfil = $nPerfil->pegarNmPerfil();
		}
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'selecionarAcessos'));
		$this->visualizacao->idPerfil = VComponente::montar('oculto','idPerfil',$negocio->pegarIdPerfil());
		$this->visualizacao->nmPerfil = $negocio->pegarNmPerfil();
		$this->visualizacao->listagem = $listagem;
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->removerItem($this->inter->pegarTexto('botaoExcluir'));
		return $menu;
	}

}
?>