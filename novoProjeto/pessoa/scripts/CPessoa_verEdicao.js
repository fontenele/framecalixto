$(document).ready(function(){
	$('#csPessoa')
		.change(function(){
			local = $("#nrDocumento").parent();
			$('#nrDocumento').remove();
			$(local).append('<input title="CPF/CNPJ" class="numerico" size="27" value="" id="nrDocumento" name="nrDocumento" tabindex="1"/>');
			if($(this).val().charAt(0) == 'F') {
				$('#nrDocumento').mask("999.999.999-99",{completed:function(){}});
			}else{
				$('#nrDocumento').mask("99.999.999/9999-99",{completed:function(){}});
			}
		})
		.trigger('change');
});