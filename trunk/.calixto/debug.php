<?php
/**
* Funções criadas para ajudar o desenvolvedor a visualizar e encontrar erros
* @package Infra-estrutura
* @subpackage Debug
*/

/**
* Função para debugar com exibição tipo var_dump
* @param [mixed]
* @return [string]
*/
function debug1($var){
	ob_start();
	echo '<link rel="stylesheet" href=".calixto/estilos/debug.css" />';
	echo '<div class=debug><pre>';
	var_dump($var);
	echo '</pre></div>';
	echo ob_get_clean();
}
/**
* Função para debugar com exibição lógica estrutural em tabelas
* @param [mixed]
* @param [metodos]
* @param [visualizacao]
* @return [string]
*/
function debug2($var,$metodos = true, $visualizacao = false){
	echo '<link rel="stylesheet" href=".calixto/estilos/debug.css" />';
	switch(true){
		case is_bool($var): 
			echo ($var ? '<font class=tipoPrimario >(booleano)</font> = <font class=booleano >true</font>' : '<font class=tipoPrimario>(booleano)</font> = <font class=booleano >false</font>');
		break;
		case is_integer($var): 
			echo '<font class=tipoPrimario >(integer)</font> = <font class=numero >'.((int) $var).'</font>';
		break;
		case is_double($var): 
			echo '<font class=tipoPrimario >(double) = <font class=numero >'.((double) $var).'</font>';
		break;
		case is_float($var): 
			echo '<font class=tipoPrimario >(float) = <font class=numero >'.((float) $var).'</font>';
		break;
		case is_string($var): 
			echo '<font class=tipoPrimario >(string) = <font class=string >"'.((string) $var).'"</font>';
		break;
		case is_array($var):
			echo '<table border=1 class=array><tr><td><table class=itens>';
			foreach($var as $indice => $valor){
				echo "<tr><td><font class=keyword>[$indice]=></font></td><td>";
				echo debug2($valor,$metodos);
				echo '</td></tr>';
			}
			echo '</tr></table></td></tr></table>';
		break;
		case is_object($var):
			echo '<table border=1 class=objeto><tr><td><table class=propriedades>';
			if($metodos){
				foreach(get_class_methods($var) as $propriedade => $valor){
					echo '<tr><td>-><font class=metodo>'.$valor.'</font>()</td></tr>';
				}
			}
			switch(true){
				case ($var instanceof TData):
				case ($var instanceof TNumerico):
					echo $var;
				break;
				case (($var instanceof visualizacao) && !$visualizacao):
				break;
				default:
					foreach(get_object_vars($var) as $propriedade => $valor){
						switch (true){
							case is_object($valor):
								echo '<tr><td><font class=keyword>var <font class=tipoClasse >('.get_class($valor).')</font></font> <font class=variavel>$'.$propriedade.'</font></td><td>';
							break;
							case is_array($valor):
								echo '<tr><td><font class=keyword>var <font class=tipoPrimario >(array)</font></font> <font class=variavel>$'.$propriedade.'</font></td><td>';
							break;
							default:
								echo '<tr><td><font class=keyword>var </font><font class=variavel>$'.$propriedade.'</font></td><td>';
							break;
						}
						echo debug2($valor,$metodos);
						echo '</td></tr>';
					}
			}
			echo '</tr></table></td></tr></table>';
		break;
		case is_resource($var): 
			echo '<font class=tipoPrimario >(resource)</font> = '.$var;
		break;
		case is_null($var): 
			echo '<font class=tipoPrimario > (null)</font> = <font class=nulo >null</font>';
		break;
		case true:
			echo '<font class=tipoPrimario >(mixed)</font> = "'.$var.'"';
		break;
	}
}
/**
* Função para debugar com exibição da classe
* @param [mixed]
* @return [string]
*/
function debug3(objeto $var){
		echo '<link rel="stylesheet" href=".calixto/estilos/debug.css" />';
		echo '<div class=debug><pre>';
		ob_start();
		Reflection::export(new ReflectionClass($var));
		$out = ob_get_clean();
		$out = highlight_string("<?php\n".$out."?>");
		echo '</div></pre>';
}
/**
* Função para debugar 
* @param [mixed]
* @return [string]
*/
function x(){
	$args = func_get_args();
	$ar = debug_backtrace();
	echo "<div class='debug'>Chamada da função x no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
	foreach($args as $x){
		echo debug2($x,false,false);
	}
}
function x1($x){
	$ar = debug_backtrace();
	echo "<div class='debug'>Chamada da função x1 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
	echo debug1($x);
}
function x2($x,$metodos = false, $visualizacao = false){
	$ar = debug_backtrace();
	echo "<div class='debug'>Chamada da função x2 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
	echo debug2($x,$metodos, $visualizacao);
}
function x3($x){
	$ar = debug_backtrace();
	echo "<div class='debug'>Chamada da função x3 no arquivo:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
	echo debug3($x);
}
?>
