$(document).ready(function(){
	$('.menu1')
		.before('<a href="menuContent.html" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="botaoMenuPrincipal" accesskey="m" ><span class="ui-icon ui-icon-triangle-1-s"></span>Menu Principal</a>');
	menu1 = $('.menu1').html();
	$('.menu1').html('&nbsp;');
	$('#botaoMenuPrincipal').menu({
		content: menu1,
		flyOut: true, // flyout ou ipod menu
		backLink: true,
		trigger: "click"
	});
});