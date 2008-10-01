<?php
include_once('externas/phpmailer/class.phpmailer');
class email{

	private $nomeRemetente;
	private $emailRemetente;

	private $assunto;
	private $conteudo;
	private $mail;

	public function __construct($nomeRemetente, $emailRemetente){

		$this->mail = new PHPMailer();
		$this->mail->Priority = 1;
		$this->mail->IsMail(); // mandar via SMTP

		$this->mail->From = $emailRemetente;
		$this->mail->FromName = $nomeRemetente;

		$this->mail->AddReplyTo($emailRemetente,$nomeRemetente);

		$this->mail->WordWrap = 80; // set word wrap

		$this->mail->IsHTML(true); // send as HTML

	}

	public function passarNomeRemetente($nome){
		$this->mail->FromName = $nome;
	}

	public function passarEmailRemetente($email){
		$this->mail->From = $email;
	}

	public function addEmailDestinatario($nome = null , $email){
		$this->mail->AddAddress( $email , $nome );
	}

	public function passarAssunto($assunto){
		$this->mail->Subject = $assunto;
	}

	public function passarConteudo($conteudo){
		$this->mail->Body		= $conteudo;
		$this->mail->AltBody	= $conteudo;
	}

	public function validarEmail(){
		if (!ereg("^([0-9,a-z,A-Z]+)([.,_]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$", $email)){
			throw new Exception('Email inválido!');
		}
	}

	private function cabecalhoEmail(){

		$boundary = "XYZ-" . date("dmYis") . "-ZYX";
	
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";

		$headers .= "From: ".$this->nomeRemetente." <".$this->emailRemetente.">\n";
		$headers .= "Reply-To: ".$this->nomeRemetente." <".$this->emailRemetente.">\n";

		return $headers;
	}

	public function enviar(){

		return $this->mail->Send();

	}

}

?>