function Ajax(){
	var xmlhttp, completo = false;
	
	try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
	catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
	catch (e) { try { xmlhttp = new XMLHttpRequest(); }
	catch (e) { xmlhttp = false; }}}
	if (!xmlhttp) return null;
	/* Método que executa a conexão
	* param url: url a ser acessada;
	* param metodo: método de envio (GET ou POST);
	* vars: variáveis a serem enviadas;
	* funcao: função javascript que será chamada quando o resultado da requisição estiver preenchido.
	*/
	this.executaRequisicao = function(url, metodo, funcao, mostraAmpulheta){
		if( mostraAmpulheta == true ){
			mostraAmpulhetaAjax();
		}
		if (!xmlhttp) return false;
		completo = false;
		metodo = metodo.toUpperCase();
		try {
			if (metodo == "GET"){
				xmlhttp.open(metodo, url, true);
			}
			else{
				xmlhttp.open(metodo, url, true);
				xmlhttp.setRequestHeader("Method", "POST "+url+" HTTP/1.1");
				xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			}
			xmlhttp.onreadystatechange = function(){
				if (xmlhttp.readyState == 4 && !completo){
					if( mostraAmpulheta == true ){
						escondeAmpulhetaAjax();
					}
					if(funcao != null){
						texto = xmlhttp.responseText;
						padrao = /on line/;
						padrao2 = /\<html\>/;
						if( texto.search( padrao ) != -1 || texto.search( padrao2 ) != -1 ){
							alert( texto );
							return;
						}
						if( texto != '' ){
							eval(texto);
						}
						else{
							retorno = '';
						}
						funcao(retorno);
					}
					completo = true;
				}
			};
			xmlhttp.send(null);
		}
		catch(z) { return false; }
		return true;
	};
	
	return this;
	
}

function texto(){
	/**
	* Função simuladora da sprintf da linguagem C
	*/
	function sprintf() {
		if (!arguments ¦¦ arguments.length < 1 ¦¦!RegExp) { return; }
		var str = arguments[0];
		var re = /([^%]*)%('.¦0¦\x20)?(-)?(\d+)?(\.\d+)?(%¦b¦c¦d¦u¦f¦o¦s¦x¦X)(.*)/;
		var a = b = [], numSubstitutions = 0, numMatches = 0;
		while (a = re.exec(str)) {
			var leftpart = a[1], pPad = a[2], pJustify = a[3], pMinLength = a[4];
			var pPrecision = a[5], pType = a[6], rightPart = a[7]; numMatches++;
			if (pType == '%') {
				subst = '%';
			} else {
				numSubstitutions++;
				if (numSubstitutions >= arguments.length) {
					alert('Error! Not enough function arguments (' +
					(arguments.length - 1) + ', excluding the string)\n' +
					'for the number of substitution parameters in string (' +
					numSubstitutions + ' so far).');
				}
				var param = arguments[numSubstitutions];
				var pad = '';
				if (pPad && pPad.substr(0,1) == "'") {
					pad = leftpart.substr(1,1);
				} else if (pPad) {
					pad = pPad;
				}
				var justifyRight = true;
				if (pJustify && pJustify === "-") justifyRight = false;
				var minLength = -1;
				if (pMinLength) minLength = parseInt(pMinLength);
				var precision = -1;
				if (pPrecision && pType == 'f') {
					precision = parseInt(pPrecision.substring(1));
				}
				var subst = param;
				switch (pType) {
					case 'b': subst = parseInt(param).toString(2); break;
					case 'c': subst = String.fromCharCode(parseInt(param)); break;
					case 'd': subst = parseInt(param)? parseInt(param) : 0; break;
					case 'u': subst = Math.abs(param); break;
					case 'f': subst = (precision > -1)?
						Math.round(parseFloat(param) * Math.pow(10, precision)) /
						Math.pow(10, precision) : parseFloat(param); break;
					case 'o': subst = parseInt(param).toString(8); break;
					case 's': subst = param; break;
					case 'x': subst = ('' + parseInt(param).toString(16)).toLowerCase(); break;
					case 'X': subst = ('' + parseInt(param).toString(16)).toUpperCase(); break;
				}
				var padLeft = minLength - subst.toString().length;
				if (padLeft > 0) {
					var arrTmp = new Array(padLeft+1);
					var padding = arrTmp.join(pad?pad:" ");
				} else {
				var padding = ""; } 
			}
			str = leftpart + padding + subst + rightPart;
		}
		return str;
	} 
	/**
	* Retorna a validação de um email 
	*/
	function email(valor){
		re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
		a = re.exec(valor);
		if(!a) alert('Email inválido!');
	}
}
