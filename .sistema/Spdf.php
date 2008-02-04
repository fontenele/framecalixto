<?php
/**
* Classe responsável por passar a inteligência do controle para um pdf
* @package Sistema
* @subpackage visualização
*/
class Spdf extends pdf{
	/**
	* Método de montagem do cabeçalho do pdf
	*/
	function cabecalho(){
		//$this->Image('logo_pb.png',10,8,33);
		$this->SetFont('Arial','B',15);
		$this->Cell(80);
		$this->Cell(30,10,'Title',1,0,'C');
		$this->Ln(20);
	}
	/**
	* Método de montagem do rodapé do pdf
	*/
	function rodape(){
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
?>