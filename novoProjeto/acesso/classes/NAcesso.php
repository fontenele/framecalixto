<?php
/**
* Classe de representação de uma camada de negócio da entidade [Acesso]
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Acesso
*/
class NAcesso extends negocioPadrao{
	/**
	* @var integer Identificador do acesso
	*/
	public $idAcesso;
	/**
	* @var integer Identificador do perfil
	*/
	public $idPerfil;
	/**
	* @var integer Identificador do usuário
	*/
	public $idUsuario;
	/**
	* @var string Nome do Acesso
	*/
	public $nmAcesso;
	/**
	* @var TData Data Inicio
	*/
	public $dtInicio;
	/**
	* @var TData Data Fim
	*/
	public $dtFim;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	public function nomeChave(){ return 'idAcesso'; }
	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		parent::verificarAntesInserir();
/*		if(!($this->idPerfil xor $this->idUsuario)){
			throw new erroNegocio('Um acesso deve ser definido para um perfil ou para um usuário.');
		}else{
			if ($this->idUsuario) {
				if(!($this->dtInicio && $this->dtFim)){
					throw new erroNegocio('Deve ser definido um intervalo de tempo para o acesso do usuário.');
				}
				if($this->dtInicio->pegarTempoMarcado() <= $this->dtFim->pegarTempoMarcado()){
					throw new erroNegocio('A data inicial não pode ser menor ou igual a data final.');
				}
			}
			if($this->idPerfil){
				$this->dtInicio = new TData(null);
				$this->dtFim = new TData(null);
			}
		}
*/	}
	/**
	 * Método de leitura de acessos pelo usuário
	 * @param NUsuario 
	 * @param string Nome do controle acessado
	 * @return colecaoPadraoNegocio
	 */
	public function lerAcessosPorUsuario(NUsuario $nUsuario, $controle = null){
		return $this->vetorPraColecao($this->pegarPersistente()->lerAcessosPorUsuario($nUsuario->idUsuario,$controle));
	}
}
?>