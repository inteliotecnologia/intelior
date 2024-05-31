<?
require_once("includes/conexao.unico.php");
require_once("includes/funcoes.php");

define("AJAX_LINK", "link.php?");
define("AJAX_FORM", "form.php?");
define("CAMINHO", "uploads/");

define("SISTEMA_NOME", "Intelior");
define("SISTEMA_NOME_COMPLETO", "Intelior");
define("SISTEMA_VERSAO", "0.1");

//se a pagina atual nao for a de login
if ( ($_GET["pagina"]!="home") && ($_GET["pagina"]!="esqueci_senha") && ($_GET["pagina"]!="acesso/proposta_gera") && ($_GET["pagina"]!="login") && ($_GET["pagina"]!="widget") ) {
	$retorno= true;
	if ($_SESSION["id_usuario"]=="")
		$retorno= false;
	
	if (!$retorno)
		header("location: ./index2.php?pagina=login&redirecionar=". base64_encode($_SERVER[QUERY_STRING]));
}
?>