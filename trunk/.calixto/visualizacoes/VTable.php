<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VTable extends VEtiquetaHtml{
	protected $dados = array();
	protected $primeiraLinhaTitulo = false;
	/**
	 * Método construtor
	 */
    public function __construct(){
		parent::__construct('table');
	}
	/**
	* Método de configuração antes da impressão da etiqueta
	*/
	public function configurar(){
		switch(true){
			case(is_array($this->dados)):
				if($this->primeiraLinhaTitulo){
					$titulos = array_shift($this->dados);
					$this->conteudo .= "\t<thead>\n\t\t<tr>\n";
					foreach($titulos as $titulo){
						$this->conteudo.="\t\t\t<th>{$titulo}</th>\n";
					}
					$this->conteudo .= "\t\t</tr>\n\t</thead>\n";
				}
				$this->conteudo.="\t<tbody>\n";
				foreach($this->dados as $linha){
					$this->conteudo.="\t\t<tr>\n";
					foreach($linha as $campo){
						$this->conteudo.="\t\t\t<th>{$campo}</th>\n";
					}
					$this->conteudo.="\t\t</tr>\n";
				}
				$this->conteudo.="\t</tbody>\n";
			break;
		}
	}
}
?>
