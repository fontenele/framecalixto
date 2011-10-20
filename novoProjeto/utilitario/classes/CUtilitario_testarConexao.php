<?php
/**
 * Testa a conexao com um banco de dados
 */
class CUtilitario_testarConexao extends controlePadrao{
	/**
	 * Método inicial
	 */
	public function  inicial() {
		if(isset($_REQUEST['banco'])){
			$tipo = isset($_REQUEST['banco']['tipo'][0])?$_REQUEST['banco']['tipo'][0]:null;
			$servidor = isset($_REQUEST['banco']['servidor'][0])?$_REQUEST['banco']['servidor'][0]:null;
			$porta = isset($_REQUEST['banco']['porta'][0])?$_REQUEST['banco']['porta'][0]:null;
			$banco = isset($_REQUEST['banco']['banco'][0])?$_REQUEST['banco']['banco'][0]:null;
			$usuario = isset($_REQUEST['banco']['usuario'][0])?$_REQUEST['banco']['usuario'][0]:null;
			$senha = isset($_REQUEST['banco']['senha'][0])?$_REQUEST['banco']['senha'][0]:null;
			try{
				$conn = conexao::criar(null, $servidor, $porta, $banco, $usuario, $senha, $tipo);
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}
	}
}
?>
