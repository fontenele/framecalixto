$(document).ready( function() {
	$('.fc-campo-grupo:not(:first)').hide();
	$('.fc-titulo-especifico').click(function(){$(this).next().toggle();});
});