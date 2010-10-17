jQuery.fn.extend({
	reverse: function(){
		return this.pushStack(this.get().reverse(), arguments);
	},
	trilha: function(){
		codigo = '';
		$.each($(this).parents('li').reverse(), function(i,li){
			codigo += ','+$(li).index();
		});
		return codigo.substr(1, codigo.length);
	},
	addLi: function(tpl,fn){
		botaoMM = $('<input type="button" value="-" >')
			.toggle(function(){
				$(this).parent().find('ol').hide();
				$(this).val('+');
			},function(){
				$(this).parent().find('ol').show();
				$(this).val('-');
			});
		botaoRem = $('<input type="button" value="X" >')
			.click(function(){$(this).parent().remove();});
		botaoAdd = $('<input type="button" value=">" >')
			.toggle(function(){
				$(this).parent().addUl(/* como passar os parametros aki ??? */);
				$(this).val('<');
			},function(){
				$(this).parent().find('ol').remove();
				$(this).val('>');
			});
		$(this).append($('<li>').append(botaoMM).append(botaoRem).append(botaoAdd).append(tpl));
		fn();
		return $(this);
	},
	addUl: function(options){
		var defaults = {
			tpl:'<span class="nome">' +
					'<input type="text" class="edicao" value="Novo..."/>' +
					'<span class="visual">Novo...</span>' +
				'</span>',
			acaoUl: function(){},
			acaoLi: function(){}
		}
		settings = $.extend(defaults,options);
		botaoAdd = $('<input type="button" value="V" >').click(function(){$(this).parent().addLi(settings.tpl,settings.acaoLi);});
		lista = $('<ol>').append(botaoAdd).addLi(settings.tpl,settings.acaoLi).sortable({connectWith: '.ui-sortable'});
		$(this).append(lista);
		settings.acaoUl();
	}
});

$(document).ready(function(){
/*	$('.tabela2').addUl({
		acaoUl:function(){
			//alert('adicionando lista');
		},
		acaoLi:function(){
			//alert('adicionando item');
		}
	});
*/	$('.visual').live('dblclick',function(){
		$(this).hide().parent().find('.edicao').show().focus();
	});
	$('.edicao').live('blur',function(){
		console.log($(this).trilha());
		$(this).hide().next().show().html($(this).val());
	});
});
