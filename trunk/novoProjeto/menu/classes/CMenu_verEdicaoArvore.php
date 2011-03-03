<?php
/**
* Classe de controle
* Executa a gravação de um objeto : Menu
* @package Sistema
* @subpackage Menu
*/
class CMenu_verEdicaoArvore extends controlePadraoVerEdicao{
	protected $colecaoNegocios;

	public function inicial(){
		$nMenu = new NMenu();
		$nMenu->lerTodosAninhado();
		$this->visualizacao->arvore = $this->mostrarArvore($nMenu->pegarColecaoFilhos());
		parent::inicial();
	}
	/**
	 * Método de visualização da coleção aninhada
	 * @param integer $nivel nível de repasse para a mostragem
	 */
	public function mostrarArvore($colecao,$tab=null){
		$str=$tab."<ul>\n";
		while($negocio = $colecao->avancar()){
			$str.=$tab."\t\t<li><span id=".$negocio->valorChave().">".$negocio->valorDescricao()."</span>\n";
			if($negocio->pegarColecaoFilhos()->possuiItens()){
				$str.=$this->mostrarArvore($negocio->pegarColecaoFilhos(),$tab."\t\t");
			}
			$str.=$tab."\t\t</li>\n";
		}
		$str.=$tab."</ul>\n";
		return $str;
	}
}
?>
