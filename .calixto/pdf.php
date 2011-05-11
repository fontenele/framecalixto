<?php
//include_once('externas/fpdf153/fpdf.php');

include_once('externas/tcpdf/config/lang/bra.php');
include_once('externas/tcpdf/tcpdf.php');
/**
* Classe responsável por passar a inteligência do controle para um pdf
* @package FrameCalixto
* @subpackage visualização
*/
class pdf extends TCPDF{
	public function __construct(){
		global $l;
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		if(sessaoSistema::tem('usuario')){
			$nPessoa = new NPessoa();
			$nPessoa->ler(sessaoSistema::pegar('usuario')->pegarIdPessoa());
			$nmAutor = $nPessoa->pegarNmPessoa();
		}else{
			$nmAutor = 'FrameCalixto - www.framecalixto.com';
		}
		$this->SetAuthor($nmAutor);
		$this->SetCreator(PDF_CREATOR);
		$this->setLanguageArray($l);
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->setFontSubsetting(true);
		$this->SetFont('dejavusans', '', 14, '', true);
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$this->AddPage();
		$this->configurar();
	}
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
	/*public function Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false) {
		parent::Image(realpath('.sistema/imagens/'.$file), $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage);
	}
	/*function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
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
	}*/

}
?>