String.prototype.trim = function (){return this.ltrim().rtrim();}
String.prototype.ltrim = function (){return new String(this.replace(eval("/^\ */") , ''));}
String.prototype.rtrim = function (){return new String(this.replace(eval("/\ *$/") , ''));}
String.prototype.strReplace = function (strAntiga, strNova){return new String(this.replace(eval("/"+strAntiga+"/g") , new String(strNova)));}
String.prototype.ucFirst = function(){return this.charAt(0).toUpperCase()+this.substr(1);}
/**
 * função para fazer lowerCamelCase();
 */
String.prototype.upperCamelCase = function (){
	palavra = this.toLowerCase();
	arPalavra = palavra.split(' ');
	palavraFim = '';
	for(i in arPalavra){
		palavraFim += arPalavra[i].ucFirst();
	}
	return palavraFim.retiraAcentos();
}
/**
 * função para fazer lowerCamelCase();
 */
String.prototype.lowerCamelCase = function (){
	palavra = this.upperCamelCase();
	return palavra.charAt(0).toLowerCase()+palavra.substr(1);
}
/**
 * funcao para retirar os acentos da string
 */
String.prototype.retiraAcentos = function(){
	str = this;
	stA = new String('çàèìòùâêîôûäëïöüáéííúãĩõũÇÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÁÉÍÓÚÃĨÕŨ');
	stB = new String('caeiouaeiouaeiouaeiouaiouÇAEIOUAEIOUAEIOUAEIOUAIOU');
	for(i in stA){ str = str.strReplace(stA.charAt(i),stB.charAt(i)); }
	return str;
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
