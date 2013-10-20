<?php
/**
 * Smarty plugin
 * @package	   Externas
 * @subpackage Smarty:plugins
 */


/**
 * Smarty {math} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * @link http://smarty.php.net/manual/en/language.function.math.php {math}
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_menu($params, &$smarty)
{
	if(isset($params['icone'])){
		$icone = $params['icone'];
	}
	if(isset($params['texto'])){
		$texto = $params['texto'];
	}
	if(isset($params['url'])){
		$link = $params['url'];
	}
	if(isset($params['controle'])){
		global $definicoes;
		$link = sprintf('?c=%s',$params['controle']);
		$controleDeAcesso = $definicoes->xpath('//controleDeAcesso');
		$liberado = strval($controleDeAcesso[0]['liberado']) == 'sim';
		if(!($liberado || isset($smarty->_tpl_vars[' acessosLiberados'][$params['controle']]))){
			return '';
		}
	}
	return "<li><a href={$link}>{$icone} {$texto}</a></li>";
}

/* vim: set expandtab: */

?>
