<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para gravar um objeto de negocio
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoGravar extends controle{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verEdicao'));
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio();
		$this->preencherNegocioParaGravar($negocio);
		$this->sessao->registrar('negocio',$negocio);
		$negocio->gravar();
		if($this->sessao->tem('negocio')){
			$negocioSessao = $this->sessao->pegar('negocio');
			if(!$negocioSessao->valorChave()) $this->sessao->retirar('negocio');
		}
		$this->registrarComunicacao($this->internacionalizacao->pegarMensagem('gravarSucesso'));
	}
	/**
	* Método de preenchimento do objeto de negócio para a gravação
	* @param [negocio] objeto para preenchimento
	*/
	public function preencherNegocioParaGravar(negocio $negocio){
		try{
			$atributos = array_keys(get_class_vars(get_class($negocio)));
			foreach($_POST as $campo => $valor){
				if(in_array($campo,$atributos)){
					$metodo = 'passar'.ucfirst($campo);
					$negocio->$metodo($valor);
				}
			}
		}
		catch(erro $e){
			throw($e);
		}
	}
}
?>
