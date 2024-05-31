<?
require_once("includes/conexao.php");
if (pode("12", $_SESSION["perfil"])) {
	$acao= $_GET["acao"];
	$num=1;
	
	if ($acao=='e') {
		
		$st='Editar';
		
		$result= mysql_query("select * from  propostas
								where propostas.id_proposta = '". ($_GET["id_proposta"]) ."'
								". $str ."
								and   propostas.status <> '2'
								") or die(mysql_error());
		$num= mysql_num_rows($result);
		$rs= mysql_fetch_object($result);
	}
	else $st='Nova';
	
	if ($num>0) {
	?>	
	
	<script type="text/javascript">
	
	$(document).ready(function() {
		$("textarea").fadeIn();
	});
	
	tinymce.init({
	    selector: "textarea",
	    theme: "modern",
	    plugins: [
	        "advlist autolink lists charmap hr ",
	        "wordcount ",
	        "nonbreaking table directionality",
	        "paste"
	    ],
	    toolbar1: "undo redo | bold italic underline | bullist numlist outdent indent | table",
	    //toolbar2: " media | forecolor backcolor emoticons",
	    image_advtab: true,
	    language : 'pt_BR',
	    content_css : "./tinymce.css"
	});

	</script>
	
	<div class="row">
		<div class="col-md-12">
			<div class="page-header">
				<h1>Proposta <small><?=$st;?> proposta <? if ($acao=='e') echo "nº". formata_saida($rs->num, 3) ."/". $rs->ano;?></small></h1>
			</div>
			
			<form id="fileupload" enctype="multipart/form-data" action="<?= AJAX_FORM; ?>formProposta&amp;acao=<?= $acao; ?>" method="post" name="form">
			    
			    <? if ($acao=='e') { ?>
			    <input name="id_proposta" class="escondido" type="hidden" id="id_proposta" value="<?= $rs->id_proposta; ?>" />
			    <input name="hash" class="escondido" type="hidden" id="id_proposta" value="<?= $rs->hash; ?>" />
			    <? } ?>
			    
		        <div class="row">
		        	<div class="col-md-5">
		                <label for="nome">Tipo<span class="text-danger">*</span>:</label>
		                <br />
		                
		                <select class="form-control" id="tipo_nota" name="tipo_nota" required="required">
							<option value="">- selecione -</option>
							<option value="1" <? if ( ($acao=='i') || ($rs->tipo_proposta=='1') ) echo "selected='selected'"; ?>>Orçamento</option>
							<option value="2" <? if ($rs->tipo_proposta=='2') echo "selected='selected'"; ?>>Contratação</option>
		                </select>
		                <br />
						
						<label for="perfil">Quem<span class="text-danger">*</span>:</label>
						<div id="pessoa_area">
							
						</div>
						<br />
						
						<script type="text/javascript">
							carregaPessoa('<?=$rs->id_pessoa;?>');
						</script>
						
						<label for="proposta">Proposta<span class="text-danger">*</span>:</label>
		                <input class="form-control" type="text" name="proposta" id="proposta" value="<?= $rs->proposta; ?>" placeholder="Proposta" required="required" />
						<br />
		        		
		        		<div class="row">
			        		<div class="col-md-6">
				        		<label for="data">Data:</label>
				        		<?
				        		if ($acao=='i') $data= date("d/m/Y");
				        		else $data= desformata_data($rs->data);
				        		?>
				                <input class="form-control" type="text" name="data" id="data" value="<?= $data; ?>" placeholder="00/00/0000" />
			        		</div>
			        		<div class="col-md-6">
			        			<label for="hora">Hora:</label>
				        		<?
				        		if ($acao=='i') $hora= date("H:i");
				        		else $hora= ($rs->hora);
				        		?>
				                <input class="form-control" type="text" name="hora" id="hora" value="<?= $hora; ?>" placeholder="00:00" />
			        		</div>
		        		</div>
		                <br />
		                
		        		<label for="valor">Valor:</label>
		                <input class="form-control" type="text" name="valor" id="valor" value="<?= fnum($rs->valor); ?>" placeholder="R$0,00" />
		                <br />
		                
		                <label for="valido_ate">Válida até:</label>
		                <input class="form-control" type="text" name="valido_ate" id="valido_ate" value="<?= desformata_data($rs->valido_ate); ?>" placeholder="00/00/0000" />
		                <br />
						
		        	</div>
		        	<div class="col-md-7">	
		        		
		        		<div class="well">
			                <label for="descricao">Detalhamento:</label>
			                <br />
			                
			            	<textarea style="display:none;" rows="22" class="form-control" name="descricao" id="descricao" placeholder="Descrição"><?=$rs->descricao;?></textarea>
							<br />
		        		</div>
		                
		        	</div>
					
		        </div> 
		        <br/><br/>
		        
		        <?
		        if ($acao=='e') {								
					$result_a= mysql_query("select * from arquivos
											where id = '". $rs->id_proposta ."'
											and   tabela = 'propostas'
											and   status <> '2'
											") or die(mysql_error());
					
					$num_a= mysql_num_rows($result_a);
					
					if ($num_a>0) {
				?>
				
		        <div class="row">
		        	<div class="col-md-12">
			        	<fieldset>
							<legend>Anexos (<?=$num_a;?>)</legend>

							<table class="table table-condensed">
								<thead>
									<tr>
										<th width="70%">Arquivo</th>
										<th width="30%">Tamanho</th>
									</tr>
								</thead>
								<tbody>
								<?
								while ($rs_a= mysql_fetch_object($result_a)) {
								?>
									<tr>
										<td><a href="<?=CAMINHO;?>anexos/propostas/<?=$rs->hash;?>/<?=$rs_a->arquivo;?>" target="_blank"><small><?= $rs_a->arquivo; ?></small></a></td>
										<td><small><?= format_bytes($rs_a->tamanho); ?></small></td>
									</tr>
								<? } ?>
								<tbody>
							</table>
									

			        	</fieldset>
		        	</div>
		        </div>
		        <br />
		        <? } } ?>
		        
		        <div class="row">
		        	<div class="col-md-12">
			        	<fieldset>
							<legend>Anexar arquivos</legend>
							
							<br />
							
							<?
							limpa_pasta_temp(session_id());
							?>
							
							<!-- Redirect browsers with JavaScript disabled to the origin page -->
					        <noscript><input type="hidden" name="redirect" value="http://blueimp.github.io/jQuery-File-Upload/"></noscript>
					        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
					        <div class="row-fluid fileupload-buttonbar">
					            <div class="span12">
					                <!-- The fileinput-button span is used to style the file input field as button -->
					                <span class="btn btn-xs btn-success fileinput-button">
					                    <i class="glyphicon glyphicon-plus glyphicon-white"></i>
					                    <span>Adicionar arquivos...</span>
					                    <input type="file" name="files[]" multiple />
					                </span>
					                <button type="submit" class="btn btn-xs btn-primary start">
					                    <i class="glyphicon glyphicon-upload glyphicon-white"></i>
					                    <span>Começar upload</span>
					                </button>
					                <button type="reset" class="btn btn-xs btn-warning cancel">
					                    <i class="glyphicon glyphicon-ban-circle glyphicon-white"></i>
					                    <span>Cancelar upload</span>
					                </button>
					                <button type="button" class="btn btn-xs btn-danger delete delete2">
					                    <i class="glyphicon glyphicon-trash glyphicon-white"></i>
					                    <span>Apagar arquivos carregados</span>
					                </button>
					                <input type="checkbox" class="toggle checkbox_delete_toggle">
					                <!-- The loading indicator is shown during file processing -->
					                <span class="fileupload-loading"></span>
					            </div>
					            <!-- The global progress information -->
					            <div class="span12 fileupload-progress fade">
					                <!-- The global progress bar -->
					                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					                    <div class="bar" style="width:0%;"></div>
					                </div>
					                <!-- The extended global progress information -->
					                <div class="progress-extended">&nbsp;</div>
					            </div>
					        </div>
					        <!-- The table listing the files available for upload/download -->
					        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
							
						</fieldset>
		        	</div>
		        </div>
		        
				<br />
				
			    <div class="form-actions">
			    	<button class="btn btn-primary" type="submit" data-loading-text="Salvando...">Salvar</button>
					<a type="button" class="btn btn-default cancelar" href="./?pagina=acesso/propostas">Cancelar</a>
			    </div>
				
			</form>
		</div>
	</div>
	
	
	
	<!-- The blueimp Gallery widget
	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
	    <div class="slides"></div>
	    <h3 class="title"></h3>
	    <a class="prev">‹</a>
	    <a class="next">›</a>
	    <a class="close">×</a>
	    <a class="play-pause"></a>
	    <ol class="indicator"></ol>
	</div> -->
	
	<!-- The template to display files available for upload -->
	<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
		
		<tr class="template-upload fade">
	        <td>
	            
	            <span class="preview"></span>
	        </td>
	        <td>
	            <p class="name">{%=file.name%}</p>
	            {% if (file.error) { %}
	                <div><span class="label label-important">Erro</span> {%=file.error%}</div>
	            {% } %}
	        </td>
	        <td>
	            <p class="size">{%=o.formatFileSize(file.size)%}</p>
	            {% if (!o.files.error) { %}
	                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
	            {% } %}
	        </td>
	        <td>
	            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
	                <button class="btn btn-primary start">
	                    <i class="glyphicon glyphicon-upload glyphicon-white"></i>
	                    <span>Iniciar upload</span>
	                </button>
	            {% } %}
	            {% if (!i) { %}
	                <button class="btn btn-warning cancel">
	                    <i class="glyphicon glyphicon-ban-circle glyphicon-white"></i>
	                    <span>Cancelar</span>
	                </button>
	            {% } %}
	        </td>
	    </tr>
	{% } %}
	</script>
	<!-- The template to display files available for download -->
	<script id="template-download" type="text/x-tmpl">
	{% if (o.files[i]>0) { %}
	<tr>
		<th>&nbsp;</th>
		<th>Arquivo</th>
		<th>Tamanho</th>
		<th>Ações</th>
	</tr>
	{% } %}
	
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	    {% if (!file.error) { %}
	    <tr class="template-download fade">
	        <td>
	            <input type="hidden" name="arquivo[]" value="{%=file.name%}" />
				<input type="hidden" name="tamanho[]" value="{%=file.size%}" />
				<input type="hidden" name="tipo[]" value="{%=file.type%}" />
				<input type="hidden" name="url[]" value="{%=file.url%}" />
	            
	            <span class="preview">
	                {% if (file.thumbnailUrl) { %}
	                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
	                {% } %}
	            </span>
	        </td>
	        <td>
	            
	            {% if (file.error) { %}
	                {%=file.name%} <br />
	                <div><span class="label label-important">Erro</span> {%=file.error%}</div>
	            {% } else { %}
	            <p class="name">
	                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
	            </p>
	            {% } %}
	        </td>
	        <td>
	            <span class="size">{%=o.formatFileSize(file.size)%}</span>
	        </td>
	        <td>
	            <button class="btn btn-xs btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
	                <i class="glyphicon glyphicon-trash glyphicon-white"></i>
	                <span>Apagar</span>
	            </button>
	            <input type="checkbox" name="delete" value="1" class="toggle">
	        </td>
	    </tr>
	    
	    {% } %}
	{% } %}
	</script>
	
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<!-- The Templates plugin is included to render the upload/download listings -->
	<script src="includes/jquery-file-upload/js/tmpl.js"></script>
	<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
	<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>
	<!-- The Canvas to Blob plugin is included for image resizing functionality -->
	<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
	<!-- blueimp Gallery script -->
	<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
	<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
	<script src="includes/jquery-file-upload-2/js/jquery.iframe-transport.js"></script>
	<!-- The basic File Upload plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload.js"></script>
	<!-- The File Upload processing plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-process.js"></script>
	<!-- The File Upload image preview & resize plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-image.js"></script>
	<!-- The File Upload audio preview plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-audio.js"></script>
	<!-- The File Upload video preview plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-video.js"></script>
	<!-- The File Upload validation plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-validate.js"></script>
	<!-- The File Upload user interface plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-ui.js"></script>
	<!-- The File Upload jQuery UI plugin -->
	<script src="includes/jquery-file-upload-2/js/jquery.fileupload-jquery-ui.js"></script>
	<!-- The main application script -->
	<script src="includes/jquery-file-upload-2/js/main.js"></script>
	
	<? /*
	
	
	<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
	<script src="includes/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
	<!-- The Templates plugin is included to render the upload/download listings -->
	<script src="includes/jquery-file-upload/js/tmpl.js"></script>
	<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
	<script src="includes/jquery-file-upload/js/load-image.min.js"></script>
	<!-- The Canvas to Blob plugin is included for image resizing functionality -->
	<script src="includes/jquery-file-upload/js/canvas-to-blob.min.js"></script>
	
	<!-- blueimp Gallery script -->
	<script src="includes/jquery-file-upload/js/jquery.blueimp-gallery.min.js"></script>
	<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
	<script src="includes/jquery-file-upload/js/jquery.iframe-transport.js"></script>
	<!-- The basic File Upload plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload.js"></script>
	<!-- The File Upload processing plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-process.js"></script>
	<!-- The File Upload image preview & resize plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-image.js"></script>
	<!-- The File Upload audio preview plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
	<!-- The File Upload video preview plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-video.js"></script>
	<!-- The File Upload validation plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
	<!-- The File Upload user interface plugin -->
	<script src="includes/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
	<!-- The main application script -->
	<script src="includes/jquery-file-upload/js/main.js"></script>
	<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
	<!--[if (gte IE 8)&(lt IE 10)]>
	<script src="includes/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>
	<![endif]-->
	*/ ?>
	
	<? } else { ?>
	<div class="row">
		<div class="col-md-12 single">
			<div class="page-header">
				<h1>Ops! <small>:-(</small></h1>
			</div>
			
			<p>Você não tem acesso a esta página.</p>
			<br />
			
			<a class="btn" href="./?pagina=painel">Voltar à página inicial</a>
		</div>
	</div>
	<? } ?>
<? } ?>
