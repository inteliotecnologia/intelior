<?
session_start();

$conf_usuario="intelio_user";
$conf_db="intelior";

$conexao= @mysql_connect("enceladus.cle1tvcm29jx.us-east-1.rds.amazonaws.com", $conf_usuario, "2i92iS02iV3d23dm") or die("O servidor está um pouco instável, favor tente novamente! ". mysql_error());
@mysql_select_db($conf_db) or die("O servidor está um pouco instável, favor tente novamente!! ". mysql_error());

mysql_query('set names utf8;');
?>