<?php
/**
* Classe de controle
* Define o arquivo de configuração do sistema
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_definirSistema extends controle{

	protected $debug = false;
	protected $caminhoDefinicao = ".sistema/xml/definicoes.xml";

	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			if(!arquivo::gravavel($this->caminhoDefinicao)){
				$this->registrarComunicacao('Arquivo ');
			}
			$this->passarProximoControle($_POST['sistema']['paginaInicial']);
			$this->montarArquivoDefinicaoXML();
		}catch(erro $e){
			$this->passarProximoControle('CUtilitario_geradorDefinirSistema');
			throw $e;
		}
	}
	/**
	* Escreve o arquivo com o conteudo passado
	* @param string caminho do arquivo a ser escrito
	* @param string conteudo do arquivo a ser escrito
	*/
	protected function escreverArquivo($caminho,$conteudo){
		$caminho = caracteres::RetiraAcentos($caminho);
		if($this->debug){
			echo "<br /><br /><br />No arquivo: {$caminho}<br /><br />";
			highlight_string($conteudo);
			die ;
		}
		$handle = fopen ($caminho, "w");
		fwrite($handle, $conteudo);
		fclose($handle);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	function montarArquivoDefinicaoXML(){
		$d = $_POST;
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<definicoes>\n";
		$xml.= "\t<sistema paginaInicial='{$d['sistema']['paginaInicial']}' paginaErro='{$d['sistema']['paginaErro']}' ambiente='{$d['sistema']['ambiente']}' />\n";
		$xml.= "\t<controleDeAcesso liberado='{$d['controleDeAcesso']['liberado']}' classe='{$d['controleDeAcesso']['classe']}' metodoLiberacao='{$d['controleDeAcesso']['metodoLiberacao']}' />\n";
		$xml.= "\t<controleDeMenu classe='{$d['controleDeMenu']['classe']}' metodoMenuSite='{$d['controleDeMenu']['metodoMenuSite']}' metodoMenuSistema='{$d['controleDeMenu']['metodoMenuSistema']}' />\n";
		$xml.= "\t<bancos>\n";
		foreach($d['banco']['id'] as $id => $banco){
			$xml.= "\t\t<banco id='{$d['banco']['id'][$id]}' tipo='{$d['banco']['tipo'][$id]}' servidor='{$d['banco']['servidor'][$id]}' porta='{$d['banco']['porta'][$id]}' nome='{$d['banco']['nome'][$id]}' usuario='{$d['banco']['usuario'][$id]}' senha='{$d['banco']['senha'][$id]}' conexaoMultipla='{$d['banco']['conexaoMultipla'][$id]}' />\n";
		}
		$xml.= "\t</bancos>\n";
		$xml.= "\t<diretorios>\n";
		foreach($d['diretorios']['id'] as $id => $diretorio){
			$xml.= "\t\t<diretorio id='{$d['diretorios']['id'][$id]}' dir='{$d['diretorios']['dir'][$id]}' entidade='{$d['diretorios']['entidade'][$id]}' />\n";
		}
		$xml.= "\t</diretorios>\n";
		$xml.= "\t<arquivos>\n";
		foreach($d['arquivos']['tipo'] as $tipo => $arquivo){
			$xml.= "\t\t<arquivo tipo='{$d['arquivos']['tipo'][$tipo]}' nome='{$d['arquivos']['nome'][$tipo]}' />\n";
		}
		$xml.= "\t</arquivos>\n";
		$xml.=
"	<classes>
		<classe dir='../.calixto/' />
		<classe id='T' dir='../.calixto/tiposDeDados/' />
		<classe id='P' dir='classes/' entidade='sim' tipoBanco='sim' />
		<classe id='N' dir='classes/' entidade='sim' />
		<classe id='C' dir='classes/' entidade='sim' />
		<classe id='I' dir='classes/' entidade='sim' />
		<classe id='E' dir='classes/' entidade='sim' />
		<classe id='S' dir='.sistema/classes/' />
		<classe id='V' dir='../.calixto/visualizacoes/' />
		<classe id='erro' dir='../.calixto/tiposDeErros/' />
		<classe id='definicao' dir='../.calixto/definicoes/' />
		<classe id='conexaoPadrao' dir='../.calixto/padroes/conexoes/' />
		<classe id='persistentePadrao' dir='../.calixto/padroes/persistentes/' />
		<classe id='negocioPadrao' dir='../.calixto/padroes/negocios/' />
		<classe id='controlePadrao' dir='../.calixto/padroes/controles/' />
		<classe id='visualizacaoPadrao' dir='../.calixto/padroes/visualizacoes/' />
		<classe id='internacionalizacaoPadrao' dir='../.calixto/padroes/internacionalizacoes/' />
		<classe id='colecaoPadrao' dir='../.calixto/padroes/colecoes/' />
	</classes>
";
		$xml.= "</definicoes>";
		$this->escreverArquivo($this->caminhoDefinicao,$xml);
	}
}
?>