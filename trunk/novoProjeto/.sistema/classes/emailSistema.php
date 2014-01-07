<?php

/**
 * Classe utilitária para criação de email
 * @package FrameCalixto
 * @subpackage utilitários
 */
class emailSistema extends email {

	/**
	 * Método construtor
	 * @param string $nomeRemetente
	 * @param string $emailRemetente
	 */
	public function __construct($nomeRemetente = 'Gestão Contábil', $emailRemetente = 'contato@gestaocontabil.org') {
		parent::__construct($nomeRemetente, $emailRemetente);
	}

	/**
	 * Envia o email
	 * @return boolean
	 */
	public function enviar() {
		switch (definicaoSistema::pegarAmbiente()) {
			case definicaoSistema::homologacao :
				return $this->mail->Send();
				break;
			case definicaoSistema::producao :
				return $this->mail->Send();
				break;
			case definicaoSistema::desenvolvimento :
				exec('rm -f ' . definicaoPasta::temporaria() . '/email_*');
				$file = tempnam(definicaoPasta::temporaria(), 'email_');
				unlink($file);
				fputs($email = fopen($file . '.html', 'a+'), $this->__toString());
				fclose($email);
				return true;
			default:
				break;
		}
	}

}

?>
