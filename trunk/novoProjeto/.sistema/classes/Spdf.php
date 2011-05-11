<?php
/**
* Classe responsável por passar a inteligência do controle para um pdf
* @package Sistema
* @subpackage visualização
*/
class Spdf extends pdf{
	public function configurar(){
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);
		$this->SetSubject('FrameCalixto');
		$this->SetKeywords('framecalixto, framework, php, php5, sistemas');
	}
	/**
	* Método de montagem do cabeçalho do pdf
	*/
	function cabecalho(){
		$this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->Image(realpath('.sistema/imagens/framecalixto.png'),9,1,19);
		$this->SetFont('Times','B',12);
		$this->Ln(2);
		$this->Cell(0,10,'Modifique o título do cabeçalho em Spdf::cabecalho();',1,0,'C');
		$this->Ln(20);
		$this->SetFont('Times','B',8);
	}
	/**
	* Método de montagem do rodapé do pdf
	*/
	function rodape(){
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$this->SetY(-15);
		$this->SetFont('Times','I',8);
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
?>