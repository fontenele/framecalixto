<?php
/**
* Classe de representação de uma camada de negócio da entidade Controle Menu
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Controle Menu
*/
class NControleMenu extends negocio{
	/**
	* Menu principal do sistema
	* @var array
	*/
	protected $menuPrincipal = array();
	/**
	* Menu do sistema
	* @var array
	*/
	protected $menuSistema = array();
	/**
	* Array de acessos liberados para o usuário
	* @var array
	*/
	protected $acessosLiberados = array();
	/**
	* Flag de liberação dos menus
	* @var boolean
	*/
	protected $menuLiberado = false;
	/**
	* Método construtor que lê os acessos liberados para o usuário logado
	*/
	public function __construct(){
		try{
			$definicoes = definicao::pegarDefinicao();
			$controleDeAcesso = $definicoes->xpath('//controleDeAcesso');
			$this->menuLiberado = (isset($controleDeAcesso[0]) && strval($controleDeAcesso[0]['liberado']) == 'sim') ? true : false;
			if(!$this->menuLiberado){
				$nUsuario = sessaoSistema::pegar('usuario');
				$nAcesso = new NAcesso();
				$this->acessosLiberados = array_flip($nAcesso->lerAcessosPorUsuario($nUsuario)->gerarVetorDeAtributo('nmAcesso'));
			}
			return true;
		}catch (erro $e){
			return false;
		}
	}
	/**
	* Método criado para fazer a verificação do menuPrincipal do sistema quanto ao controle de acesso
	* @param string $propriedadeMenu propriedade que ficara adicionado o item
	* @param string $caminhoItem caminho do item separado por / (barra)
	* @param string $valorItem item do menu que será acessado
	* @param string $destravar destrava a validação do controle de acesso
	*/
	protected function adicionarItem($propriedadeMenu,$caminhoItem,$valorItem, $imagem = null ,$destravar = false, $prefixo = '?c='){
		if($destravar || $this->menuLiberado || isset($this->acessosLiberados[$valorItem])){
			$arCaminho = explode('/',$caminhoItem);
			$item = $arCaminho[count($arCaminho)-1];
			$imagem = $imagem ? ",'{$imagem}'":null;
			eval("\$this->{$propriedadeMenu}->{'".str_replace('/',"'}->{'",$caminhoItem)."'} = new VMenu('{$item}','{$prefixo}{$valorItem}'{$imagem});");
		}
	}
	/**
	* Método criado para efetuar a montagem do menu do site
	*/
	public function menuPrincipal(){
		try{
			$this->menuPrincipal = new colecaoPadraoMenu();
			$this->adicionarItem('menuPrincipal','Sistema/Principal','CControleAcesso_verPrincipal',true);
			$this->adicionarItem('menuPrincipal','Sistema/Login','CControleAcesso_verLogin',true);
			$this->adicionarItem('menuPrincipal','Cadastros/Perfil','CPerfil_verPesquisa');
			$this->adicionarItem('menuPrincipal','Cadastros/Estado','CEstado_verPesquisa');
			$this->adicionarItem('menuPrincipal','Cadastros/Pessoa','CPessoa_verPesquisa');
			$this->adicionarItem('menuPrincipal','Cadastros/Usuário','CUsuario_verPesquisa');
			$this->adicionarItem('menuPrincipal','Apoio/Gerador','CUtilitario_listarEntidade');
			$this->adicionarItem('menuPrincipal','Apoio/Tabelas','CUtilitario_listarTabelas');
			$this->adicionarItem('menuPrincipal','Apoio/Recriador de Base','CUtilitario_atualizadorBase');
			$this->adicionarItem('menuPrincipal','Apoio/Importador','CUtilitario_verImportador');
			$this->adicionarItem('menuPrincipal','Apoio/Definições do Sistema','CUtilitario_geradorDefinirSistema');
			return $this->menuPrincipal;
		}
		catch(erro $e){
			return array();
		}
	}
	/**
	* Método criado para efetuar a montagem do menu do sistema
	*/
	public function menuMenuSistema(){
		try {
			return $this->menuSistema;
		}catch (erro $e){
			return array();
		}
	}
}
?>