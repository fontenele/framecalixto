$(document).ready( function() {
	$('.texto').hide();
	$('.titulo').click(function(){$(this).next().toggle();});
});