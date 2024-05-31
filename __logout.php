<?
session_start();

require_once("includes/conexao.php");
require_once("includes/funcoes.php");

//log($_SESSION["id_usuario"], $_SESSION["id_empresa"], 's', $_SERVER["REMOTE_ADDR"], gethostbyaddr($_SERVER["REMOTE_ADDR"]));

logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, 0, 'login', 'Faz logout', '', $str_log_oculto, $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);

$_SESSION["id_usuario"]="";
$_SESSION["id_acesso"]="";
$_SESSION["nome"]="";
$_SESSION["cargo"]="";
$_SESSION["tema"]= "";
$_SESSION["foto"]= "";
$_SESSION["ultimo_login"]="";

session_destroy();

header("location: ./index2.php?pagina=login&erro=t");
?>