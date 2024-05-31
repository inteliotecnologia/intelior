<?
require_once("includes/conexao.php");
require_once("includes/funcoes.php");

if (!isset($_GET["pagina"])) $pagina= "home";
else $pagina= $_GET["pagina"];

session_start();

if (isset($_GET["redireciona"]))
	echo
	"
	<script language='javascript' type='text/javascript'>
		window.top.location.href='./index2.php?pagina=login';
	</script>
	";
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	
		<title><?= SISTEMA_NOME_COMPLETO; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		
	    <link rel="apple-touch-icon" href="images/57.png" />
	    <link rel="apple-touch-icon" sizes="72x72" href="images/72.png" />
	    <link rel="apple-touch-icon" sizes="114x114" href="images/114.png" />
		
		<?
		if (($_SESSION['tema']!='') && ($_SESSION['tema']!='Normal'))
			$tema_css= '_'. strtolower($_SESSION['tema']);
		elseif (($_COOKIE['tema']!='') && ($_COOKIE['tema']!='Normal'))
			$tema_css= '_'. strtolower($_COOKIE['tema']);
			
			$tema_css='';
		?>
		<link href="includes/bootstrap/css/bootstrap<?=$tema_css;?>.css" rel="stylesheet" />
		<link href="includes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
		
		<link href="style.css" rel="stylesheet" />
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="includes/bootstrap/js/html5shiv.js"></script>
		<![endif]-->
		
		<link rel="shortcut icon" href="images/32.png" />
		
		<script language="javascript" type="text/javascript" src="includes/js/jquery-1.10.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="includes/bootstrap/js/bootstrap.min.js"></script>
		
		<script language="javascript" type="text/javascript" src="includes/js/functions.js"></script>
	
	</head>
	<body class="pg_<?= str_replace("/", "-", $pagina); ?>">
		
		<div class="container">
			<div class="row">
				
				<div class="caixa_login">
					<div class="cold-md-12">
						<div class="col-md-6">
							<br/><br/><br/><br/>
							
							<h3 class="sis_nome"><?=SISTEMA_NOME_COMPLETO;?></h3>
							<em class="sis_versao">Versão <?=SISTEMA_VERSAO;?></em>
							
							<br />
							
						</div>
						
						<div class="col-md-6">
							<br/><br/>
							
							<? if ($_SESSION['id_usuario']=="") { ?>
							<div class="form-signin">
							  <form enctype="multipart/form-data" method="post" action="<?=AJAX_FORM;?>formLogin&pagina=login&pre=index2.php">
							    
							    <input type="hidden" name="redirecionar" value="<?=$_GET['redirecionar'];?>" />
							    
							    <input name="email" type="email" class="form-control" value="<?=$_COOKIE['email'];?>" placeholder="Endereço de e-mail" required="required" />
							    <input name="senha" type="password" class="form-control" placeholder="Senha" required="required" />
							    
							    <? /*<label class="checkbox">
							      <input type="checkbox" value="remember-me"> Lembrar-me
							    </label>
							    <br />*/ ?>
							    
							    <button class="btn btn-lg btn-primary" type="submit" data-loading-text="Entrando...">Entrar</button>
							  </form>
							  
							  <!--<a href="index2.php?pagina=esqueci">Esqueci a minha senha</a>-->
							</div>
							<? }  else { ?>
							<h3>Já identificado!</h3>
							<br />
							<p>Você já está logado como <strong><?=$_SESSION['nome'];?></strong>. ;)</p>
							<br />
							
							<a class="btn btn-lg btn-primary" href="./?pagina=painel">Ir ao sistema &raquo;</a>
							<? } ?>
							
							<?
							if ( ($_GET["erro"]!="") || ($_GET["erros"]!="") ) {
								if ($_GET['erro']=='t') $dv_cl='alert-warning';
								elseif (($_GET['erro']=='i') || ($_GET['erro']=='j') || ($_GET['erro']=='l') ) $dv_cl= 'alert-danger';
								else $dv_cl='alert-success';
							?>
							<br />
								<div class="alert esconde <?=$dv_cl;?>">
									<a class="close" data-dismiss="alert" href="#">&times;</a>
									
									<?
						            if ($_GET["erro"]=='j') echo "<h5 class='alert-heading'>Seu acesso está inativo</h5>";
									if ($_GET["erro"]=='l') echo "<h5 class='alert-heading'>E-mail e/ou senha inválidos!</h5>";
									if ($_GET["erro"]=='t') echo "<h5 class='alert-heading'>Você saiu do sistema.</h5>";
									
									if ($_GET[erros]==='0') echo "<h5 class='alert-heading'>Solicitação concluída com sucesso.</h5>";
									elseif ($_GET[erros]>0) echo "<h5 class='alert-heading'>Não foi possível cadastrar. Por favor, tente novamente.</h5>";
									?>
								</div>
							
							<? } ?>
						</div>
					</div>
				</div>
			</div>
	    </div> <!-- /container -->
		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-43013142-1', 'otimize.org');
		  ga('send', 'pageview');
		
		</script>
		
	</body>
</html>