<? if ($_SESSION["id_usuario"]!="") { ?>
	
	<div class="row">
		<div class="span12 painel_destaque well">
			<h3>Olá, <?=primeira_palavra($_SESSION[nome]);?>!</h3>
			<h4>Seu último login foi em <?= formata_data_timestamp($_SESSION["ultimo_login"]); ?></h4>
			
		</div>
	</div>
	
	<? /*
	<div class="row-fluid">
		<div class="span3 well">
			<big><big><big>2</big></big> clientes cadastrados</big>
			
		</div>
		
		<div class="span4 offset1">
			:-)
		</div>
	</div>
	*/ ?>
<? } ?>