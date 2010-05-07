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
			$this->adicionarItem('menuPrincipal','Sistema/Principal','CControleAcesso_verPrincipal','.sistema/temas/frameCalixto/imagens/imac.png',true);
			$this->adicionarItem('menuPrincipal','Sistema/Login','CControleAcesso_verLogin','.sistema/temas/frameCalixto/imagens/padlocke.png',true);
			
			$this->adicionarItem('menuPrincipal','Cadastros/Estado','CEstado_verEdicao','.sistema/temas/frameCalixto/imagens/web-file.png');
			$this->adicionarItem('menuPrincipal','Cadastros/Pessoa','CPessoa_verEdicao','.sistema/temas/frameCalixto/imagens/personal-folder2.png');
			$this->adicionarItem('menuPrincipal','Cadastros/Perfil','CPerfil_verEdicao','.sistema/temas/frameCalixto/imagens/Generic2.png');
			$this->adicionarItem('menuPrincipal','Cadastros/Usuário','CUsuario_verEdicao','.sistema/temas/frameCalixto/imagens/user.png');
			
			$this->adicionarItem('menuPrincipal','Apoio/Pesquisar','CUtilitario_pesquisaGeral','.sistema/temas/frameCalixto/imagens/view.png');
			$this->adicionarItem('menuPrincipal','Apoio/Gerador','CUtilitario_listarEntidade','.sistema/temas/frameCalixto/imagens/swipe-machine.png');
			$this->adicionarItem('menuPrincipal','Apoio/Tabelas','CUtilitario_listarTabelas','.sistema/temas/frameCalixto/imagens/window-side-by-side.png');
			$this->adicionarItem('menuPrincipal','Apoio/Recriador de Base','CUtilitario_atualizadorBase','.sistema/temas/frameCalixto/imagens/gears.png');
			$this->adicionarItem('menuPrincipal','Apoio/Importador','CUtilitario_verImportador','.sistema/temas/frameCalixto/imagens/database2.png');
			$this->adicionarItem('menuPrincipal','Apoio/Definições do Sistema','CUtilitario_geradorDefinirSistema','.sistema/temas/frameCalixto/imagens/conf.png');

			$this->menuPrincipal->passar_id('menuPrincipal');
			$this->menuPrincipal->Apoio->passar_imagem('.sistema/temas/frameCalixto/imagens/suport.png');
			$this->menuPrincipal->Cadastros->passar_imagem('.sistema/temas/frameCalixto/imagens/shared.png');
			$this->menuPrincipal->Sistema->passar_imagem('.sistema/temas/frameCalixto/imagens/globo.png');
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