<?
require_once("includes/conexao.php");

$result= mysql_query("select * from  usuarios
						where usuarios.id_usuario = '". $_SESSION["id_usuario"] ."'
						limit 1
						") or die(mysql_error());
$rs= mysql_fetch_object($result);	
?>
			
	<? include('includes/arquivos/dados_menu.php'); ?>
	
	<form enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formTema" method="post" name="form">
		
		<div class="page-header">
			<h2>Temas</h2>
		</div>
	    
        <div class="row">
        	<div class="col-md-4">
                <label for="tema">Tema:</label>
				<select class="form-control" id="tema" name="tema">
					<?
					$result_tem= mysql_query("select * from cad_temas
											order by tema asc
											") or die(mysql_error());
					while ($rs_tem= mysql_fetch_object($result_tem)) {
					?>
					<option <? if ( ($rs->tema==$rs_tem->tema) || (($_SESSION[tema]=='') && ($rs_tem->tema=='Normal')) ) echo 'selected="selected"'; ?> value="<?=$rs_tem->tema;?>"><?=$rs_tem->tema;?></option>
					<? } ?>
				</select>
				
        	</div>
			
        </div>   
		<br /><br />
		
	    <div class="form-actions">
	    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
			<a type="button" class="btn btn-default cancelar" href="./?pagina=acesso/dados">Cancelar</a>
	    </div>
		
	</form>
