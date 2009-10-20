$(document).ready(function(){
	$('#csPessoa')
		.change(function(){
			local = $("#documento").parent();
			$('#documento').remove();
			$(local).append('<input title="CPF/CNPJ" class="numerico" size="27" value="" id="documento" name="documento" tabindex="1"/>');
			if($(this).val().charAt(0) == 'F') {
				$('#documento').mask("999.999.999-99",{completed:function(){}});
			}else{
				$('#documento').mask("99.999.999/9999-99",{completed:function(){}});
			}
		})
		.trigger('change');
});