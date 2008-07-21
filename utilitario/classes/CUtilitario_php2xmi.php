<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Atualizador de Base de Dados
*/
class CUtilitario_php2xmi extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
//		$classes = array();
//		$d = new diretorio("./",false);
//		while (false !== ($entry = $d->read())) {
//			if(preg_match('/^(.*)php$/', $entry, $res)){
//				switch (true) {
//					case ($entry == './index.php'):
//					case ($entry == './.sistema/definicoes.php'):
//					case ($entry == './.calixto/debug.php'):
//					case (preg_match('/^\.\/\.calixto\/classes\/externas.*/', $entry, $res)):
//					case (preg_match('/^\.\/\.calixto\/classes\/padroes\/conexoes.*/', $entry, $res)):
//					case (preg_match('/^\.\/\.tmp.*/', $entry, $res)):
//					case (preg_match('/^(.*).mysql.php$/', $entry, $res)):
//					break;
//					default:
//						preg_match('/^((.*)\.postgres\.php|(.*)\.php)$/', basename($entry), $res);
//						$classes[$res[1]] = array_pop($res);
//					break;
//				}
//			}
//		}
//		$d->close();
//		
//		$classes = array('persistente'=>'persistente');
		
		$uml = new uml();
		$uml->parseDirectory('.calixto/classes/','sem Nome');
		$uml->ignoredDirectories = array('externas');
		/**
		 * Example 1 : Basic example with file "test.php"
		 */
		
		
		$uml->generateXMI(1);                     // Generation of XMI in version 1
		$uml->saveXMI('/var/www/html/framecalixto/.tmp/framework.xmi');       // We save the XMI code
		
//*
//		 * Example 2 : Advanced example with files "test.php" and "test2.php"
//		 
/*		
		$t = new PHP_UML;
		
		$t->dollar             = false;    // We don't keep the $ before the variable names
		$t->componentView      = false;    // We don't want a component view to be included (subsystems and components)
		$t->deploymentView     = true;     // We want a deployment view (artifacts and packages)
		$t->docblocks          = true;     // We want the parser to look into the class / file comments
		$t->ignoredDirectories = array();  // We want to scan all directories
		
		// We parse "test.php" and "test2.php". The default namespace will be 'testModel':
		$t->parseFile(array('test1.php', 'test2.php'), 'testModel');    
		$t->generateXMI(2, 'utf-8');        // Generation of XMI in version 2
		$t->saveXMI('test_example2.xmi');   // We save the XMI code in a file "test.xmi"
		echo htmlentities($t->XMI);         // And we echo it.
		
		
//*
//		 * Example 3 : Example with the whole current directory
//		 		 
		$t = new PHP_UML;
		
		$t->parseDirectory('./');
		$t->generateXMI(1, 'iso-8859-1');
		$t->saveXMI('./tmp/test_example3.xmi');		
	*/	
		
	}
}
?>