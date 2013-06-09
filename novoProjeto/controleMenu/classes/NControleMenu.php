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
	* Método criado para adicionar um item a um menu
	* @param string $propriedadeMenu propriedade que ficara adicionado o item
	* @param string $caminhoItem caminho do item separado por / (barra)
	* @param string $valorItem item do menu que será acessado
	* @param string $destravar destrava a validação do controle de acesso
	*/
	public function adicionarItemDinamico($coPadraoMenu,$caminhoItem,$valorItem, $imagem = null ,$destravar = false, $prefixo = '?c='){
		if($destravar || $this->menuLiberado || isset($this->acessosLiberados[$valorItem])){
			$arCaminho = explode('/',$caminhoItem);
			$item = $arCaminho[count($arCaminho)-1];
			$imagem = $imagem ? ",'{$imagem}'":null;
			eval("\$coPadraoMenu->{'".str_replace('/',"'}->{'",$caminhoItem)."'} = new VMenu('{$item}','{$prefixo}{$valorItem}'{$imagem});");
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
			$this->menuPrincipal->passar_id('menuPrincipal');
			$this->menuPrincipal->passar_classe('fc-menu-sistema dropdown-menu');
			
			$nmLoginLabel = sessaoSistema::tem('usuario') ? 'Sair' : 'Entrar';
			$nmLoginImagem = sessaoSistema::tem('usuario') ? 'icon-off' : 'icon-flag';
			
			$this->adicionarItem('menuPrincipal','l0','');
			$this->adicionarItem('menuPrincipal','Sistema','');
			$this->adicionarItem('menuPrincipal','Página Principal','CControleAcesso_verPrincipal','icon-home',true);
			$this->adicionarItem('menuPrincipal',"{$nmLoginLabel}",'CControleAcesso_verLogin',"{$nmLoginImagem}",true);
			
			$this->adicionarItem('menuPrincipal','l1','');
			$this->adicionarItem('menuPrincipal','Administração','');
			$this->adicionarItem('menuPrincipal','Estados','CEstado_verPesquisa','icon-globe');
			$this->adicionarItem('menuPrincipal','Pessoas','CPessoa_verPesquisa','icon-comment');
			$this->adicionarItem('menuPrincipal','Perfis','CPerfil_verPesquisa','icon-tags');
			$this->adicionarItem('menuPrincipal','Usuários','CUsuario_verPesquisa','icon-user');
			$this->adicionarItem('menuPrincipal','Log de Acessos','CLogAcesso_verPesquisa','icon-edit');
			
			$this->adicionarItem('menuPrincipal','l2','','');
			$this->adicionarItem('menuPrincipal','Apoio','','');
			$this->adicionarItem('menuPrincipal','Pesquisar','CUtilitario_verPesquisaGeral','icon-search');
			$this->adicionarItem('menuPrincipal','Gerador','CUtilitario_verListarEntidade','icon-cog');
			$this->adicionarItem('menuPrincipal','Lista para gerador','CUtilitario_verListarTabelas','icon-list');
			$this->adicionarItem('menuPrincipal','Dicionário de dados','CUtilitario_verDicionarioDeDados','icon-list-alt');
			$this->adicionarItem('menuPrincipal','Recriador de Banco','CUtilitario_verRecriarBase','icon-fire');
			$this->adicionarItem('menuPrincipal','Importador','CUtilitario_verImportador','icon-download-alt');
			$this->adicionarItem('menuPrincipal','Definições do Sistema','CUtilitario_verDefinirSistema','icon-wrench');
			
			$this->menuPrincipal->l0->passar_classe('divider');
			$this->menuPrincipal->l1->passar_classe('divider');
			$this->menuPrincipal->l2->passar_classe('divider');
			$this->menuPrincipal->Sistema->passar_classe('disabled');
			$this->menuPrincipal->{'Administração'}->passar_classe('disabled');
			$this->menuPrincipal->Apoio->passar_classe('disabled');
			return $this->menuPrincipal;
		}
		catch(erro $e){
			return array();
		}
	}
    public function menuPrincipalDinamico(){
        return $this->montarMenuDinamico('menuPrincipal'); 
    }
    public function montarMenuDinamico($nmMenu){
       $coPadraoMenu = new colecaoPadraoMenu();
       $coPadraoMenu->passar_id($nmMenu);
       $nMenu = new NMenu();
       $nMenu->passarNmMenu($nmMenu);
       $nMenu = $nMenu->pesquisar()->pegar(0);
       $idMenu = $nMenu->valorChave();
       
       $nMenuItem = new NMenuItem();
       $nMenuItem->passarIdMenu($idMenu);
       $nMenuItem->passarIdPai(operador::eNulo(operador::restricaoE));
       $coMenuItens = $nMenuItem->pesquisar();

       if($coMenuItens->possuiItens()){
            while($nMenuItem = $coMenuItens->avancar()){
                $this->adicionarItemDinamico($coPadraoMenu,$nMenuItem->pegarNmMenuItem(),$nMenuItem->pegarTxUrl(),$nMenuItem->pegarTxImagem());
                $nMenuItensFilhos = new NMenuItem();
                $nMenuItensFilhos->passarIdMenu($idMenu);
                $nMenuItensFilhos->passarIdPai($nMenuItem->valorChave());
                
                $coMenuItensFilhos = $nMenuItensFilhos->pesquisar();
                if($coMenuItensFilhos->possuiItens()){
                    while($nMenuItemFilho = $coMenuItensFilhos->avancar()){
                        $this->adicionarItemDinamico($coPadraoMenu,$nMenuItem->pegarNmMenuItem().'/'.$nMenuItemFilho->pegarNmMenuItem(),$nMenuItemFilho->pegarTxUrl(),$nMenuItemFilho->pegarTxImagem());
                    }
                }
            }
       }
       return $coPadraoMenu;
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
