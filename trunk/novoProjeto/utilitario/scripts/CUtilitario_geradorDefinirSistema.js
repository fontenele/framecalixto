$(document).ready(function(){
	$('#tabs').tabs();
	$('#novaConexao').click(function(){
		$('#conexoes').append($('#conexoes>.d-tr:last').clone());
	});
});