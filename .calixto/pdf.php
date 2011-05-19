<?php
define('K_TCPDF_EXTERNAL_CONFIG', TRUE);
$k_path_main = '../.calixto/externas/tcpdf/';
define('K_PATH_MAIN', $k_path_main);
$k_path_url = $k_path_main; // default value for console mode
if (isset($_SERVER['HTTP_HOST']) AND (!empty($_SERVER['HTTP_HOST']))) {
	if (isset($_SERVER['HTTPS']) AND (!empty($_SERVER['HTTPS'])) AND strtolower($_SERVER['HTTPS']) != 'off') {
		$k_path_url = 'https://';
	} else {
		$k_path_url = 'http://';
	}
	$k_path_url .= $_SERVER['HTTP_HOST'];
	$k_path_url .= str_replace('\\', '/', substr(K_PATH_MAIN, (strlen($_SERVER['DOCUMENT_ROOT']) - 1)));
}
define('K_PATH_URL', $k_path_url);
define('K_PATH_FONTS', K_PATH_MAIN . 'fonts/');
define('K_PATH_CACHE', realpath('.tmp/').'/');
define('K_PATH_URL_CACHE', K_PATH_URL . '.tmp/');
define('K_PATH_IMAGES', K_PATH_MAIN . 'images/');
define('K_BLANK_IMAGE', K_PATH_IMAGES . '_blank.png');
define('PDF_PAGE_FORMAT', 'A4');
define('PDF_PAGE_ORIENTATION', 'P');
define('PDF_CREATOR', 'TCPDF');
define('PDF_AUTHOR', 'TCPDF');
define('PDF_HEADER_TITLE', 'TCPDF Example');
define('PDF_HEADER_STRING', "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org");
define('PDF_HEADER_LOGO', 'tcpdf_logo.jpg');
define('PDF_HEADER_LOGO_WIDTH', 30);
define('PDF_UNIT', 'mm');
define('PDF_MARGIN_HEADER', 5);
define('PDF_MARGIN_FOOTER', 10);
define('PDF_MARGIN_TOP', 27);
define('PDF_MARGIN_BOTTOM', 25);
define('PDF_MARGIN_LEFT', 15);
define('PDF_MARGIN_RIGHT', 15);
define('PDF_FONT_NAME_MAIN', 'helvetica');
define('PDF_FONT_SIZE_MAIN', 10);
define('PDF_FONT_NAME_DATA', 'helvetica');
define('PDF_FONT_SIZE_DATA', 8);
define('PDF_FONT_MONOSPACED', 'courier');
define('PDF_IMAGE_SCALE_RATIO', 1.25);
define('HEAD_MAGNIFICATION', 1.1);
define('K_CELL_HEIGHT_RATIO', 1.25);
define('K_TITLE_MAGNIFICATION', 1.3);
define('K_SMALL_RATIO', 2 / 3);
define('K_THAI_TOPCHARS', true);
define('K_TCPDF_CALLS_IN_HTML', true);

include_once('externas/tcpdf/config/lang/bra.php');
include_once('externas/tcpdf/tcpdf.php');

/**
 * Classe responsável por passar a inteligência do controle para um pdf
 * @package FrameCalixto
 * @subpackage visualização
 */
class pdf extends TCPDF {

	public function __construct() {
		global $l;
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		if (sessaoSistema::tem('usuario')) {
			$nPessoa = new NPessoa();
			$nPessoa->ler(sessaoSistema::pegar('usuario')->pegarIdPessoa());
			$nmAutor = $nPessoa->pegarNmPessoa();
		} else {
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
	public function Header() {
		$this->cabecalho();
	}

	/**
	 * Método de montagem do cabeçalho do pdf
	 */
	public function cabecalho() {
		
	}

	/**
	 * Método de montagem do rodapé do pdf
	 */
	public function Footer() {
		$this->rodape();
	}

	/**
	 * Método de montagem do rodapé do pdf
	 */
	public function rodape() {
		
	}

	/**
	 *
	 */
	function SetTitle($title) {
		parent::SetTitle(caracteres($title));
	}

	function SetSubject($subject) {
		parent::SetSubject(caracteres($subject));
	}

	function SetAuthor($author) {
		parent::SetAuthor(caracteres($author));
	}

	function SetKeywords($keywords) {
		parent::SetKeywords(caracteres($keywords));
	}

	function SetCreator($creator) {
		parent::SetCreator(caracteres($creator));
	}

	/* public function Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false) {
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
	  } */
}

?>