<?php
include_once('externas/fpdf153/fpdf.php');
/**
* Classe responsável por passar a inteligência do controle para um pdf
* @package Sistema
* @subpackage visualização
*/
class pdf extends fpdf{
	/**
	* Método de montagem do cabeçalho do pdf
	*/
	public function Header(){ $this->cabecalho(); }
	/**
	* Método de montagem do cabeçalho do pdf
	*/
	public function cabecalho(){}
	/**
	* Método de montagem do rodapé do pdf
	*/
	public function Footer(){ $this->rodape(); }
	/**
	* Método de montagem do rodapé do pdf
	*/
	public function rodape(){}
	/**
	*
	*/
	function SetTitle($title){
		parent::SetTitle(caracteres($title));
	}
	function SetSubject($subject){
		parent::SetSubject(caracteres($subject));
	}
	function SetAuthor($author){
		parent::SetAuthor(caracteres($author));
	}
	function SetKeywords($keywords){
		parent::SetKeywords(caracteres($keywords));
	}
	function SetCreator($creator){
		parent::SetCreator(caracteres($creator));
	}
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
		parent::Cell($w,$h,caracteres($txt),$border,$ln,$align,$fill,$link);
	}
	function Text($x,$y,$txt){
		parent::Text($x,$y,caracteres($txt));
	}
	function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0){
		parent::MultiCell($w,$h,caracteres($txt),$border,$align,$fill);
	}
	function Write($h,$txt,$link=''){
		parent::Write($h,caracteres($txt),caracteres($link));
	}

}
?>