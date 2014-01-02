<?php
/**
* Classe de controle
* Cria a visualização de um novo objeto : Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_verNovo extends controlePadraoVerEdicao{
    
	public function montarApresentacao(\negocio $negocio, $tipo = 'edicao') {
		CUsuario_verEdicao::montarApresentacaoAgrupadaDeEdicao($negocio, $this->visualizacao, 'usuario');
		$this->visualizacao->action = '?c=CUsuario_gravarNovo';
		$this->visualizacao->usuario_nmLogin->obrigatorio(true);
		$this->visualizacao->usuario_nmSenha->obrigatorio(true);
	}
	/**
	* Método de validação do controle de acesso
	* @return boolean resultado da validação
	*/
	public function validarAcessoAoControle(){ 
		return true;
	}
}
?>
