<?
require_once("includes/conexao.php");
if ((pode("12", $_SESSION["perfil"])) || ($_GET["lb"]=="1") ) {
	
?>	

<? /*<div class="rodape">
	<img id="logo_proposta2" src="images/intelio.png" width="150" class="pull-right" alt="intelio" />
</div>
*/ ?>

<div class="row cabecalho">
	<div class="col-xs-8 col-md-8 infos">
		<small><strong>intelio tecnologia</strong> <br />
		22.765.412/0001-55 <br/>
		(48) 3207-0827 <br />
		intelio.com.br</small>
	</div>
	<div class="col-xs-4 col-md-4" style="padding-right:0;">
		<img id="logo_proposta" src="images/intelio.png" width="100" class="pull-right" alt="intelio" />
	</div>
</div>
<br /><br />

<div class="row corpo">
	<div class="col-xs-3 col-md-3 corpo_sidebar">
		
		
		<?=desformata_data($rs->data);?>
		<br /><br />
		
		<h5>Para:</h5>
		<big>
		<?
		$nome2= pega_pessoa($rs->id_pessoa, 'nome2');
		$cpf_cnpj= pega_pessoa($rs->id_pessoa, 'cpf_cnpj');
		$contato=pega_pessoa_meta($rs->id_pessoa, 'contato');
		?>
		
		<? if($nome2!='') echo $nome2 .'<br/>';?> 
		<? if($cpf_cnpj!='') echo $cpf_cnpj .'<br/>';?>
		<? if ($contato!='') { ?> A/C <?=$contato?> <br/> <? } ?>
		
		</big>
		
		
		<? if ($rs->valor!='0') { ?>
		<br />
		<h5>Valor:</h5>
		<big>
		R$<?= number_format($rs->valor, 2, ',', '.'); ?>
		</big>
		<? } ?>
		
		<br /><br/><br/>
		
		
		
	</div>
	<div class="col-xs-9 col-md-9 corpo_conteudo">
	
		<h3 style="margin-top:0;">Proposta nº<?=($rs->num).'/'. $rs->ano;?></h3>
		<h4><?=$rs->proposta;?></h4>
		<br />
		
		<?= $rs->descricao; ?>
				
		<br /><br/><br/>
		
		<small>
		Atenciosamente, <br/>
		<strong><?= $_SESSION["nome"]; ?></strong> <br/>
		<?= pega_usuario($_SESSION["id_usuario"], 'cargo'); ?> <br/>
		</small>
		
	</div>
</div>

<script>
	$(document).ready(function() {
		var altura= $('.corpo_conteudo').height();
		$('.corpo_sidebar').css('height', altura+'px');
	});
</script>

<? } ?>
