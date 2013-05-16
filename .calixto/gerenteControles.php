<?php

/**
 * Classe que faz o gerenciamento dos controles do sistema
 * Esta classe padroniza a forma de chamada dos controles do sistema
 * @package FrameCalixto
 * @subpackage utilitários
 */
class gerenteControles extends objeto {

	/**
	 * @var controle controle que está sendo utilizado pelo gerente
	 */
	public $controle;

	/**
	 * @var string nome do próximo controle que será redirecionado
	 */
	public $proximoControle;

	/**
	 * Método contrutor do gerente de controle
	 * @param string nome da classe de controle a ser gerenciada
	 * @return boolean retorno de sucesso da construção do gerenciador
	 */
	function __construct($controle) {
		try {
			if (!$controle)
				$this->redirecionar();
			$cControle = new $controle($this, true);
			if ($cControle instanceof controle) {
				$this->passarControle($cControle);
				if (!empty($this->proximoControle) && !controle::requisicaoAjax())
					$this->redirecionar("?c={$this->proximoControle}");
			}else {
				throw new erroInclusao("Controle [{$controle}] inexistente!");
			}
			return true;
		} catch (erroNegocio $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				controle::comunicar($e->getMessage(), 'erro');
				if (!empty($this->proximoControle))
					$this->redirecionar("?c={$this->proximoControle}");
			}
			return false;
		} catch (erroLogin $e) {
			controle::comunicar($e->getMessage(), 'erro');
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				if (!empty($this->proximoControle))
					$this->redirecionar("?c={$this->proximoControle}");
				$this->redirecionar('?c=' . definicaoSistema::pegarControleInicial());
			}
			return false;
		} catch (erroAcesso $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				controle::comunicar($e->getMessage(), 'erro');
				if (!empty($this->proximoControle))
					$this->redirecionar("?c={$this->proximoControle}");
				$this->redirecionar(sprintf('?c=%s', 'CControleAcesso_verPrincipal'));
			}
			return false;
		} catch (erroSessao $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				controle::comunicar($e->getMessage(), 'erro');
				if (!empty($this->proximoControle))
					$this->redirecionar("?c={$this->proximoControle}");
				$this->redirecionar(sprintf('?c=%s', definicaoSistema::pegarControleInicial()));
			}
			return false;
		} catch (erroBanco $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e, 'Ocorreu um erro de banco de dados, provavelmente nossos analista já estão analisando o motivo.');
			} else {
				echo $e->__toHtml();
			}
			return false;
		} catch (erro $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				echo $e->__toHtml();
			}
			return false;
		} catch (Exception $e) {
			if (controle::tipoResposta()) {
				$this->exibirErro($e);
			} else {
				echo $e;
			}
			return false;
		}
	}

	/**
	 * Exibe o erro ocorrido no processo executado pelo controle
	 * @param Exception $e
	 * @param string $mensagemForcada
	 */
	public function exibirErro(Exception $e, $mensagemForcada = null) {
		$ar['tipo'] = get_class($e);
		$ar['erro'] = $e->getMessage();
		$ar['trace'] = 'produção';
		if (definicaoSistema::pegarAmbiente() !=  definicaoSistema::producao){
			$ar['trace'] = $e->getTraceAsString();
		}
		switch (controle::tipoResposta()) {
			case controle::ajax:
			case controle::json:
				$json = new json();
				controle::responderJson($json->pegarJson($ar));
				break;
			case controle::xml:
				$xml = "<erro>
                        <tipo>{$ar['tipo']}</tipo>
                        <erro>{$ar['erro']}</erro>
                        <erro>{$ar['trace']}</erro>
                    </erro>";
				controle::responderXml($xml);
				break;
			case controle::texto:
				echo $e;
				break;
		}
	}

	/**
	 * Método que executa o redirecionamento para um outro controle
	 * @param string link para a chamada do próximo controle
	 */
	function redirecionar($url) {
		try {
			if (empty($url))
				throw new erroLogin('Não foi passado o redirecionamento!');
			header("LOCATION:$url");
		} catch (erroLogin $e) {
			throw $e;
		}
	}

}

?>
