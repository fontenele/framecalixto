<?php
include_once('externas/phpmailer/class.phpmailer.php');
/**
 * Classe utilitária para criação de email
 * @package FrameCalixto
 * @subpackage utilitários
 */
class email{
	/**
	 * Nome do Remetente
	 * @var string remetente do email
	 */
	public $nomeRemetente;
	/**
	 * Email do remetente para uma eventual resposta
	 * @var string email do remetente
	 */
	public $emailRemetente;
	/**
	 * Assunto do email
	 * @var string
	 */
	public $assunto;
	/**
	 * Texto com o conteúdo do email
	 * @var string
	 */
	public $conteudo;
	/**
	 * Objeto responsável pela configuração e envio do email
	 * @var PHPMailer objeto configurador de email
	 */
	public $mail;
	/**
	 * Método construtor
	 * @param string $nomeRemetente
	 * @param string $emailRemetente
	 */
	public function __construct($nomeRemetente, $emailRemetente){

		$this->mail = new PHPMailer();
		$this->mail->Priority = 1;
		$this->mail->IsMail(); // mandar via SMTP
		$this->mail->CharSet = 'utf-8';

		$this->mail->From = $emailRemetente;
		$this->mail->FromName = $nomeRemetente;

		$this->mail->AddReplyTo($emailRemetente,$nomeRemetente);

		$this->mail->WordWrap = 80; // set word wrap

		$this->mail->IsHTML(true); // send as HTML

	}
	/**
	 * Configura o nome do remetente do email a ser enviado
	 * @param string $nome
	 */
	public function passarNomeRemetente($nome){
		$this->mail->FromName = $nome;
	}
	/**
	 * Configura o email do remetente do email a ser enviado
	 * @param string $email
	 */
	public function passarEmailRemetente($email){
		$this->mail->From = $email;
	}
	/**
	 * Adiciona um destinatário ao email a ser enviado
	 * @param string $nome
	 * @param string $email
	 */
	public function addEmailDestinatario($nome = null , $email = null){
		$this->mail->AddAddress( $email , $nome );
	}
	/**
	 * Configura o assunto do email a ser enviado
	 * @param string $assunto
	 */
	public function passarAssunto($assunto){
		$this->mail->Subject = $assunto;
	}
	/**
	 * Configura o conteúdo do email a ser enviado
	 * @param string $conteudo
	 */
	public function passarConteudo($conteudo){
		$this->mail->Body		= $conteudo;
		$this->mail->AltBody	= $conteudo;
	}
	/**
	 * Valida um email
	 * @param string $email
	 */
	public static function validar($email){
		if (!ereg("^([0-9,a-z,A-Z]+)([.,_]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$", $email)){
			return false;
		}
		return true;
	}
	/**
	 * Configura o cabeçalho do email
	 * @return string
	 */
	private function cabecalhoEmail(){

		$boundary = "XYZ-" . date("dmYis") . "-ZYX";
	
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";

		$headers .= "From: ".$this->nomeRemetente." <".$this->emailRemetente.">\n";
		$headers .= "Reply-To: ".$this->nomeRemetente." <".$this->emailRemetente.">\n";

		return $headers;
	}
	/**
	 * Envia o email
	 * @return boolean
	 */
	public function enviar(){
		return $this->mail->Send();

	}
	
	public function anexar( $caminho , $nome , $codificacao , $tipo ){
		return $this->mail->AddAttachment();
	}
	
	public function __toString(){
		$to = '';
		if (isset($this->mail->to[0])) foreach ($this->mail->to as $email){
			$to .= "\"$email[1]\" $email[0];";
		}
		$bcc = isset($this->mail->bcc[0]) ? implode(';',$this->mail->bcc[0]) : null;
		$str = "
		<fieldset class='debugEmail'><legend>email</legend>
		<fieldset><b>De:</b> {$this->mail->From}<br/>
		<b>Para:</b> {$to}<br/>
		<b>Assunto:</b>{$this->mail->Subject}<br/>
		</fieldset>
		<fieldset>
		{$this->mail->Body}
		</fieldset>
		</fieldset>";
		return $str;
	}

}

?>