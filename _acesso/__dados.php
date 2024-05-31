<?
require_once("includes/conexao.php");
if (pode("1234", $_SESSION["perfil"])) {
	$result= mysql_query("select * from  usuarios, pessoas
							where usuarios.id_usuario = '". $_SESSION["id_usuario"] ."'
							and   pessoas.id_pessoa = usuarios.id_pessoa
							limit 1
							") or die(mysql_error());
	$rs= mysql_fetch_object($result);	
?>

<script language="javascript" type="text/javascript" src="includes/js/bootstrap.file-input.js"></script>
<script>
	$(document).ready(function() {
		$('input[type=file]').bootstrapFileInput();
		$('.file-inputs').bootstrapFileInput();
	});
</script>

<div class="container">
	<div class="row">
	
		<? if (pode("12", $_SESSION["perfil"])) include('includes/arquivos/dados_menu.php'); ?>
		
		<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formDadosPessoais" method="post" name="form">
			
			<div class="page-header">
				<h2>Dados pessoais</h2>
			</div>
			
		    <? /*<input name="id_usuario" class="escondido" type="hidden" id="id_usuario" value="<?= $rs->id_usuario; ?>" />*/ ?>
		    
	        <div class="row">
	        	<div class="col-md-4">
	                
	                <label for="nome">Nome<span class="text-danger">*</span>:</label>
	                <input class="form-control" type="text" name="nome" id="nome" value="<?= $rs->nome; ?>" placeholder="Nome" required="required" />
					<br />
					
	                <label for="email">E-mail<span class="text-danger">*</span>:</label>
	                <input class="form-control" type="email" name="email" id="email" value="<?= $rs->email; ?>" placeholder="E-mail" required="required" />
					<br />
					
					<label for="cpf">CPF:</label>
	                <input class="form-control" type="text" name="cpf_cnpj" id="cpf_cnpj" value="<?= $rs->cpf_cnpj; ?>" placeholder="" />
	                <br />
	                              
	        	</div>
	        	<div class="col-md-4">
					
					
					<div class="well">
						<label for="senha">Senha:</label>
		            	<input class="form-control" type="password" name="senha" id="senha" placeholder="Caso queira mudar a senha" />
						<br />
						
		            	<label for="senha2">Confirmação:</label>
		            	<input class="form-control" type="password" name="senha2" id="senha2" placeholder="Confirmação da nova senha" />
	            	</div>
	        	</div>
				
				<div class="col-md-4">
					
	            	<label>Foto:</label>
							
					<input type="file" name="foto" id="foto" title="Escolher foto" />
					<br /><br />
							
					<? if ($rs->foto!="") { ?>
					<label>Foto atual:</label>
		            <div id="foto_area">
	            		<img class="img-rounded" src="includes/timthumb/timthumb.php?src=<?= $rs->foto; ?>&amp;w=120&amp;h=120&amp;zc=1&amp;q=95" border="0" alt="" />
		            </div>
		            
	            	<br />
		            <a id="foto_usuario_excluir" class="btn btn-xs btn-danger" href="javascript:apagaArquivo('<?=$rs->id_usuario;?>', '<?= $rs->foto; ?>');">Apagar foto</a> <br /><br />
		            
		            <? } ?>
	            	
				</div>
	        </div>   
			<br />
			
		    <div class="form-actions">
		    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
				<a type="button" class="btn btn-default cancelar" href="./?pagina=painel">Cancelar</a>
		    </div>
			
		</form>
	
	</div>
	
</div>
<? } ?>
