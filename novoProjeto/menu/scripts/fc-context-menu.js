(function($){
	$.fn.fcContextMenu = function(options){
		var defaults = {
			autoClose: false,
			menuBlock: 'fc-context-menu',
			offsetX : 8,
			offsetY : 8,
			speed : 'slow',
			onShow: function(e){},
			onHide: function(e){}
		};
		var options = $.extend(defaults, options);
		var contextMenu = '.' + options.menuBlock;
		var contextMenuItens = contextMenu+' li';
		$(contextMenu).bind("contextmenu",function(e){return false;});
		$(document).keydown(function(e){
			if(e.keyCode == 27){
				options.onHide(e);
				$(contextMenu).hide(options.speed);
			}
		});
		$(this).parent().mousedown(function(e){
			if(e.button != 2){
				options.onHide(e);
				$(contextMenu).hide(options.speed);
			}
		});
		$(contextMenuItens).click(function(e){
			options.onHide(e);
			$(contextMenu).hide();
		});

		return this.each(function(){
			$(this).bind("contextmenu",function(e){return false;});
			$(this).mousedown(function(e){
				if(e.button == "2"){
					var offsetX = e.pageX  + options.offsetX;
					var offsetY = e.pageY + options.offsetY;
					options.onShow(e);
					$(contextMenu).show(options.speed).css({
						'display':'block',
						'top':offsetY,
						'left':offsetX
					});
				}
			});
			if(options.autoClose){
				$(contextMenu).hover(function(){}, function(e){
					options.onHide(e);
					$(contextMenu).hide(options.speed);
				})
			}
		});
	};
})(jQuery);
