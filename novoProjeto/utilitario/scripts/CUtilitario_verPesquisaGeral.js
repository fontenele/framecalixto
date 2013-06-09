$(document).ready( function() {
	$('.corpo').hide();
	$('.titulo').click(function(){$(this).next().toggle();});
});