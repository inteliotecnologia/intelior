$(document).ready(function() {
	
	//$('.alert').alert();
	$('.alert.esconde').delay(8500).fadeOut(750);
	
	$('.tt').tooltip();
	
	//$(".texto a:not(target=_blank):not(.btn)").attr("target", "_blank");
	
	$('.nav-header').click(function () {
		$(this).parent().parent().children(':not(li:first-child)').toggle(100);
	});
	
	$(document).on('click', '.btn-imprimir', function(event){
		window.print();
		
		var num= $(this).attr('data-num');
		var id_emissao= $(this).attr('data-id_emissao');
		
		$.get('link.php', {chamada: 'salvaLog', area: 'memorandos', acao: 'Imprime memorando '+num, id_emissao: id_emissao },
			
		function(retorno) {
			
		});
	});


	$(document).on('click', '.feed_link', function(event){
		$('.chat_frame').fadeIn();
	});
	
	$(document).on('click', '.feed_link_o', function(event){
		$('.chat_frame').fadeOut();
	});

	
	$(document).on('change', '#tipo_nota', function(e){
		carregaPessoa('0');
	});
	
	$(document).on('submit', '.confirma', function(e){
		if ($('.confirma').hasClass('pode')) {
			//$('.confirma').submit();
			return true;
		}
		else {
			e.preventDefault();
			$('#modal_confirma').modal('show');
		}
	});
	
	$('#sim').click(function() {
		$('.confirma').addClass('pode');
	    $('.confirma').submit();
	    //btn.button('loading');
	    
	    $('button:submit, #sim').button('loading');
	    $('.cancelar').remove();
	});
	
	$('#nao').click(function() {
	    $('.confirma').removeClass('pode');
	    $('button:submit, #sim').button('reset');
	});
	
	$('form').not('.memo').submit(function () {
        var btn = $('button:submit');
        btn.button('loading');
        $('.cancelar').remove();
        //setTimeout(function () {
        //    btn.button('reset');
        //}, 6000);
    });
	
});

$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = this;
    });
}

$(['images/loading.gif']).preload();

function carregaPessoa(id_pessoa) {
	
	var tipo_pessoa= $("#tipo_nota").val();
	
	$("#pessoa_area").html("<img src='images/loading.gif' alt='' />");
	
	$.get('link.php', {chamada: "carregaPessoa", tipo_pessoa: tipo_pessoa, id_pessoa: id_pessoa },
	
	function(retorno) {
		$("#pessoa_area").html(retorno);
	});

}

function carregaConteudo(rotina, id, num, id_pc) {
	$(id).html("<img src='images/loading.gif' alt='' />");
	
	$.get('link.php', {chamada: rotina, id: id, num: num, id_pc: id_pc },
	
	function(retorno) {
		$(id).html(retorno);
		
		/*$.getScript("js/functions.js").done(function(script, textStatus) {
			console.log( textStatus );
		})
		.fail(function(jqxhr, settings, exception) {
			//$( "div.log" ).text( "Triggered ajaxError handler." );
		});*/
	});

}

function apagaLinha(chamada, id, tipo) {
	$.get('link.php', {chamada: chamada, id: id, tipo: tipo },
	
	function(retorno) {
		if (retorno=="0") {
			
			$("#linha_"+id).addClass("warning");
			$("#linha_"+id).fadeOut("fast");
			
		}
		else bootbox.alert("Não foi possível completar a operação! "+ retorno, function() { });
	});

}

function apagaArquivo(id_usuario, src) {

	$.get('link.php', {chamada: "arquivoExcluir", src: src, id_usuario: id_usuario },

	function(retorno) {
		if (retorno!="0") bootbox.alert("Não foi possível excluir o arquivo!", function() { });
		else {
			$("#foto_area").html("Foto excluída.");
		}
	});
}

function inverte_1_0(num) {
	if (num=="1") return(0);
	else return(1);
}

function situacaoLinha(chamada, id, status, tipo) {
	$.get('link.php', {chamada: chamada, id: id, status: status, tipo: tipo },
	
	function(retorno) {
		if (retorno=="0") {
			bootbox.alert("Situação alterada com sucesso!", function() { });
			
			$("#situacao_link_"+id).attr("src", "images/ico_"+inverte_1_0(status)+".png");
		}
		else bootbox.alert("Não foi possível alterar a situação!", function() { });
	});

}

function formata_saida(valor, tamanho_saida) {
	valor+="";
	var tamanho= valor.length;
	var saida="";
	
	for (var i=tamanho; i<tamanho_saida; i++)
		saida+='0';
	
	return(saida+valor);
}
