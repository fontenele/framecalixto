<?php
/**
* Classe que faz o gerenciamento dos controles do sistema
* Esta classe padroniza a forma de chamada dos controles do sistema
* @package Infra-estrutura
* @subpackage utilitários
*/
class gerenteControles extends objeto{
	/**
	* @var [controle] controle que está sendo utilizado pelo gerente
	*/
	public $controle;
	/**
	* @var [string] nome do próximo controle que será redirecionado
	*/
	public $proximoControle;
	/**
	* Método contrutor do gerente de controle
	* @param [string] nome da classe de controle a ser gerenciada
	* @return [booleano] retorno de sucesso da construção do gerenciador
	*/
	function __construct($controle){
		try{
			if(!$controle) $this->redirecionar();
			$cControle = new $controle($this,true);
			if( $cControle instanceof controle ){
				$this->passarControle($cControle);
				if(!empty($this->proximoControle))
					$this->redirecionar("?c={$this->proximoControle}");
			}else{
				throw new erroInclusao("Controle [{$controle}] inexistente!");
			}
			return true;
		}
		catch (erroNegocio $e){
			sessaoSistema::registrar('comunicacao', $e->getMessage());
			if(!empty($this->proximoControle))
				$this->redirecionar("?c={$this->proximoControle}");
			return false;
		}
		catch (erroLogin $e){
			sessaoSistema::registrar('comunicacao', $e->getMessage());
			if(!empty($this->proximoControle))
				$this->redirecionar("?c={$this->proximoControle}");
			$this->redirecionar('?c='.definicaoSistema::pegarControleInicial());
			return false;
		}
		catch (erroAcesso $e){
			sessaoSistema::registrar('comunicacao', $e->getMessage());
			if(!empty($this->proximoControle))
				$this->redirecionar("?c={$this->proximoControle}");
			$this->redirecionar(sprintf('?c=%s',definicaoSistema::pegarControleErro()));
			return false;
		}
		catch (erroSessao $e){
			sessaoSistema::registrar('comunicacao', $e->getMessage());
			if(!empty($this->proximoControle))
				$this->redirecionar("?c={$this->proximoControle}");
			$this->redirecionar(sprintf('?c=%s',definicaoSistema::pegarControleErro()));
			return false;
		}
		catch (erro $e){
			echo $e->__toHtml();
			return false;
		}
		catch (Exception $e){
			echo $e;
			return false;
		}
	}
	/**
	* Método que executa o redirecionamento para um outro controle
	* @param [string] link para a chamada do próximo controle
	*/
	function redirecionar($url){
		try{
			if(empty($url)) throw new erroLogin('Não foi passado o redirecionamento!');
			header("LOCATION:$url");
		}
		catch (erroLogin $e){throw $e;}
	}
}
?>