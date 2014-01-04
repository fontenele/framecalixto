String.prototype.trim = function (){return this.ltrim().rtrim();};
String.prototype.ltrim = function (){return new String(this.replace(eval("/^\ */") , ''));};
String.prototype.rtrim = function (){return new String(this.replace(eval("/\ *$/") , ''));};
String.prototype.strReplace = function (strAntiga, strNova){return new String(this.replace(eval("/"+strAntiga+"/g") , new String(strNova)));};
String.prototype.ucFirst = function(){return this.charAt(0).toUpperCase()+this.substr(1);};
/**
 * função para fazer CamelCase();
 */
String.prototype.CamelCase = function (tipo){
	palavra = this.strReplace(' ','_');
	arPalavra = palavra.split('_');
	if(arPalavra.length > 1){
		palavra = this.strReplace(' ','_').toLowerCase().retiraAcentos();
		arPalavra = palavra.split('_');
		primeira = arPalavra.shift();
		palavraFim = '';
		for(i in arPalavra){
			palavraFim += arPalavra[i].ucFirst();
		}
		
	}else{
		primeira = this;
		palavraFim = '';
	}
	if(tipo !== 'lower') primeira = primeira.ucFirst();
	return primeira + palavraFim;
};
/**
 * função para fazer upperCamelCase();
 */
String.prototype.upperCamelCase = function (){
	return this.CamelCase('upper');
};
/**
 * função para fazer lowerCamelCase();
 */
String.prototype.lowerCamelCase = function (){
	return this.CamelCase('lower');
};
/**
 * função para fazer lowerCamelCase();
 */
String.prototype.lowerCamelCase = function (){
	palavra = this.upperCamelCase();
	return palavra.charAt(0).toLowerCase()+palavra.substr(1);
};
/**
 * funcao para retirar os acentos da string
 */
String.prototype.retiraAcentos = function(){
	str = this;
	stA = new String('çàèìòùâêîôûäëïöüáéíóúãĩõũÇÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÁÉÍÓÚÃĨÕŨ');
	stB = new String('caeiouaeiouaeiouaeiouaiouÇAEIOUAEIOUAEIOUAEIOUAIOU');
	for(i in stA){ str = str.strReplace(stA.charAt(i),stB.charAt(i)); }
	return str;
};
String.prototype.retiraEspeciais = function(){
	return this.replace(/[-[\]{}()*+?%&@!?¨:;'"<>/=\\^$|#\b]/g, "");	
};
String.prototype.makeLowerUnderLine = function(){
	return this.toLowerCase().retiraAcentos().strReplace('[^a-zA-Z0-9_]', '_');
};
String.prototype.makeUpperUnderLine = function(){
	return this.toUpperCase().retiraAcentos().strReplace('[^a-zA-Z0-9_]', '_');
};
/**
 * Funcao simuladora da number_format
 */
function number_format (number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

/**
* Função simuladora da sprintf da linguagem C
*/
function sprintf() {
	try{
		if (!arguments || arguments.length < 1 ||!RegExp) { return; }
		var str = arguments[0];
		var re = /([^%]*)%('.|0|\x20)?(-)?(\d+)?(\.\d+)?(%|b|c|d|u|f|o|s|x|X)(.*)/;
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
	catch(e){alert(e);}
}
