(function($){
	$.fn.fcTreeViewManager = function(options){
		var target=null;
		var tree = this;
		var defaults = {
			target : null,
			speed : 0,
			onShow: function(e){
				target=e.currentTarget;
			},
			blockCuted:'marcado',
			blockDel:'delete',
			blockCut:'cut',
			blockPaste:'paste',
			blockPasteChildren:'fpaste'
		};
		var options = $.extend(defaults, options);
		var cuted = '.'+options.blockCuted;
		var del = '.'+options.blockDel;
		var cut = '.'+options.blockCut;
		var paste = '.'+options.blockPaste;
		var fpaste = '.'+options.blockPasteChildren;

		$(tree).fcTreeView();
		$(tree).find('span').fcContextMenu(options);
		$(del).click(function(){
			$(tree).find('li').removeClass(options.blockCuted);
			$(target).parent().remove();
		});
		$(cut).click(function(){
			$(tree).find('li').removeClass(options.blockCuted);
			$(target).parent().addClass(options.blockCuted);
		});
		$(paste).click(function(){
			var antes = $(cuted).parent();
			$(target).parent().after($(cuted));
			if(!$(antes).find('li:first')[0]){
				$(antes).siblings('.hitarea').remove();
				$(antes).remove();
			}
			$(tree).find('li').removeClass(options.blockCuted);
			$(tree).fcTreeViewRefresh();
		});
		$(fpaste).click(function(){
			var antes = $(cuted).parent();
			if($(target).parent().find('ul')[0]){
				$(target).parent().find('ul').append($(cuted));
			}else{
				$(target).parent().append('<div class="hitarea">').append($('<ul>')).find('ul').append($(cuted));
			}
			if(!$(antes).find('li:first')[0]){
				$(antes).siblings('.hitarea').remove();
				$(antes).remove();
			}
			$(tree).find('li').removeClass(options.blockCuted);
			$(tree).fcTreeViewRefresh();
		});
		$(document).keydown(function(e){
			if(e.keyCode == 27){
				$(tree).find('li').removeClass(options.blockCuted);
				$(tree).fcTreeViewRefresh();
			}
		});
	}
})(jQuery)