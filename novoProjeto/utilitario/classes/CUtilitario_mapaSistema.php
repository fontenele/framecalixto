<?php
/**
 *
 */
class CUtilitario_mapaSistema extends controle{
	/**
	 * Método inicial
	 */
	public function inicial(){
		if(!isset($_GET['q'])) return;
		$this->responder($this->mapear(),$_GET['q']);
	}
	public static function mapearMenu(){
		
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
								$res[$classe] = "{$inter->pegarNome()},{$nomeAcao}";
							}else{
								$res[$classe] = "{$inter->pegarNome()},{$arAcao[1]}";
							}
						}else{
							$res[$classe] = "{$inter->pegarNome()},???????????????";
						}
					}
				}
				$diretorio->close();
			}
		}
		$d->close();
		return $res;
	}
	protected function responder($items,$q){
		if(!$q) return;
		$q = caracteres::RetiraAcentos($q);
		foreach ($items as $key=>$value) {
			if (($value) && strpos(caracteres::RetiraAcentos(strtolower($value)), $q) !== false) {
				echo "$key|$value\n";
			}
		}
	}
}
?>
