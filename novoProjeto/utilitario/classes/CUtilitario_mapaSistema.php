<?php
/**
 *
 */
class CUtilitario_mapaSistema extends controle{
	/**
	 * Método inicial
	 */
	public function inicial(){
		$this->mapear();
	}
	public static function mapear(){
		$d = dir(".");
		$res = array();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($diretorio = "./{$arquivo}/classes/") && ($arquivo{0} !== '.') ){
				$entidade = ucfirst($arquivo);
				$diretorio = dir($diretorio);
				$entidadeInternacionalizacao = definicaoEntidade::internacionalizacao("C{$entidade}");
				if(!is_file($diretorio->path.$entidadeInternacionalizacao.'.php')) continue;
				$inter = new $entidadeInternacionalizacao();
				while(false !== ($classe = $diretorio->read())){
					if($classe{0} == 'C'){
						$classe = str_replace('.php','',$classe);
						$arAcao  = explode('_',$classe);
						if(isset($arAcao[1])){
							$nomeAcao = $inter->pegarTexto($arAcao[1]);
							if($nomeAcao){
								$res[] = "{$inter->pegarNome()},{$nomeAcao}";
							}else{
								$res[] = "{$inter->pegarNome()},{$arAcao[1]}";
							}
						}else{
							$res[] = "{$inter->pegarNome()},???????????????";
						}
					}
				}
				$diretorio->close();
			}
		}
		$d->close();
		echo json_encode($res);
	}
}
?>
