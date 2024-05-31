<?
require_once("includes/conexao.php");
if (pode("12", $_SESSION["perfil"])) {
	$acao= $_GET["acao"];
	$num=1;
	
	if ($acao=='e') {
		
		$st='Editar';
		
		$result= mysql_query("select * from  fornecedores, pessoas
								where fornecedores.id_fornecedor = '". $_GET["id_fornecedor"] ."'
								and   pessoas.id_pessoa = fornecedores.id_pessoa
								". $str ."
								and   pessoas.status <> '2'
								") or die(mysql_error());
		$num= mysql_num_rows($result);
		$rs= mysql_fetch_object($result);
	}
	else $st='Novo';
	
	if ($num>0) {
	?>	
	
	<script language="javascript" type="text/javascript" src="includes/js/bootstrap.file-input.js"></script>
	<script>
		$(document).ready(function() {
			$('input[type=file]').bootstrapFileInput();
			$('.file-inputs').bootstrapFileInput();
		});
	</script>
	
	<div class="col-md-12">
		<div class="page-header">
			<h1>Fornecedor <small><?=$st;?> fornecedor</small></h1>
		</div>
		
		<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formFornecedor&amp;acao=<?= $acao; ?>" method="post" name="form">
		    
		    <? if ($acao=='e') { ?>
		    <input name="id_fornecedor" class="escondido" type="hidden" id="id_fornecedor" value="<?= $rs->id_fornecedor; ?>" />
		    <input name="id_pessoa" class="escondido" type="hidden" id="id_pessoa" value="<?= $rs->id_pessoa; ?>" />
		    <? } ?>
		    
	        <div class="row">
	        	<div class="col-md-5">
	                <label for="nome">Nome<span class="text-danger">*</span>:</label>
                	<input type="text" name="nome" id="nome" class="form-control"  value="<?= $rs->nome; ?>" placeholder="Nome" required="required" />
					<br />
					
					<label for="nome">Razão social/Nome completo:</label>
                	<input type="text" name="nome2" id="nome2" class="form-control"  value="<?= $rs->nome2; ?>" placeholder="Nome" required="required" />
					<br />
					
					<label for="cpf_cnpj">CPF/CNPJ:</label>
	                <input type="text" name="cpf_cnpj" id="cpf_cnpj" value="<?= $rs->cpf_cnpj; ?>" class="form-control" placeholder="CPF/CNPJ" />
	                <br />
	                
	                <label for="email">E-mail:</label>
	                <input type="email" name="email" id="email" class="form-control"  value="<?= $rs->email; ?>" placeholder="E-mail" required="required" />
					<br />
					
					<label for="tags">Tags:</label>
	                <input type="text" name="tags" id="tags" value="<?= $rs->tags; ?>" class="form-control" placeholder="Tags" />
	                <br />
	                
	        	</div>
	        	<div class="col-md-offset-1 col-md-5">	
	        		<br/>
	            	<?
	            	$i=3;
	            	
	            	$campo= pega_pessoa_meta_campos('l');
	            	
	            	while ($campo[$i][0]) {
	            		if ($acao=='e') {
		            		$result_meta= mysql_query("select * from pessoas_meta
			            								where id_pessoa = '". $rs->id_pessoa ."'
			            								and   meta = '". $campo[$i][0] ."'
			            								");
			            	$rs_meta= mysql_fetch_object($result_meta);
		            	}
	            	?>
	            	<div class="row">
		            	<div class="col-md-5">
		            		<input type="hidden" name="meta[]" id="meta" value="<?= $campo[$i][0]; ?>" />
		            		
		            		<label for="valor_<?=$i;?>"><?= $campo[$i][1]; ?>:</label>
		            	</div>
		            	<div class="col-md-7">
		            		<? if ($i==6) { ?>
		            		<select name="valor[]" id="valor_<?=$i;?>" class="form-control">
		            			<option value="">- selecione -</option>
		            			<?
		            			$result_cidade= mysql_query("select cidades.id_cidade, cidades.cidade, ufs.uf from cidades, ufs
																where cidades.id_uf = ufs.id_uf
																order by cidade asc
																");
								while ($rs_cidade= mysql_fetch_object($result_cidade)) {
		            			?>
		            			<option value="<?=$rs_cidade->id_cidade;?>" <? if ($rs_cidade->id_cidade==$rs_meta->valor) echo 'selected="selected"'; ?>><?=$rs_cidade->cidade .'/'. $rs_cidade->uf; ?></option>
		            			<? } ?>
		            		</select>
		            		<? } else { ?>
		            		<input type="text" name="valor[]" id="valor_<?=$i;?>" class="form-control" value="<?= $rs_meta->valor; ?>" />
		            		<? } ?>
		            		<br />
		            	</div>
	            	</div>
	            	<? $i++; } ?>
	            	
	        	</div>
				
	        </div>   
			<br />
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
				<a type="button" class="btn btn-default cancelar" href="./?pagina=acesso/fornecedores">Cancelar</a>
		    </div>
			
		</form>
	</div>
	<? } else { ?>
	<div class="row">
		<div class="col-md-12 single">
			<div class="page-header">
				<h1>Ops! <small>:-(</small></h1>
			</div>
			
			<p>Você não tem acesso a esta página.</p>
			<br />
			
			<a class="btn btn-default" href="./?pagina=painel">Voltar à página inicial</a>
		</div>
	</div>
	<? } ?>
<? } ?>
