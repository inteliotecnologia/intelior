<?
require_once("includes/conexao.php");
if (pode("12", $_SESSION["perfil"])) {
	$acao= $_GET["acao"];
	$num=1;
	
	if ($acao=='e') {
		
		$st='Editar';
		
		$result= mysql_query("select * from  usuarios
								where usuarios.id_usuario = '". base64_decode($_GET["id_usuario"]) ."'
								". $str ."
								and   usuarios.status <> '2'
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
	
	<div class="row">
		<div class="col-md-12">
			<div class="page-header">
				<h1>Usuário <small><?=$st;?> usuário</small></h1>
			</div>
			
			<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formUsuario&amp;acao=<?= $acao; ?>" method="post" name="form">
			    
			    <? if ($acao=='e') { ?>
			    <input name="id_usuario" class="escondido" type="hidden" id="id_usuario" value="<?= $rs->id_usuario; ?>" />
			    <? } ?>
			    
		        <div class="row-fluid">
		        	<div class="col-md-4">
		                <label for="nome">Nome<span class="text-error">*</span>:</label>
		                <input class="form-control" type="text" name="nome" id="nome" value="<?= $rs->nome; ?>" placeholder="Nome" required="required" />
						<br />
						
		                <label for="email">E-mail<span class="text-error">*</span>:</label>
		                <input class="form-control" type="email" name="email" id="email" value="<?= $rs->email; ?>" placeholder="E-mail" required="required" />
						<br />
						
						<label for="cpf">CPF:</label>
		                <input class="form-control" type="text" name="cpf" id="cpf" value="<?= $rs->cpf; ?>" placeholder="000.000.000/00" />
		                
		        	</div>
		        	<div class="col-md-4">	
		        		<br/>
		        		<div class="well">
			                <label for="senha">Senha<? if ($acao=='i') { ?><span class="text-error">*</span><? } ?>:</label>
			            	<input class="form-control" type="password" name="senha" id="senha" placeholder="Senha" <? if ($acao=='i') { ?> required="required" <? } ?> />
							<br />
							
			            	<label for="senha2">Confirmação<? if ($acao=='i') { ?><span class="text-error">*</span><? } ?>:</label>
			            	<input class="form-control" data-validation-matches-match="senha"
		data-validation-matches-message="A confirmação não confere" type="password" name="senha2" id="senha2" placeholder="Confirmação de senha" <? if ($acao=='i') { ?> required="required" <? } ?> />
		        		</div>
		            	
		        	</div>
					
					<div class="col-md-4">
						
		            	<label for="perfil">Perfil<span class="text-error">*</span>:</label>
						<select class="form-control" id="perfil" name="perfil" required="required">
							<option value="">- selecione -</option>
							<?
							
							if (pode("2", $_SESSION["perfil"])) {
								$str= " limit 1,2 ";
							}
							else $str= " limit 3 ";
							
							$result_per= mysql_query("select * from  cad_perfis
													order by id_perfil asc
													". $str ."
													") or die(mysql_error());
							while ($rs_per= mysql_fetch_object($result_per)) {
							?>
							<option class="tt" title="<?=$rs_per->descricao;?>" <? if ($rs->perfil==$rs_per->id_perfil) echo 'selected="selected"'; ?> value="<?=$rs_per->id_perfil;?>"><?=$rs_per->perfil;?></option>
							<? } ?>
						</select>
		            	<br /><br />
		            	
	            		<labe>Foto:</labe> <br/ >
	            		<input type="file" name="foto" id="foto" title="Escolher foto" />
						<br /><br />
				
	            		<? if ($rs->foto!="") { ?>
			            <label>Foto atual:</label>
			            <div id="foto_area">
		            		<img class="img-rounded" src="includes/timthumb/timthumb.php?src=<?= $rs->foto; ?>&amp;w=120&amp;h=120&amp;zc=1&amp;q=95" border="0" alt="" />
			            </div>
			            
		            	<br />
			            <a id="foto_usuario_excluir" class="btn btn-xs btn-danger" href="javascript:apagaArquivo('<?=$rs->id_usuario;?>', '<?= $rs->foto; ?>');">Apagar foto</a> <br /><br />
			            
			            <? } else echo ''; ?>
		            	
					</div>
		        </div>   
				<br />
				
			    <div class="form-actions">
			    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
					<a type="button" class="btn cancelar" href="./?pagina=acesso/usuarios">Cancelar</a>
			    </div>
				
			</form>
		</div>
	</div>
	<? } else { ?>
	<div class="col-md-12 single">
		<div class="page-header">
			<h1>Ops! <small>:-(</small></h1>
		</div>
		
		<p>Você não tem acesso a esta página.</p>
		<br />
		
		<a class="btn" href="./?pagina=painel">Voltar à página inicial</a>
	</div>
	<? } ?>
<? } ?>
