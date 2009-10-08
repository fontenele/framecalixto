//====================================================================================
//
//====================================================================================
/**
 * função para fazer lowerCamelCase();
 */
function upperCamelCase(nome){
	nome = nome.toLowerCase();
	arNome = nome.split(' ');
	nomeFim = '';
	for(i in arNome){
		ucfirst = arNome[i].charAt(0).toUpperCase();
		nomeFim += ucfirst + arNome[i].substr(1,arNome[i].length);
	}
	return RetiraAcentos(nomeFim);
}
/**
 * função para fazer lowerCamelCase();
 */
function lowerCamelCase(nome){
	nome = nome.toLowerCase();
	arNome = nome.split(' ');
	nomeFim = '';
	for(i in arNome){
		ucfirst = arNome[i].charAt(0);
		if(i > 0){
			ucfirst = arNome[i].charAt(0).toUpperCase();
		}
		nomeFim += ucfirst + arNome[i].substr(1,arNome[i].length);
	}
	return RetiraAcentos(nomeFim);
}
/**
* Debug para javascript
*/
function x(obj){
	document.write('<pre>' + var_dump(obj) + '</pre>');
}
function var_dump(obj,tab) {
	var tab = tab || 0;
	var tabulacao = '';
	if(tab > 0)for(i = 0; i < tab; i++){tabulacao += "    ";}
	if(typeof obj == "object") {
		res = '';
		for(i in obj){
			res += tabulacao + '[' + i + '] => ' + var_dump(obj[i],tab + 1);
		}
		return "\n" + res;
	}
	return "(" + typeof(obj) + ") '" + obj + "'\n";
}//end function var_dump

/**
* Esta função captura uma tecla digitada
* Parametros necessarios :
* @var [ob] event =>  evento para a captura ex: onKeypres, onKeyup,onKeydown.
* @return [txt] codigo da tecla capturada
*/
function CapturaTecla(evento){
    return (navigator.appName.indexOf("Netscape")!= -1) ? evento.which : evento.keyCode;
}
/**
*   Esta função preenche um componente com o valor de outro
* @var [ob] objeto html preenchedor
* @var [ob] objeto html a ser preenchido
* @var [ar] array de dados correlativo (opcional)
*/
function preencheCampo(Preenchedor, Preenchido, arDados){
    arDados = arDados || '';
    if(arDados == ''){
        Preenchido.value = '';
        Preenchido.value = Preenchedor.value;
        return;
    }else{
        Preenchido.value = '';
        if(Preenchedor.type == 'select-one'){
            for(dado in arDados){
                if(Preenchedor.value == arDados[dado].Id){
                    Preenchido.value = arDados[dado].Cd;
                    return;
                }
            }
        }else{
            for(dado in arDados){
                if(Preenchedor.value == arDados[dado].Cd){
                    Preenchido.value = arDados[dado].Id;
                    return;
                }
            }
        }
    }
}
/**
* Função simuladora da sprintf da linguagem C
* @return [txt] texto formatado
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
/**
* Simula a função str_replace do PHP
* @param [] string de busca
* @param [] string de substituicao
* @param [] string original
*/
function str_replace(strAntiga, strNova, strOriginal){
	str 	= new String(strOriginal);
	rExp	= "/"+strAntiga+"/g";
	rExp	= eval(rExp);
	newS	= String(strNova);
	str = new String(str.replace(rExp, newS));
	return str;
}
/**
* Retorna a validação de um email
* @var [txt] endereço eletrônico de email
* @return [booleano]
*/
function validarEmail(valor){
	try{
		re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
		a = re.exec(valor.value);
		if(!a) {
            alert(JS_ERRO_EMAIL);
            valor.value = '';
        }
	}
	catch(e){
        alert(e);
        valor.value = '';
    }
}
//====================================================================================
//                      FORMATACAO DE DATA E HORA
//====================================================================================
/**
* funcao de formatacao de data
* @var [ob] objeto html a ser formatado
* @var [ob] evento para a captura
* @var [txt] separador de data
* @var [txt] formato da data : DDMMYYYY , MMDDYYYY , YYYYMMDD
*/
function formatarData(Componente, evento, separadorData, stFormato){
	try{
		var Enter= 13;
		var BackSpace= 8;
		var Caracter;
		var CodigoTecla;
		CodigoTecla = CapturaTecla(evento);
		Caracter = String.fromCharCode(CodigoTecla);
		if (CodigoTecla== Enter)    return false;
		if (CodigoTecla== BackSpace)return true;
		switch (stFormato){
			case 'DDMMYYYY':
				var nrPrimeiroTamanho = 2;
				var nrSegundoTamanho = 5;
			break;
			case 'MMDDYYYY':
				var nrPrimeiroTamanho = 2;
				var nrSegundoTamanho = 5;
			break;
			case 'YYYYMMDD':
				var nrPrimeiroTamanho = 4;
				var nrSegundoTamanho = 7;
			break;
		}
		if((Componente.value.length == nrPrimeiroTamanho)||(Componente.value.length == nrSegundoTamanho)) {
			if (CodigoTecla != 0){Componente.value += separadorData;}
		}
	}
	catch(e){alert(e);}
}
/**
* funcao de validacao de data
* @var [ob] objeto html a ser formatado
* @var [txt] separador de data
* @var [txt] formato da data : DDMMYYYY , MMDDYYYY , YYYYMMDD
* @var [txt] dataAtual para complemento
*/
function checarData(Componente,separadorData,stFormato, stDataAtual){
	try{
		if(Componente.value == "") return;
		var re = /^[1-9]$/;
		if(re.exec(Componente.value)) Componente.value = '0' + Componente.value;
		var arData  = Componente.value.split(separadorData);
		var arDataAtual  = stDataAtual.split(separadorData);
		if(arData.length > 3) throw 1;
		switch (stFormato){
			case 'DDMMYYYY':
				var inDia   = new String (arData[0]);
				switch(arData.length){
				case 3:
				var inMes   = new String (arData[1]);
				var inAno   = new String (arData[2]);
				break;
				case 2:
				var inMes   = new String (arData[1]);
				var inAno   = new String (arDataAtual[2]);
				break;
				case 1:
				var inMes   = new String (arDataAtual[1]);
				var inAno   = new String (arDataAtual[2]);
				break;
				}
			break;
			case 'MMDDYYYY':
				var inDia   = new String (arData[1]);
				switch(arData.length){
				case 3:
				var inMes   = new String (arData[0]);
				var inAno   = new String (arData[2]);
				break;
				case 2:
				var inMes   = new String (arData[0]);
				var inAno   = new String (arDataAtual[2]);
				break;
				case 1:
				var inMes   = new String (arDataAtual[0]);
				var inAno   = new String (arDataAtual[2]);
				break;
				}
			break;
			case 'YYYYMMDD':
				var inDia   = new String (arData[2]);
				switch(arData.length){
				case 3:
				var inMes   = new String (arData[1]);
				var inAno   = new String (arData[0]);
				break;
				case 2:
				var inMes   = new String (arData[1]);
				var inAno   = new String (arDataAtual[0]);
				break;
				case 1:
				var inMes   = new String (arDataAtual[1]);
				var inAno   = new String (arDataAtual[0]);
				break;
				}
			break;
		}
		var re = /^(0[1-9]|1[0-2])$/;
		var resMes = re.exec(inMes);
		var re = /^(2[0-1]|19)\d{2}$/;
		var resAno = re.exec(inAno);
		var re = /^(0[1-9]|[1-2][0-9])\d{2}|(310[13578])|(311(0|2))|(300[^02])|301(0|1|2)$/;
		var resDia = re.exec(inDia + inMes);
		if (!resAno) throw 2;
		if (!resMes) throw 3;
		if (!resDia) throw 4;
		if ( inMes == 02 ) {
			if ( inDia > 29 ) throw 4;
			if ( !( ( inAno % 4 == 0 ) && ( ( inAno % 100 != 0 ) || ( inAno % 400 == 0 ) ) ) && ( inDia > 28 ) )
				throw 4;
		}
		switch (stFormato){
			case 'DDMMYYYY': Componente.value = inDia + separadorData + inMes + separadorData + inAno; break;
			case 'MMDDYYYY': Componente.value = inMes + separadorData + inDia + separadorData + inAno; break;
			case 'YYYYMMDD': Componente.value = inAno + separadorData + inMes + separadorData + inDia; break;
		}
	}
	catch(e){
		switch(e){
			case 1: alert(JS_ERRO_DATA);break;
			case 2: alert(sprintf(JS_ERRO_ANO, inAno));break;
			case 3: alert(sprintf(JS_ERRO_MES, inMes));break;
			case 4: alert(sprintf(JS_ERRO_DIA, inDia));break;
		}
		Componente.focus();
		Componente.value = "";
	}
}
/**
* funcao de formatacao de hora
* @var [ob] objeto html a ser formatado
* @var [ob] evento para a captura
*/
function formatarHora(Componente, evento){
    var Enter= 13;
    var BackSpace= 8;
    var Caracter;
    var CodigoTecla;

    CodigoTecla = CapturaTecla(evento);
    Caracter = String.fromCharCode(CodigoTecla);

    if (CodigoTecla== Enter)    return false;
    if (CodigoTecla== BackSpace)return true;

    if((Componente.value.length == 2)||(Componente.value.length == 5)) {
        if (CodigoTecla != 0){Componente.value += ":";}
    }
}
/**
* funcao de validacao de hora
* @var [object] Componente de formulario
*/
function checarHora(Componente){
	try{
		if(Componente.value == '') return;
		var re = /^[1-9]$/;
		if(re.exec(Componente.value)) Componente.value = '0' + Componente.value;
		var er = /^(([0-1][0-9]|2[0-3])$|([0-1][0-9]|2[0-3]):[0-5][0-9])$|(([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])$/;
		var a = er.exec(Componente.value);
		var arHora = Componente.value.split(':');
		if(!a) throw sprintf(JS_ERRO_HORA,Componente.value);
		if(arHora.length > 3) throw sprintf(JS_ERRO_HORA,Componente.value);
		switch(arHora.length){
			case 1: Componente.value += ':00:00';break;
			case 2: Componente.value += ':00';;break;
		}
	}
	catch(e){
		alert(e);
		Componente.focus();
		Componente.value = "";
	}
}
//====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE NUMERICO
//====================================================================================
function formatarNumero(Componente,charDecimal,charMilhar,nrCasasDecimais,simbolo,MaxValor,posicaoDoSimbolo,stNegativo) {
    if(Componente.value == "") return ;
    var stString = new String( Componente.value );
    var stResultado = "";
    if(MaxValor != "") {
        if(parseFloat(DesformataNumero(stString,simbolo,charDecimal,charMilhar)) > parseFloat(MaxValor)) {
            MaxValor = MaxValor.replace('.',charDecimal);
            MaxValor = FormataNumero(MaxValor,charDecimal,charMilhar,nrCasasDecimais,simbolo,posicaoDoSimbolo);
            alert(K_MAX_VALOR_ACEITAVEL + MaxValor);
            stResultado = MaxValor;
            Componente.focus();
        } else {
            stResultado = FormataNumero(stString,charDecimal,charMilhar,nrCasasDecimais,simbolo,posicaoDoSimbolo,stNegativo);
        }
    }
    Componente.value = stResultado;
}

/*  Esta função retorna o valor de uma string para o formato de Numero
    Parametros necessarios :
    *   String          =>  String para formatacao
    *   charDecimal     =>  Character separador decimal
    *   charMilhar      =>  Character separador de milhar
    *   nrCasasDecimais=>   Numero de casas decimais
    *   simbolo         =>  simbolo da unidade de medida
    *   posicaoDoSimbolo    => Posicao do simbolo numerico 'E' esquerda ou 'D' direita
    sintaxe ==>> FormataNumero(String,charDecimal,charMilhar,nrCasasDecimais,simbolo);*/
function FormataNumero(stValor,charDecimal,charMilhar,nrCasasDecimais,simbolo,posicaoDoSimbolo,stNegativo){
	stNegativo = stNegativo || '-';
	posicaoDoSimbolo = posicaoDoSimbolo || 'E';
    var STR = new String(stValor);
    var PosicaoDecimal = AchaCharacter(STR,charDecimal,"esquerda");
    var ParteInteira = "";
    var ParteDecimal = "";
    var Resultado = "";
    var negativo = false;

    if(PosicaoDecimal == 0){
        ParteInteira = LimpaNumero(STR,"Inteiro");
        ParteInteira = SeparaEmMilhar(ParteInteira,charMilhar);
        ParteDecimal = FixaTamanho(ParteDecimal,nrCasasDecimais);
    }
    else{
        ParteInteira = STR.substr(0,AchaCharacter(STR,charDecimal,"esquerda") - 1);
        ParteInteira = SeparaEmMilhar(ParteInteira,charMilhar);
        ParteDecimal = STR.substr(AchaCharacter(STR,charDecimal,"esquerda") ,STR.length);
        ParteDecimal = FixaTamanho(ParteDecimal,nrCasasDecimais);
    }
    if(nrCasasDecimais == 0){charDecimal = '';}
    if(posicaoDoSimbolo == 'E' || posicaoDoSimbolo == 'esquerda'){
        Resultado = simbolo + ParteInteira + charDecimal + ParteDecimal;
    }else{
        Resultado = ParteInteira + charDecimal + ParteDecimal + simbolo ;
    }
    if(negativo){
        if(stNegativo == '-'){
            Resultado = '-' + Resultado;
        }else{
            Resultado = '(' + Resultado + ')';
        }
    }
    return Resultado;
}
function desformatarNumero(Componente,stUnMedida,charDecimal,charMilhar){
    var stFormatada = Componente.value;
    var STRfinal;
    var negativo = false;

    if ((stFormatada.charAt(0) == '-')||(stFormatada.charAt(0) == '(')){ negativo = true; }
    STRfinal = stFormatada.replace('(',''); 	// Retirando simbolo
    STRfinal = STRfinal.replace(')','');    	// Retirando simbolo
    STRfinal = STRfinal.replace('-','');    	// Retirando simbolo
    STRfinal = STRfinal.replace(stUnMedida,''); // Retirando simbolo
    STRfinal = STRfinal.replace(charMilhar,''); // Retirando Separador de Milhar
    if(negativo){
        STRfinal = '-' + STRfinal;
    }
    Componente.value = STRfinal;
}
/*  Esta função retira a mascara de uma string com o formato numerico
    Parametros necessarios :
    *   stFormatada =>  String com formato numerico,
    *   stUnMedida  =>  simbolo da unidade de medida
    *   charDecimal =>  Character separador decimal
    *   charMilhar  =>  Character separador de milhar
    sintaxe ==>> DesformataNumero(stNumero,stSimbolo,charDecimal,charMilhar);*/
function DesformataNumero(stFormatada,stUnMedida,charDecimal,charMilhar){
    var STRfinal;
    STRfinal = stFormatada.replace(stUnMedida,'');  // Retirando simbolo
    STRfinal = STRfinal.replace(charMilhar,'');     // Retirando Separador de Milhar
    STRfinal = STRfinal.replace(charDecimal,'.');   // Ajustar decimal
    return STRfinal;
}

/*  Esta função inverte o conteudo de uma string Ex: calixto => otxilac
    Parametros necessarios :
    *   STR     =>  String,
    sintaxe ==>> InverteString(STR)*/
function InverteString(STR){
    var STRfinal = "";
    var i = STR.length;
    while (i >= 0){ STRfinal += STR.charAt(i);  i--;}
    return STRfinal;
}

/*  Esta função procura um caracter em uma string e retorna a posicao da primeira ocorrencia
    Parametros necessarios :
    *   STR         =>  String para a busca,
    *   Character   =>  Character procurado,
    *   Inicio      =>  Inicio da procura ('direira' ou 'esquerda')
    sintaxe ==>> AchaCharacter(STR,Character,Inicio)*/
function AchaCharacter(STR,Character,Inicio){
    var posicao;
    if(Inicio == "direita"){posicao = InverteString(STR).indexOf(Character,0);}
    else{posicao = STR.indexOf(Character,0);}
    return posicao + 1 ;
}

/*  Esta função procura um caracter em uma string e retorna a posicao da primeira ocorrencia
    Parametros necessarios :
    *   STR         =>  String para a busca,
    *   Character   =>  Character procurado,
    *   Inicio      =>  Inicio da procura ('direira' ou 'esquerda')
    sintaxe ==>> AchaCharacteres(STR,Character,Inicio)*/
function AchaCharacteres(STR,Character,Inicio){
    var posicao;
    if(Inicio == "direita"){
        STR = InverteString(STR);
    }
    var tamanho = 0;
    for(var i=0; i< STR.length; i++){
        if(STR.charAt(i) == Character ){tamanho++;}
    }
    var arPosicoes = new Array(tamanho);
    var tamanho = 0;
    for(var i=0; i< STR.length; i++){
        if(STR.charAt(i) == Character ){
            arPosicoes[tamanho] = i;
            tamanho++;
        }
    }
    return arPosicoes;
}

/*  Esta função retira os caracteres diferentes de numeros da string passada
    Parametros necessarios :
    *   STR     =>  String com o numero,
    *   Tipo    =>  'decimal' para nao suprimir zeros a direita
    sintaxe ==>> LimpaNumero(STR,Tipo)*/
function LimpaNumero(STR,Tipo){
    var STRfinal = "";
    var i=0;
    if(STR == ""){return String(0);}
    while (i <= STR.length){
        if(STR.charAt(i)!= " " && !isNaN(STR.charAt(i))){STRfinal = STRfinal + STR.charAt(i);}
        i++;
    }
    if(Tipo == "Decimal"){return STRfinal;}
    if(STRfinal.length > 0){
        STRfinal = parseInt(STRfinal,10);
        STRfinal = String(STRfinal);
    }
    else{ STRfinal = String(0);
    }
    return STRfinal;
}

function RetiraAcentos(STR){
	STR = str_replace('ç','c', STR);
	STR = str_replace('à','a', STR);
	STR = str_replace('è','e', STR);
	STR = str_replace('ì','i', STR);
	STR = str_replace('ò','o', STR);
	STR = str_replace('ù','u', STR);
	STR = str_replace('â','a', STR);
	STR = str_replace('ê','e', STR);
	STR = str_replace('î','i', STR);
	STR = str_replace('ô','o', STR);
	STR = str_replace('û','u', STR);
	STR = str_replace('ä','a', STR);
	STR = str_replace('ë','e', STR);
	STR = str_replace('ï','i', STR);
	STR = str_replace('ö','o', STR);
	STR = str_replace('ü','u', STR);
	STR = str_replace('á','a', STR);
	STR = str_replace('é','e', STR);
	STR = str_replace('í','i', STR);
	STR = str_replace('ó','o', STR);
	STR = str_replace('ú','u', STR);
	STR = str_replace('ã','a', STR);
	STR = str_replace('õ','o', STR);
	STR = str_replace('À','A', STR);
	STR = str_replace('Ç','C', STR);
	STR = str_replace('È','E', STR);
	STR = str_replace('Ì','I', STR);
	STR = str_replace('Ò','O', STR);
	STR = str_replace('Ù','U', STR);
	STR = str_replace('Â','A', STR);
	STR = str_replace('Ê','E', STR);
	STR = str_replace('Î','I', STR);
	STR = str_replace('Ô','O', STR);
	STR = str_replace('Û','U', STR);
	STR = str_replace('Ä','A', STR);
	STR = str_replace('Ë','E', STR);
	STR = str_replace('Ï','I', STR);
	STR = str_replace('Ö','O', STR);
	STR = str_replace('Ü','U', STR);
	STR = str_replace('Á','A', STR);
	STR = str_replace('É','E', STR);
	STR = str_replace('Í','I', STR);
	STR = str_replace('Ó','O', STR);
	STR = str_replace('Ú','U', STR);
	STR = str_replace('Ã','A', STR);
	STR = str_replace('Õ','O', STR);
	return STR;
}
/*  Esta função separa em milhar o numero passado
    Parametros necessarios :
    *   STR         =>  String com o numero,
    *   charMilhar  =>  Character separador de milhar
    sintaxe ==>> SeparaEmMilhar(STR,charMilhar)*/
function SeparaEmMilhar(STR,charMilhar){
    var STRfinal = "";
    var i=1;
    STR = LimpaNumero(STR,"Inteiro");
    STR = InverteString(STR);
    while (i <= STR.length){
        STRfinal += STR.charAt(i-1);
        if(i%3 == 0){STRfinal += charMilhar}
        i++;
    }
    if(STRfinal != ""){
        STRfinal = InverteString(STRfinal);
        if(STRfinal.substr(0,1) == "."){STRfinal = (STRfinal.substr(1,STRfinal.length));};
        return STRfinal;
    }else{
        return String(0);
    }
}

/*  Esta função fixa o tamanho da parte decimal de um numero
    Parametros necessarios :
    *   STR             =>  String com a parte decimal do numero,
    *   nrCasasDecimais =>  Numero de casas decimais
    sintaxe ==>> FixaTamanho(STR,nrCasasDecimais)*/
function FixaTamanho(STR,nrCasasDecimais){
    STR = LimpaNumero(STR,"Decimal");
    var i = STR.length;
    if(i < nrCasasDecimais){
        while(nrCasasDecimais > i ){
            STR = STR + "0";
            i++;
        }
    }
    if(i > nrCasasDecimais){
        STR = STR.substr(0,STR.length - (STR.length - nrCasasDecimais));
    }
    return STR;
}
//====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE TELEFONE
//====================================================================================
function desformatarTelefone(componente){
	if(componente.value)
	componente.value = LimpaNumero(componente.value,"Decimal");
}
function formatarTelefone(componente){
    var STRfinal = "";
    var i=1;
	STR = InverteString(componente.value);
    while (i <= STR.length){
        STRfinal = STR.charAt(i-1) + STRfinal;
        if(i == 4){STRfinal = '-' + STRfinal;}
		if(i == 8 && STR.length > 8){STRfinal = ')' + STRfinal;}
        i++;
    }
	if(STR.length > 8){STRfinal = '(' + STRfinal;}
	componente.value = STRfinal;
}
//====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE CEP
//====================================================================================
function desformatarCep(componente){
	if(componente.value)
	componente.value = LimpaNumero(componente.value,"Decimal");
}
function formatarCep(componente){
    var STRfinal = "";
    var i=1;
	STR = InverteString(componente.value);
    while (i <= STR.length){
        STRfinal = STR.charAt(i-1) + STRfinal;
        if(i == 3){STRfinal = '-' + STRfinal;}
		if(i == 6){STRfinal = '.' + STRfinal;}
        i++;
    }
	componente.value = STRfinal;
}
//====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE NOME COMPLETO
//====================================================================================
/**
* funcao de formatacao de data
* @var [ob] objeto html a ser formatado
* @var [ob] evento para a captura
* @var [txt] separador de data
* @var [txt] formato da data : DDMMYYYY , MMDDYYYY , YYYYMMDD
*/
function validarNome(Componente, evento){
	try{
		var Enter= 13;
		var BackSpace= 8;
		var Caracter;
		var CodigoTecla;
		CodigoTecla = CapturaTecla(evento);
		Caracter = String.fromCharCode(CodigoTecla);
		if (CodigoTecla== Enter)    return false;
		if (CodigoTecla== BackSpace)return true;
		if (!CodigoTecla) return true;
		re = /([aA-zZ]|[\ ])/;
		if(re.exec(Caracter)){
			if(Caracter == '\\') return false;
			if(Caracter == '_') return false;
			return true;
		}else{
			if(Caracter == 'Ç') return true;
			if(Caracter == 'ç') return true;
			return false;
		}
	}
	catch(e){alert(e);}
}
/**
* função de validação de nome completo
* @var [ob] objeto html a ser formatado
*/
function validarNomeCompleto(componente){
	try{
		if(!componente.value) return;
		re = /^(a{3,}|b{3,}|c{3,}|d{3,}|e{3,}|f{3,}|g{3,}|h{3,}|i{3,}|j{3,}|k{3,}|l{3,}|m{3,}|n{3,}|o{3,}|p{3,}|q{3,}|r{3,}|s{3,}|t{3,}|u{3,}|v{3,}|x{3,}|z{3,}|w{3,}|y{3,}|k{3,})$/i;
		if(re.exec(componente.value)){ throw 1; }
		re = /^[aA-zZ]+\ [aA-zZ]+/;
		if(!re.exec(componente.value)){ throw 1; }
	}
	catch(e){
		alert('Nome inválido ou incompleto!');
		componente.value = '';
	}
}
////====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE CPF/CNPJ
//====================================================================================
function desformatarDocumentoPessoal(componente){
	if(componente.value)
	componente.value = LimpaNumero(componente.value,"Decimal");
}
function formatarDocumentoPessoal(componente, tipo ){
	var tipo = tipo || "cpf";
    var STRfinal = "";
    var i=1;
	STR = InverteString(componente.value);
	if(tipo == "cpf"){
		while (i <= STR.length){
			if(i == 12) break;
			STRfinal = STR.charAt(i-1) + STRfinal;
			if(i == 2){STRfinal = '-' + STRfinal;}
			if(i == 5){STRfinal = '.' + STRfinal;}
			if(i == 8){STRfinal = '.' + STRfinal;}
			i++;
		}
	}else{
		while (i <= STR.length){
			if(i == 15) break;
			STRfinal = STR.charAt(i-1) + STRfinal;
			if(i == 2){STRfinal = '-' + STRfinal;}
			if(i == 6){STRfinal = '/' + STRfinal;}
			if(i == 9){STRfinal = '.' + STRfinal;}
			if(i == 12){STRfinal = '.' + STRfinal;}
			i++;
		}
	}
	componente.value = STRfinal;
}
////====================================================================================
//                      FORMATAÇÃO E VALIDAÇÃO DE CPF/CNPJ
//====================================================================================
function validarTelefone(){}
////====================================================================================
//====================================================================================
$(function(){
	$("ul.dropdown li").hover(function(){
		$(this).addClass("hover");
		$('ul:first',this).css('visibility', 'visible');
	}, function(){
		$(this).removeClass("hover");
		$('ul:first',this).css('visibility', 'hidden');
	});
	$("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
});

$(document).ready( function() {
    $('.cnpj').mask("99.999.999/9999-99",{completed:function(){aposDigitarCnpj($('#' + $(this).attr('id')));}});
    $('.cpf').mask("999.999.999-99",{completed:function(){aposDigitarCpf($('#' + $(this).attr('id')));}});
    $("input:checkbox[readonly]").click( function(){ return false; } );
    
    $("#seletorDePagina").change( function( ) {
    		var c = $.getURLParam("c").split('_');
    		//alert(c[0]);
            window.location = "?c="+c[0]+"_mudarPagina&pagina=" + $(this).val();
    });
    $("#seletorPagina").change( function( ) {
            window.location = "?c="+$.getURLParam("c")+"&pagina=" + $(this).val();
    });
    /*$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
	$(".data").datepicker({ dateFormat: 'dd/mm/yy', showOn: 'both', buttonImage: '.sistema/imagens/calendar.gif', buttonImageOnly: true , yearRange: '-100:+20'}
	)/*.attr("readonly" , "readonly")*/;
	$('textarea').blur(function(){
		if(!$(this).attr('id')) return;
		$(this).val($(this).val().substring(0,parseInt($(this).attr('limite'))));
		$('#textarea_'+$(this).attr('id')).remove();
	});
	$('textarea').focus(function(){
		if(!$(this).attr('id')) return;
		if(!$(this).attr('limite')) $(this).attr('limite',3000);
		$(this).after('<div id="textarea_'+$(this).attr('id')+'">Limite de caracteres <span>'+ $(this).val().length +'/'+$(this).attr('limite')+'</span></div>');
	 });
	$("textarea").live('keypress', function(event){
		if(!$(this).attr('id')) return true;
		if(event.keyCode == 9 || event.keyCode == 8){
			$('#textarea_'+$(this).attr('id')+' span').html(($(this).val().length +1) +'/'+$(this).attr('limite'));
			return true;
		}
		if($(this).val().length > parseInt($(this).attr('limite'))-1) return false;
		$('#textarea_'+$(this).attr('id')+' span').html(($(this).val().length +1) +'/'+$(this).attr('limite'));
		return true;
	});
	$.fn.htmlDialog = function( valor ){
		try{
			eval( "valor = (" + valor + ")" );
			if( valor.tipo ) alert( valor.erro );
			return( false );
		}catch(e){
			$(this).html( valor );
			return( true );
		}
	};

});
function aposDigitarCnpj(){}
function aposDigitarCpf(){}

/* Copyright (c) 2006 Mathias Bank (http://www.mathias-bank.de)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * 
 * Thanks to Hinnerk Ruemenapf - http://hinnerk.ruemenapf.de/ for bug reporting and fixing.
 */
jQuery.extend({
/**
* Returns get parameters.
*
* If the desired param does not exist, null will be returned
*
* @example value = $.getURLParam("paramName");
*/
 getURLParam: function(strParamName){
	  var strReturn = "";
	  var strHref = window.location.href;
	  var bFound=false;
	  
	  var cmpstring = strParamName + "=";
	  var cmplen = cmpstring.length;

	  if ( strHref.indexOf("?") > -1 ){
	    var strQueryString = strHref.substr(strHref.indexOf("?")+1);
	    var aQueryString = strQueryString.split("&");
	    for ( var iParam = 0; iParam < aQueryString.length; iParam++ ){
	      if (aQueryString[iParam].substr(0,cmplen)==cmpstring){
	        var aParam = aQueryString[iParam].split("=");
	        strReturn = aParam[1];
	        bFound=true;
	        break;
	      }
	      
	    }
	  }
	  if (bFound==false) return null;
	  return strReturn;
	}
});