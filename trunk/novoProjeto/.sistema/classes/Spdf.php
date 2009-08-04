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
		$this->SetFont('Arial','B',12);
		$this->Cell(0,10,'Modifique o título do cabeçalho em Spdf::cabecalho();',1,0,'C');
		$this->Ln(20);
		$this->SetFont('Times','B',8);
	}
	/**
	* Método de montagem do rodapé do pdf
	*/
	function rodape(){
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
?>