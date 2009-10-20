/**
 * metodo de formatacao de componente
 */
jQuery.fn.formatar = function(configurador){
	jQuery(this)
	.live('change',function(){
		if(configurador.expRegTexto){if(!configurador.expRegTexto.exec(jQuery(this).val())) return false;}
		if(configurador.validarTexto){configurador.validarTexto(this);}
	})
	.live('keypress',function(evento){
		try{
			if(evento.keyCode == 13)return false;	//enter
			if(evento.keyCode == 8)	return true;	//backspace
			if(evento.keyCode == 9)	return true;	//tab
			if(evento.keyCode == 46)return true;	//del
			if(configurador.limite && (configurador.limite == jQuery(this).val().length)) return false;
			if(configurador.expRegChar){if(!configurador.expRegChar.exec(String.fromCharCode(evento.originalEvent.charCode))) return false;}
			if(configurador.validarChar){if(!configurador.validarChar(evento,this))return false;}
			for(i in configurador.separadores){if(i == jQuery(this).val().length)jQuery(this).val(jQuery(this).val()+configurador.separadores[i]);}
		}catch(e){
			alert(e);
		}
	});
	return this;
}
/**
* Configurador de formatação de data
*/
jQuery.fn.formatarData = function (configurador){
	configurador = configurador || new Object();
	configurador.expRegChar = /\d/;
	configurador.limite=10;
	configurador.formato = configurador.formato || 'DDMMYYYY';
	switch (configurador.formato){
		case 'YYYYMMDD': configurador.separadores = {5:'/',7:'/'};break;
		case 'MMDDYYYY': configurador.separadores = {2:'/',5:'/'};break;
		case 'DDMMYYYY': configurador.separadores = {2:'/',5:'/'};break;
	}
	d = new Date();
	configurador.dataAtual = configurador.dataAtual || d.format('d/m/Y');
	configurador.validarTexto = function(componente){
		try{
			if(jQuery(componente).val() == "") return;
			var re = /^[1-9]$/;
			if(re.exec(jQuery(componente).val())) jQuery(componente).val('0' + jQuery(componente).val());
			var arData  = jQuery(componente).val().split('/');
			var dataAtual = configurador.dataAtual.split('/');
			console.log(dataAtual);
			if(arData.length > 3) throw 1;
			switch (configurador.formato){
				case 'YYYYMMDD': form = [2,1,0]; break;
				case 'MMDDYYYY': form = [1,0,2]; break;
				case 'DDMMYYYY': form = [0,1,2]; break;
			}
			inDia = arData[form[0]] ? arData[form[0]] : dataAtual[form[0]];
			inMes = arData[form[1]] ? arData[form[1]] : dataAtual[form[1]];
			inAno = arData[form[2]] ? arData[form[2]] : dataAtual[form[2]];
			console.log('Dia:'+inDia);
			console.log('Mes:'+inMes);
			console.log('Ano:'+inAno);
			reAno = /^(2[0-1]|19)\d{2}$/;
			if(!reAno.exec(inAno)) throw 2;
			reMes = /^(0[1-9]|1[0-2])$/;
			if (!reMes.exec(inMes)) throw 3;
			reDia = /^(0[1-9]|[1-2][0-9])\d{2}|(310[13578])|(311(0|2))|(300[^02])|301(0|1|2)$/;
			if (!reDia.exec(inDia + inMes)) throw 4;
			if ( inMes == 02 ) {
				if ( inDia > 29 ) throw 4;
				if ( !( ( inAno % 4 == 0 ) && ( ( inAno % 100 != 0 ) || ( inAno % 400 == 0 ) ) ) && ( inDia > 28 ) )
					throw 4;
			}
			switch (configurador.formato){
				case 'DDMMYYYY': jQuery(componente).val(inDia + '/' + inMes + '/' + inAno); break;
				case 'MMDDYYYY': jQuery(componente).val(inMes + '/' + inDia + '/' + inAno); break;
				case 'YYYYMMDD': jQuery(componente).val(inAno + '/' + inMes + '/' + inDia); break;
			}
		}
		catch(e){
			switch(e){
				case 1: alert(JS_ERRO_DATA);break;
				case 2: alert($.sprintf(JS_ERRO_ANO, inAno));break;
				case 3: alert($.sprintf(JS_ERRO_MES, inMes));break;
				case 4: alert($.sprintf(JS_ERRO_DIA, inDia));break;
			}
			console.log(e);
			jQuery(componente).focus();
			jQuery(componente).val('');
		}
	}
	jQuery(this).formatar(configurador);
}
/**
* Configurador de formatação de hora
*/
jQuery.fn.formatarHora = function (configurador){
	configurador = configurador || new Object();
	configurador.erro = configurador.erro || function(erro){alert(erro);};
	configurador.separadores = {2:':',5:':'};
	configurador.expRegChar = /\d/;
	configurador.limite=8;
	configurador.validarTexto = function(componente){
		re = /^(([0-1][0-9]|2[0-3])$|([0-1][0-9]|2[0-3]):[0-5][0-9])$|(([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])$/;
		var arHora = jQuery(componente).val().split(':');
		switch(true){
			case (arHora.length == 1): jQuery(componente).val(jQuery(componente).val()+':00:00');break;
			case (arHora.length == 2): jQuery(componente).val(jQuery(componente).val()+':00');break;
			case ((arHora.length > 3) || (!re.exec(jQuery(componente).val()))): configurador.erro('erroHora');break;
		}
	};
	jQuery(this).formatar(configurador);
}
/**
* Configurador de formatação de data
*/
jQuery.fn.formatarCep = function (configurador){
	configurador = configurador || new Object();
	configurador.erro = configurador.erro || function(erro){alert(erro);};
	configurador.separadores = {2:'.',6:'-'}
	configurador.expRegChar = /\d/
	configurador.limite=10;
	jQuery(this).formatar(configurador);
}
/**
* Configurador de formatação de data
*/
jQuery.fn.formatarEmail = function (configurador){
	configurador = configurador || new Object();
	configurador.erro = configurador.erro || function(erro){alert(erro);};
	configurador.separadores = {}
	configurador.limite=256;
	configurador.validarTexto = function(componente){
		re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
		if(!re.exec(jQuery(componente).val())){
			jQuery(componente).val('');
			configurador.error('erroEmail');
		}
	}
	jQuery(this).formatar(configurador);
}
