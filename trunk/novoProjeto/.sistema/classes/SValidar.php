<?php
class SValidar extends controlePadrao{
	public function inicial(){
		try {
			$_GET['ajax']=1;
			if(!isset($_GET['negocio'])) throw new erroNegocio('Não foi definido a entidade de negócio a ser validada!');
			$conexao = conexao::criar();
			$conexao->iniciarTransacao();
			$negocio = new $_GET['negocio']($conexao);
			if(isset($_GET['controle'])){
				eval("{$_GET['controle']}::montarNegocio(\$negocio);");
			}else{
				parent::montarNegocio($negocio);
			}
			$negocio = new NPessoa();
			if($negocio->valorChave()){
				$negocioAnterior = new $_GET['negocio']($conexao);
				$negocioAnterior->ler($negocio->valorChave());
				$negocio->verificarAntesAlterar($negocioAnterior);
			}else{
				$negocio->verificarAntesInserir();
			}
			$conexao->desfazerTransacao();
		} catch (Exception $e) {
			$conexao->desfazerTransacao();
			throw $e;
		}

	}
	/**
	* Método de criação da visualizacao
	*/
	public function criarVisualizacaoPadrao(){}
	/**
	* Método de criação da visualizacao
	*/
	public function criarInternacionalizacaoPadrao(){}
}
?>
