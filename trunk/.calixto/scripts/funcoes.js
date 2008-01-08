//====================================================================================
//
//====================================================================================
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

function $(id){	return document.getElementById(id);}
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
		a = re.exec(valor);
		if(!a) alert(JS_ERRO_EMAIL);
	}
	catch(e){alert(e);}
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
		var re = /^2[0-1]\d{2}$/;
		var resAno = re.exec(inAno);
		var re = /^(0[1-9]|[1-2][0-9])\d{2}|(310[13578])|(31(10|12))|(300[2469])|(3011)$/;
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
