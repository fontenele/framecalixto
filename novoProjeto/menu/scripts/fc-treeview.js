(function($){
	$.fn.fcTreeView = function(){
		$(this).addClass('fc-treeview');
		$('.hitarea').live('click',function(){
			if($(this).parent().find('.hitarea:first').hasClass('closed-hitarea')){
				$(this).parent().find('ul:first').show();
				$(this).parent().find('.hitarea:first').removeClass('closed-hitarea');
			}else{
				$(this).parent().find('ul:first').hide();
				$(this).parent().find('.hitarea:first').addClass('closed-hitarea');
			}
		});
		console.log($(this).find('li'));
		$(this).find('li>ul').parent().prepend($('<div class="hitarea"></div>'));
		$(this).find('li.closed').find('.hitarea:first').trigger('click');
		return $(this);
	}
	$.fn.fcTreeViewRefresh = function(){
		$(this).removeClass('fc-treeview');
		$(this).addClass('fc-treeview');
	}
})(jQuery);
