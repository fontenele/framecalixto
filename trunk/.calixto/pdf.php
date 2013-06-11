<?php
include_once('externas/fpdf153/fpdf.php');
/**
* Classe responsável por passar a inteligência do controle para um pdf
* @package FrameCalixto
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
	function SetTitle($title,$isUtf8 = true){
		parent::SetTitle($title,$isUtf8);
	}
	function SetSubject($subject,$isUtf8 = true){
		parent::SetSubject($subject,$isUtf8);
	}
	function SetAuthor($author = 'FrameCalixto', $isUtf8 = true){
		parent::SetAuthor($author,$isUtf8);
	}
	function SetKeywords($keywords,$isUtf8 = true){
		parent::SetKeywords($keywords,$isUtf8);
	}
	function SetCreator($creator,$isUtf8 = true){
		parent::SetCreator($creator,$isUtf8);
	}
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
		parent::Cell($w,$h,utf8_decode($txt),$border,$ln,$align,$fill,$link);
	}
	function Text($x,$y,$txt){
		parent::Text($x,$y,utf8_decode($txt));
	}
	function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0){
		parent::MultiCell($w,$h,utf8_decode($txt),$border,$align,$fill);
	}
	function Write($h,$txt,$link=''){
		parent::Write($h,utf8_decode($txt),utf8_decode($link));
	}
	/**
	 * Retorna o maior número de itens em um array
	 * @param array $ar
	 * @return integer
	 */
	function maior($ar) {
		$maior = 0;
		foreach (array_map('count', $ar) as $tamanho) {
			$maior = max($maior, $tamanho);
		}
		return $maior;
	}

	/**
	 * Constroi uma linha de grid
	 * @param array $dados os dados na ordem das colunas
	 * @param array $larguras as larguras na ordem das colunas
	 * @param integer $altura a altura de cada quebra de linha dentro da tupla
	 * @param array $alinhamentos os alinhamentos na ordem das colunas
	 * @param boolean $borda definição de apresentação das bordas
	 * @param boolean $fundo definição de preenchimento do fundo
	 */
	function Linha($dados, $larguras, $altura = 5, $alinhamentos = null, $borda = true, $fundo = false) {
		if (count($dados) != count($larguras)) {
			trigger_error('O número de colunas é diferente do numero de larguras');
		}
		$colunas = array();
		for ($i = 0; $i < count($dados); $i++) {
			$colunas[] = $this->QuebrarTexto($larguras[$i], $dados[$i]);
		}
		$nrLinha = 0;
		$maior = $this->maior($colunas);
		$b = $bs = $bi = '';
		while ($nrLinha <= $maior - 1) {
			foreach ($colunas as $nrColuna => $dado) {
				$texto = isset($colunas[$nrColuna][$nrLinha]) ? $colunas[$nrColuna][$nrLinha] : '';
				$alin = isset($alinhamentos[$nrColuna]) ? $alinhamentos[$nrColuna] : '';
				if($borda){
					$b = 'LR';
					$bs = ($nrLinha == 0) ? 'T' : '';
					$bi = ($nrLinha == $maior - 1) ? 'B' : '';
				}
				$this->Cell($larguras[$nrColuna], $altura, $texto, $b . $bs . $bi, $ln = 0, $alin, $fundo, $link = '');
			}
			$this->Ln($altura);
			$nrLinha++;
		}
	}

	/**
	 * Explode o texto em um array dependendo da largura passada
	 * @param type $w
	 * @param type $txt
	 * @return type
	 */
	function QuebrarTexto($w, $txt) {
		$lines = array();
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 && $s[$nb - 1] == "\n")
			$nb--;
		$sep = -1;
		$i = $j = $l = $ns = 0;
		$nl = 1;
		while ($i < $nb) {
			$c = $s[$i];
			if ($c == "\n") {
				$lines[] = substr($s, $j, $i - $j);
				$i++;
				$sep = -1;
				$j = $i;
				$l = $ns = 0;
				$nl++;
				continue;
			}
			if ($c == ' ') {
				$sep = $i;
				$ls = $l;
				$ns++;
			}
			$l += $cw[$c];
			if ($l > $wmax) {
				if ($sep == -1) {
					if ($i == $j)
						$i++;
					$lines[] = substr($s, $j, $i - $j);
				} else {
					$lines[] = substr($s, $j, $sep - $j);
					$i = $sep + 1;
				}
				$sep = -1;
				$j = $i;
				$l = $ns = 0;
				$nl++;
			} else {
				$i++;
			}
		}
		$lines[] = substr($s, $j, $i - $j);
		return $lines;
	}


}
?>