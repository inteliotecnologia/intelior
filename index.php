<?
require_once("includes/conexao.php");
require_once("includes/funcoes.php");

if (!isset($_GET["pagina"])) $pagina= "painel";
else $pagina= $_GET["pagina"];

session_start();

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./?pagina=home';
	</script>
	";
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		
		<?php
		$titulo_pagina= SISTEMA_NOME_COMPLETO;
		?>
		
		<title><?= $titulo_pagina; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		
		<? /*
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	    */ ?>
	    
		<?
		if (($_SESSION[tema]!='') && ($_SESSION[tema]!='Normal'))
			$tema_css= '_'. strtolower($_SESSION[tema]);
		elseif (($_COOKIE[tema]!='') && ($_COOKIE[tema]!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE[tema]);
			
			$tema_css='-flatly';
		?>
		<link media="screen,print" href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		
		<? /*<link href="includes/bootstrap-wysihtml5/lib/css/prettify.css" rel="stylesheet" />
		<link href="includes/bootstrap-wysihtml5/src/bootstrap-wysihtml5.css" rel="stylesheet" />*/ ?>
		
		<link rel="stylesheet" href="includes/jquery-file-upload/css/jquery.fileupload-ui.css">
		
		<link media="screen" href="style.css" rel="stylesheet" />
		<link media="print" href="style_print.css" rel="stylesheet" />
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery-1.10.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/bootbox.min.js"></script>
		
		<? if ($pagina!='acesso/nota') { ?>
		<script language="javascript" type="text/javascript" src="includes/js/bootstrap.file-input.js"></script>
		<script>
			$(document).ready(function() {
				$('input[type=file]').bootstrapFileInput();
				$('.file-inputs').bootstrapFileInput();
			});
		</script>
		<? } ?>
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="includes/bootstrap/js/html5shiv.js"></script>
		<![endif]-->
		
		<!--[if lt IE 7]>
		<link rel="stylesheet" href="http://blueimp.github.io/cdn/css/bootstrap-ie6.min.css">
		<![endif]-->
		
		<link rel="shortcut icon" href="images/32_.png" />
		
		<script type="text/javascript" src="includes/tinymce/tinymce.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/js/functions.js"></script>
	
	</head>
	<body class="pg_<?= str_replace("/", "-", $pagina); ?>">
		
		<div class="chat_frame" style="display:none;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <a style="color: #fff !important;" class="feed_link_o" href="javascript:void(0);"><i class="glyphicon glyphicon-comment"></i> &nbsp;Feed de atividade</a>
                    
                    <? /*<div class="btn-group pull-right">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </button>
                        <ul class="dropdown-menu slidedown">
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-refresh">
                            </span>Refresh</a></li>
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-ok-sign">
                            </span>Available</a></li>
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-remove">
                            </span>Busy</a></li>
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-time"></span>
                                Away</a></li>
                            <li class="divider"></li>
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-off"></span>
                                Sign Out</a></li>
                        </ul>
                    </div>*/ ?>
                </div>
                <div class="panel-body">
                    <ul class="chat">
                        <li class="left clearfix">
                        	<span class="chat-img pull-left">
                            	<img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
							</span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font">Masud</strong>
                                    <small class="pull-right text-muted">
                                        <span class="glyphicon glyphicon-time"></span>
                                        12 mins ago
                                    </small>
                                </div>
                                <p>
                                    <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                    dolor, quis ullamcorper ligula sodales.</small>
                                </p>
                            </div>
                        </li>
                        
                    </ul>
                </div>
                <? /*<div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Send</button>
                        </span>
                    </div>
                </div>*/ ?>
            </div>
	        </div>
		</div>
	
		<div class="navbar navbar-inverse hidden-print" id="topo" style="margin-bottom:0;">
			
			<div class="navbar navbar-default navbar-fixed-top">
				<div class="container">
				  	
					
					<div class="navbar-header">
						<a class="navbar-brand" href="./?pagina=painel"><?=SISTEMA_NOME;?></a>
						
						<a class="btn navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
					</div>
					
				  	
					
				    <div class="navbar-collapse collapse">
				    	<ul class="nav navbar-nav">
							<li class="divider-vertical"></li>
							<? if ($_SESSION[id_usuario]!="") { ?>
							<li><a href="./?pagina=painel"><i class="glyphicon glyphicon-white glyphicon-eye-open"></i> Painel</a></li>
							
							<li><a href="./?pagina=acesso/propostas"><i class="glyphicon glyphicon-white glyphicon-file"></i> Propostas</a></li>
							
							<li><a href="./?pagina=acesso/caixa"><i class="glyphicon glyphicon-white glyphicon-inbox"></i> &nbsp;Caixa</a></li>
							
							<? /*<li><a class="feed_link" href="javascript:void(0);"><i class="glyphicon glyphicon-white glyphicon-comment"></i> &nbsp;Feed</a></li>*/ ?>
							
							<?
							if (pode("12", $_SESSION["perfil"])) {
								$txt_adm="Cadastros";
							?>
							
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-white glyphicon-arrow-down"></i> <?=$txt_adm;?> <b class="caret"></b></a>
								
								<ul class="dropdown-menu">
									
									<li><a href="./?pagina=acesso/clientes"><i class="glyphicon glyphicon-white glyphicon-briefcase"></i> &nbsp;Clientes</a></li>
									<li><a href="./?pagina=acesso/fornecedores"><i class="glyphicon glyphicon-white glyphicon-wrench"></i> &nbsp;Fornecedores</a></li>
									
									<li class="divider"></li>
									
									<li><a href="./?pagina=acesso/carteiras"><i class="glyphicon glyphicon-white glyphicon-download-alt"></i> Carteiras</a></li>
									
									
									
									<? if (pode("1", $_SESSION["perfil"])) { ?>
									<li class="divider"></li>
																		
									<li><a href="./?pagina=acesso/usuarios"><i class="glyphicon glyphicon-white glyphicon-user"></i> Usuários</a></li>
									
									<li class="divider"></li>
									<li><a href="./?pagina=acesso/acessos"><i class="glyphicon glyphicon-white glyphicon-road"></i> &nbsp;Acessos</a></li>
									<li><a href="./?pagina=acesso/log"><i class="glyphicon glyphicon-white glyphicon-search"></i> Logs</a></li>
									<? } ?>
								</ul>
							</li>
							
							<? } ?>
							
							<? } /*else { ?>
							<li><a href="./?pagina=home"><i class="icon-home icon-white"></i> Home</a></li>
							<? } */ ?>
						</ul>
												
						<div class="pull-right">
							<ul class="nav navbar-nav pull-right">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle atv" data-toggle="dropdown">
										
										<? if ($_SESSION[foto]!='') { ?>
										<img class="img-rounded fotinho" src="includes/timthumb/timthumb.php?src=<?= $_SESSION[foto]; ?>&amp;w=20&amp;h=20&amp;zc=1&amp;q=95" border="0" alt="" />
										<? } ?>
										
										<?=primeira_palavra($_SESSION[nome]);?> <b class="caret"></b>
									</a>
								
									<ul class="dropdown-menu">
										<li><div class="menu_perfil"><small><strong>Perfil:</strong> <?=pega_perfil_resumido($_SESSION[perfil]);?></small></div></li>
										<li class="divider"></li>
										<li><a href="./?pagina=acesso/dados"><i class="glyphicon glyphicon-white glyphicon-cog"></i> Minha conta</a></li>
										<li class="divider"></li>
										<li><a href="./index2.php?pagina=logout"><i class="glyphicon glyphicon-white glyphicon-off"></i> Sair</a></li>
									</ul>
								</li>
							</ul>
						</div>
						
				    </div>    
				</div>
			</div>
			
	    </div><!--/.navbar -->
		
		<?
		if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
			if (($_GET[erro]=='t') ) $dv_cl='alert-warning';
			elseif (($_GET[erro]=='i') || ($_GET[erro]=='j') || ($_GET[erro]=='l') || ($_GET[erro]=='n1') || ($_GET[erro]=='n2') ) $dv_cl= 'alert-error';
			else $dv_cl='alert-success';
			
			if ($_GET[id_emissao]!='') $dv_cl.=' alert-block';
		?>
		<div class="container hidden-print">
			<div class="row">
				<div class="alert <? if ($_GET[esconde]!='nao') echo 'esconde'; ?> <?=$dv_cl;?>">
					<a class="close" data-dismiss="alert" href="#">&times;</a>
					
					<?
		            if ($_GET["erro"]=='j') echo "<h4 class='alert-heading'>Seu acesso está inativo</h4><p>Contate um administrador do sistema.</p>";
					if ($_GET["erro"]=='l') echo "<h4 class='alert-heading'>E-mail e/ou senha inválidos!</h4><p>Tente novamente, você pode ter digitado seus dados de acesso incorretamente.</p>";
					if ($_GET["erro"]=='t') echo "<h4 class='alert-heading'>Você saiu do sistema.</h4><p>Acesso encerrado, para acessar novamente, faça o login.</p>";
					if ($_GET["erro"]=='n1') echo "<h4 class='alert-heading'>Não encontrado!</h4><p>O memorando <strong>e-". $_GET[nm] ."</strong> não existe.</p>";
					if ($_GET["erro"]=='n2') echo "<h4 class='alert-heading'>Sem permissão para acesso!</h4><p>Você não tem permissão para acessar o memorando <strong>e-". $_GET[nm] ."</strong> porque não é do seu setor.</p>";
					
					if ( ($_GET[hash]!='') && ($_GET[caixa]=='saida') ) {
						
						 //<!--<a href='./?pagina=doc/ver&hash=". $_GET[hash] ."' class='btn btn-success btn-large pull-right'>Ver memorando ". formata_saida($_GET[num], 3) ."/". $_GET[ano] ."</a>-->
						 echo "<h4 class='alert-heading'>Solicitação concluída com sucesso.</h4>";
						 echo "<p>O memorando foi enviado.</p>";
						 
					}
					else {
						if ($_GET[erros]==='0') echo "<h4 class='alert-heading'>Solicitação concluída com sucesso.</h4>";
						elseif ($_GET[erros]>0) echo "<h4 class='alert-heading'>Não foi possível cadastrar. Por favor, tente novamente.</h4>";
					}
					?>
				</div>
			</div>
		</div>
		<? } ?>
		
		<div class="container container_inner">
			<div class="row">
				<?php
				$paginar= $pagina;
				if (strpos($paginar, "/")) {
					$parte_pagina= explode("/", $paginar);
					
					if (file_exists("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php")) {
						
						//logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, 0, 'navega', 'Abre página', $pagina, $str_log_oculto, '', '', '', '');
						
						include("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php");
					}
					else {
						logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 0, 0, 'navega', '404', $pagina, $str_log_oculto, '', '', '', '');
						
						include("404.php");
					}
				}
				else {
					if (file_exists("__". $paginar .".php")) {
						//logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, 0, 'navega', 'Abre página', $pagina, $str_log_oculto, '', '', '', '');
						include("__". $paginar .".php");
					}
					else {
						logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 0, 0, 'navega', '404', $pagina, $str_log_oculto, '', '', '', '');
						
						include("404.php");
					}
				}
				?>
			</div>
			
			
			
		</div>
		
		<footer id="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<hr />
						
				    	<p class="muted visible-print">
				    		Impresso em <strong><?= date('d/m/Y H:i'); ?></strong> por <strong><?=$_SESSION[nome];?></strong> - <?=$_SESSION[cargo];?>
				    		
				    		<br />
				    	</p>
				    	
				    	<p class="muted credit"><span class="hidden-print">
				    	
				    	<?=SISTEMA_NOME_COMPLETO; ?> <br/>
				    	&copy; Todos os direitos reservados &bull; <?=date("Y");?><br /> </span> 
				    	
				    	<br /></p>
					</div>
			    </div>
		    </div>
		</footer>
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-43013142-6', 'intelio.com.br');
		  ga('send', 'pageview');
		
		</script>

	</body>
</html>