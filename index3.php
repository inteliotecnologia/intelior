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
	
$result= mysql_query("select * from  propostas
						where propostas.id_proposta = '". ($_GET["id_proposta"]) ."'
						". $str ."
						and   propostas.status <> '2'
						") or die(mysql_error());
$num= mysql_num_rows($result);
$rs= mysql_fetch_object($result);
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		
		<?php
		$titulo_pagina= "Intelio - Proposta ". formata_saida($rs->num, 3) ."/". $rs->ano ." - ". pega_pessoa($rs->id_pessoa, 'nome');
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
			
			$tema_css='';
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
		<div class="container container_inner">
			<?
			if (isset($_GET["pagina"])) $pagina= $_GET["pagina"];
			else $pagina= $pagina;
			
			$paginar= $pagina;
			if (strpos($paginar, "/")) {
				$parte_pagina= explode("/", $paginar);
				
				if (file_exists("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php"))
					include("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php");
				else include("404.php");
			}
			else {
				if (file_exists("__". $paginar .".php"))
					include("__". $paginar .".php");
				else include("404.php");
			}
			?>
		</div>
		<br/><br/>
		<footer id="footer">
			<div class="container">
				<div class="row">
					<div class="col-xs-9 col-md-9 col-xs-offset-3 col-md-offset-3">
						<hr />
						
				    	<p class="muted" style="line-height: 15px;">
				    		<small>
				    			Aviso: a informação contida nesta proposta é confidencial. Poderá ser de conteúdo privilegiado e conter informações que destinam-se exclusivamente a pessoa a quem está endereçada. Se o leitor desta mensagem não é o destinatário pretendido, por favor a elimine imediatamente de seus arquivos e nos informe do recebimento indevido. Qualquer acesso, uso, disseminação ou cópia não autorizada constitui prática proibida.
				    		</small>
				    	</p>
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