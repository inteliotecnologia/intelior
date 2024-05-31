<?php

function import_sql($file) {
	$delimiter = ';';
	
    $handle = fopen($file, 'r');
    $sql = '';

    if($handle) {
        /*
         * Loop through each line and build
         * the SQL query until it detects the delimiter
         */
        while(($line = fgets($handle, 4096)) !== false) {
                $sql .= trim(' ' . trim($line));
                if(substr($sql, -strlen($delimiter)) == $delimiter) {
                        mysql_query($sql) or die(mysql_error());
                        $sql = '';
                }
        }

        fclose($handle);
    } else die("Não foi possível abrir o arquivo <b>". $file ."</b> ");
}

function pega_pessoa_meta($id_pessoa, $meta) {
	$result_pre= mysql_query("select * from pessoas_meta
								where id_pessoa = '$id_pessoa'
								and   meta = '$meta'
								limit 1") or die(mysql_error());
												
	$rs_pre= mysql_fetch_object($result_pre);
	
	return($rs_pre->valor);
}

function pega_pessoa_meta_campos($i) {
	$vetor= array();
	
	/*$vetor[1][0]= "bd_host";
	$vetor[1][1]= "Endereço do BD";
	
	$vetor[2][0]= "bd_usuario";
	$vetor[2][1]= "Usuário do BD";
	
	$vetor[3][0]= "bd_senha";
	$vetor[3][1]= "Senha do BD";
	*/
	
	$vetor[3][0]= "telefone";
	$vetor[3][1]= "Telefone";
	
	$vetor[4][0]= "latitude";
	$vetor[4][1]= "Latitude";
	
	$vetor[5][0]= "longitude";
	$vetor[5][1]= "Longitude";
	
	$vetor[6][0]= "id_cidade";
	$vetor[6][1]= "Cidade";
	
	$vetor[7][0]= "cliente_desde";
	$vetor[7][1]= "Desde";
	
	$vetor[8][0]= "contato";
	$vetor[8][1]= "Contato";

	if ($i=="l") return($vetor);
	else return($vetor[$i]);
}

function pega_perfil_resumido($perfil) {
	switch($perfil) {
		case 1: $str='Administrador';
		break;
		case 2: $str='Financeiro';
		break;
		case 3: $str='Analista';
		break;
	}
	return($str);
}

function repete_str($str, $num) {
	$retorno='';
	for ($i=0; $i<$num; $i++)
		$retorno.=$str;
	
	return $retorno;
}

function prepara($str) {
	
	$str= trim($str);
	$str= mysql_real_escape_string($str);
	
	return($str);
}

function mostra($str) {
	
	$str= stripslashes($str);
	
	return($str);
}

function limpa_pasta_temp($session_id) {
	$pasta_origem= "uploads/anexos_temp/". $session_id ."/";
	$files = glob($pasta_origem .'*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
	    unlink($file); // delete file
	}
	
	$pasta_origem2= "uploads/anexos_temp/". $session_id ."/thumbnail/";
	$files = glob($pasta_origem2 .'/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
	    unlink($file); // delete file
	}
	
	@rmdir($pasta_origem);
	@rmdir($pasta_origem2);
}



function grava_acesso($id_usuario, $perfil, $data, $hora, $ip, $ip_reverso, $user_agent, $referer, $id_setor, $session_id) {
	
	$result_acesso= mysql_query("insert into acessos
								(id_usuario, perfil, data, hora, ip, ip_reverso, user_agent, referer, id_setor, session_id)
								values
								('$id_usuario', '$perfil', '". $data ."', '". $hora ."',
								'". $ip ."', '". $ip_reverso ."', '". $user_agent ."',
								'". $referer ."', '". $id_setor ."', '". $session_id ."'
								)
								") or die('2:'.mysql_error());
	$id_acesso= mysql_insert_id();
	return($id_acesso);
}

function ajeita_datas($data1, $data2, $periodo) {
	if ( ($data1!="") && ($data2!="") ) {
		$data1= formata_data_hifen($data1); $data1f= $data1;
		$data2= formata_data_hifen($data2); $data2f= $data2;
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2)+14400;
	}
	else {
		$periodo= explode('/', $periodo);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$total_dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(23, 0, 0, $periodo[0], $total_dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$data_mk[0]= $data1_mk;
	$data_mk[1]= $data2_mk;
	
	return($data_mk);
}

function format_bytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}

function converteEncode($item) {
	return mb_convert_encoding($item, "UTF-8", "ISO-8859-1");
}

function faz_embed_video($video, $largura, $altura) {
	
	if (strpos($video, "vimeo")) {
		$parte_video= explode("/", $video);
		$count_video= count($parte_video);
		
		$id_video= $parte_video[$count_video-1];
		
		$retorno= '<iframe src="http://player.vimeo.com/video/'. $id_video .'?portrait=0&amp;color=22B2BA" width="'. $largura .'" height="'. $altura .'" frameborder="0"></iframe>';
	} elseif (strpos($video, "youtube")) {
		$id_video= extrai_link_youtube($video);
		
		$retorno= pega_video_youtube($id_video, $largura, $altura);
	}
	
	return($retorno);
}

function pega_dimensao_padrao_video($tipo, $tipo_dimensao) {
	switch ($tipo) {
		case 'p':
			$largura= 940;
			$altura= 530;
		break;
		case 'a':
			$largura= 620;
			$altura= 350;
		break;
	}
	
	if ($tipo_dimensao=='l') $retorno= $largura;
	else $retorno= $altura;
	
	return($retorno);
}

function extrai_link_youtube($link) {
	//http://www.youtube.com/watch?v=lsO6D1rwrKc&v1
	//http://www.youtube.com/watch?v=Dji8M2oBVTo&mode=related&search=
	
	$novo= explode("?v=", $link);
	if (strpos($novo[1], "&")) {
		$novo= explode("&", $novo[1]);
		$link_novo= $novo[0];
	}
	else
		$link_novo= $novo[1];
	
	return($link_novo);
}


function retira_acentos($texto) {
  $array1 = array(   "#", " ", "&", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
                     , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
  $array2 = array(   "_", "_", "e", "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
                     , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
  return @str_replace( $array1, $array2, $texto );
}

function faz_url($str) {
	return(retira_acentos(strtolower(str_replace(" ", "-", $str))));
}

function string_maior_que($string, $tamanho) { 
	if (strlen($string)>$tamanho) $var= substr($string, 0, $tamanho) ."...";
	else $var= $string;
	
	return($var);
}

function pega_arquivamento($arquivamento) {
	if ($arquivamento=="1") return("Arquivado");
	else return("Retirado do arquivo");
}

function pega_sexo($sexo) {
	if ($sexo=="m") return("Masculino");
	else return("Feminino");
}

function fnum($numero) {
	$quebra= explode(".", $numero);
	$tamanho= strlen($quebra[1]);
	
	return(number_format($numero, 2, ',', '.'));
}

function fnum_naozero($numero) {
	if (($numero!=0) && ($numero!="")) {
		$quebra= explode(".", $numero);
		$tamanho= strlen($quebra[1]);
		
		return(number_format($numero, 2, ',', '.'));
	}
}

function fnum2($numero) {
	$quebra= explode(".", $numero);
	$tamanho= strlen($quebra[1]);
	
	return(number_format($numero, $tamanho, ',', '.'));
}

function fnumi($numero) {
	return(number_format($numero, 0, ',', '.'));
}

function fnumf($numero) {
	if ($numero!="") {
		$decimal= substr($numero, -2, 2);
		if ((strpos($numero, ".")) && ($decimal!="00")) return(fnum($numero));
		else return(fnumi($numero));
	}
}

function fnumf_naozero($numero) {
	if (($numero!=0) && ($numero!="")) {
		$decimal= substr($numero, -2, 2);
		if ((strpos($numero, ".")) && ($decimal!="00")) return(fnum($numero));
		else return(fnumi($numero));
	}
}

function pega_numero_semana($ano, $mes, $dia) {
   return ceil(($dia + date("w", mktime(0, 0, 0, $mes, 1, $ano)))/7);   
} 


function eh_decimal($numero) {
	$decimal= substr($numero, -2, 2);
	if ($decimal!="00") return(true);
	else return(false);
}

function primeira_palavra($frase) {
	$retorno= explode(" ", $frase);
	return($retorno[0]);
}

function formata_saida($valor, $tamanho_saida) {
	//3, 5
	$tamanho= strlen($valor);
	
	for ($i=$tamanho; $i<$tamanho_saida; $i++)
		$saida .= '0';
		
	return($saida . fnumi($valor));
}

function calcula_idade($data_nasc) {
	$var= explode("/", $data_nasc, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
	
	$dia=$var[0];
	$mes=$var[1];
	$ano=$var[2];

	if (($data_nasc!="") && ($data_nasc!="00/00/0000") && ($ano<=date("Y"))) {
		
		$idade= date("Y")-$ano;
		if ($mes>date("m"))
			$idade--;
		if (($mes==date("m")) && ($dia>date("d")) )
			$idade--;
		return($idade);
	}
	//else
	///	return("<span class=\"vermelho\">Não disponível!</span>");
}

function verifica_backup() {
	//$data= date("Y-m-d");
	//$result_pre= mysql_query("select * from backups where data_backup = '". $data ."' ");
	
	//if (mysql_num_rows($result_pre)==0)
		header("location: includes/backup/backup.php");
		
	//else echo "Backup já feito no dia de hoje!";
		
}

function soma_data($data, $dias, $meses, $anos) {
	if (strpos($data, "-")) {
		$dia_controle= explode('-', $data);
		$data= date("Y-m-d", mktime(0, 0, 0, $dia_controle[1]+$meses, $dia_controle[2]+($dias), $dia_controle[0]+$anos));
	}
	elseif (strpos($data, "/")) {
		$dia_controle= explode('/', $data);
		$data= date("d/m/Y", mktime(0, 0, 0, $dia_controle[1]+$meses, $dia_controle[0]+($dias), $dia_controle[2]+$anos));
	}
    
    return($data);
}

function soma_data_hora($data_hora, $dias, $meses, $anos, $horas, $minutos, $segundos) {
	
	//2009-10-10 10:11:12
	if (strpos($data_hora, "-")) {
		$ano= substr($data_hora, 0, 4);
		$mes= substr($data_hora, 5, 2);
		$dia= substr($data_hora, 8, 2);
		$hora= substr($data_hora, 11, 2);
		$minuto= substr($data_hora, 14, 2);
		$segundo= substr($data_hora, 17, 2);
		
		$data= date("Y-m-d H:i:s", mktime($hora+$horas, $minuto+$minutos, $segundo+$segundos, $mes+$meses, $dia+$dias, $ano+$anos));
	}
	//10/10/2009 10:11:12
	elseif (strpos($data_hora, "/")) {
		$ano= substr($data_hora, 6, 4);
		$mes= substr($data_hora, 3, 2);
		$dia= substr($data_hora, 0, 2);
		$hora= substr($data_hora, 11, 2);
		$minuto= substr($data_hora, 14, 2);
		$segundo= substr($data_hora, 17, 2);
		
		$data= date("Y-m-d H:i:s", mktime($hora+$horas, $minuto+$minutos, $segundo+$segundos, $mes+$meses, $dia+$dias, $ano+$anos));
	}
    
    return($data);
}

function pega_perfil($id_perfil) {
	$rs_pre= mysql_fetch_object(mysql_query("select perfil from cad_perfis
												where id_perfil = '$id_perfil' "));
	
	return($rs_pre->perfil);
}

function pega_nome_usuario($id_usuario) {
	$rs_pre= mysql_fetch_object(mysql_query("select * from usuarios
												where id_usuario = '$id_usuario' "));
	
	return($rs_pre->nome);
}

function pega_status_generico($status) {
	if ($status=='1') $str= 'Ativos';
	elseif ($status=='2') $str= 'Suspensos';
	elseif ($status=='0') $str= 'Excluídos';
	
	return($str);
}

function pega_pessoa($id_pessoa, $campo) {
	
	$rs_pre= mysql_fetch_object(mysql_query("select *, $campo as campo from pessoas
												where id_pessoa = '$id_pessoa'
												limit 1 "));
	
	
	return($rs_pre->campo);
}

function pega_usuario($id_usuario, $campo) {
	$campo2= $campo;
	
	$rs_pre= mysql_fetch_object(mysql_query("select *, $campo2 as campo from pessoas, usuarios
												where usuarios.id_usuario = '$id_usuario'
												and   pessoas.id_pessoa = usuarios.id_pessoa
												limit 1 "));
	
	
	return($rs_pre->campo);
}


function pega_cliente($id_cliente, $campo) {
	
	$rs_pre= mysql_fetch_object(mysql_query("select *, $campo as campo from pessoas, clientes
												where id_cliente = '$id_cliente'
												and   pessoas.id_pessoa = usuarios.id_pessoa
												limit 1 "));
	
	
	return($rs_pre->campo);
}

function pega_configuracao($configuracao) {
	$rs_pre= mysql_fetch_object(mysql_query("select valor from configuracoes
												where configuracao = '$configuracao' "));
	
	return($rs_pre->valor);
}

function traduz_periodicidade($p) {
	
	switch ($p[1]) {
		case "d": $periodo= "dia"; break;
		case "m": $periodo= "mês"; break;
		case "a": $periodo= "ano"; break;
	}
	
	return($p[0] ."x/". $periodo);
}

function valor_extenso($valor=0) {

	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "trÍs", "quatro", "cinco", "seis","sete", "oito", "nove");

	$z=0;

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
	
		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")$z++; elseif ($z > 0) $z--;
		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : "") . $r;
	}

	return($rt ? $rt : "zero");
}

function formata_hora($var) {
	//transformando em segundos
	$var= explode(":", $var, 3);
	
	$total_horas= $var[0]*3600;
	$total_minutos= $var[1]*60;
	$total_segundos= $var[2];
	
	$var= $total_horas+$total_minutos+$total_segundos;
	
	return($var);
}

function pode_um($area, $permissao) {
	$contem= strpos($permissao, $area);

	if ($contem!==false) $retorno= true;
	else $retorno= false;
		
	return($retorno);
}

function pode($areas, $permissao) {
	$tamanho= strlen($areas);
	$retorno= false;
	
	for ($i=0; $i<$tamanho; $i++) {
		
		if (pode_um($areas[$i], $permissao)) {
			$retorno=true;
			break;
		}
	}

	return($retorno);
}

function pode_algum($areas, $permissao) {
	$tamanho= strlen($areas);
	$retorno= false;
	
	for ($i=0; $i<$tamanho; $i++) {
		if (pode_um($areas[$i], $permissao)) {
			$retorno=true;
			break;
		}
	}

	return($retorno);
}

function logs($id_acesso, $id_usuario, $perfil, $tipo, $id_referencia, $area, $acao, $descricao, $descricao_oculta, $ip, $ip_reverso, $user_agent, $referer) {
	
	$descricao= str_replace('|', '\r\n', $descricao);
	$descricao_oculta= str_replace('|', '\r\n', $descricao_oculta);
	
	$result= mysql_query("insert into logs (id_acesso, id_usuario, perfil, tipo, id_referencia, area, acao, descricao, descricao_oculta, data, hora, ip, ip_reverso, user_agent, referer)
							values
							('$id_acesso', '$id_usuario', '$perfil', '$tipo', '$id_referencia', '$area', '$acao', '$descricao', '$descricao_oculta', '". date("Y-m-d") ."', '". date("H:i:s") ."', '$ip', '$ip_reverso', '$user_agent', '$referer')
							") or die(mysql_error());
}


function traduz_mes($mes) {
	switch($mes) {
		case 1: $retorno= "Janeiro"; break;
		case 2: $retorno= "Fevereiro"; break;
		case 3: $retorno= "Março"; break;
		case 4: $retorno= "Abril"; break;
		case 5: $retorno= "Maio"; break;
		case 6: $retorno= "Junho"; break;
		case 7: $retorno= "Julho"; break;
		case 8: $retorno= "Agosto"; break;
		case 9: $retorno= "Setembro"; break;
		case 10: $retorno= "Outubro"; break;
		case 11: $retorno= "Novembro"; break;
		case 12: $retorno= "Dezembro"; break;
		default: $retorno= ""; break;
	}
	return($retorno);
}

function inverte($num) {
	if ($num==1) return(0);
	else return(1);
}

function excluido_ou_nao($var) {
	if ($var==0) $retorno_msg= "Excluído com sucesso!";
	else $retorno_msg= "Não foi possível excluir!";
	
	return("<script language=\"javascript\">alert('". $retorno_msg ."');</script>");
}

function sim_nao($situacao) {
	if (($situacao==0) || ($situacao==2)) return("<span class=\"vermelho\">NÃO</span>");
	else return("<span class=\"verde\">SIM</span>");
}

function ativo_inativo($situacao) {
	if ($situacao==1) return("<span class=\"verde\">ATIVO</span>");
	elseif ($situacao==-1) return("<span class=\"vermelho\">EM ESPERA</span>");
	else return("<span class=\"vermelho\">INATIVO</span>");
}

function pega_cidade($id_cidade) {
	$rs= mysql_fetch_object(mysql_query("select cidades.cidade, ufs.uf from cidades, ufs
											where cidades.id_uf = ufs.id_uf
											and   cidades.id_cidade = '$id_cidade'
											"));
	return($rs->cidade ."/". $rs->uf);
}

function pega_uf($id_uf) {
	$rs= mysql_fetch_object(mysql_query("select uf from ufs where id_uf = '$id_uf' "));
	return($rs->uf);
}

function pega_id_uf($id_cidade) {
	$rs= mysql_fetch_object(mysql_query("select id_uf from cidades
											where id_cidade = '$id_cidade'
											"));
	return($rs->id_uf);
}

function inicia_transacao() {
	mysql_query("set autocommit=0;");
	mysql_query("start transaction;");
}

function finaliza_transacao($var) {
	if ($var==0) mysql_query("commit;");
	else mysql_query("rollback;");
}

function gera_auth() {
	return(substr(strtoupper(md5(uniqid(rand(), true))), 0, 24));
}

function tira_caracteres($char) {
	return(str_replace("'", "xxx", str_replace('"', 'xxx', str_replace('/', '', str_replace('.', '', str_replace('-', '', $char))))));
}

function formata_cpf($cpf) {
	$cpfn= substr($cpf, 0, 3) .".". substr($cpf, 3, 3) .".". substr($cpf, 6, 3) ."-". substr($cpf, 9, 2);
	return($cpfn);
}

function pega_horario($horario, $tipo) {
	
	switch($tipo) {
		case 'h': $retorno= substr($horario, 0, 2); break;
		case 'm': $retorno= substr($horario, 3, 2); break;
		case 's': $retorno= substr($horario, 5, 2); break;
	}
	
	return($retorno);
}

function formata_cnpj($cnpj) {
	//99.999.999/9999-99
	//99 999 999 9999 99
	$cnpj= substr($cnpj, 0, 2) .".". substr($cnpj, 2, 3) .".". substr($cnpj, 5, 3) ."/". substr($cnpj, 8, 4) ."-". substr($cnpj, 12, 2);
	return($cnpj);
}

function formata_data($var) {
	$var= explode("/", $var, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
		
	$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function formata_data_timestamp($var) {
	$var= explode(" ", $var, 2);
	
	return(desformata_data($var[0]) . " ". $var[1]);
	
}


function formata_data_hifen($var) {
	$var= explode("/", $var, 3);
	
	if (strlen($var[2])==2)
		$var[2]= "20". $var[2];
		
	$var= $var[2] .'-'. $var[1] .'-'. $var[0];
	return($var);
}


function faz_mk_data($var) {
	if (strpos($var, "-")) {
		$var= explode("-", $var, 3);
		$mk= mktime(14, 0, 0, $var[1], $var[2], $var[0]);
		return($mk);
	}
	else {
		$var= explode("/", $var, 3);
		$mk= mktime(14, 0, 0, $var[1], $var[0], $var[2]);
		return($mk);
	}
}

function faz_mk_hora($var) {
	$var= explode(":", $var, 3);
	$mk= mktime($var[0], $var[1], $var[2], 0, 0, 0);
	return($mk);
}

function faz_mk_hora_simples($var) {
	$var= explode(":", $var, 3);
	$mk= (($var[0]*3600)+($var[1]*60)+$var[2]);
	return($mk);
}

function faz_mk_data_completa($var) {
	
	if (strpos($var, "-")) {
		//2008-07-31 13:25:05 
		$data_completa= explode(" ", $var, 2);
		
		$data= explode("-", $data_completa[0], 3);
		$hora= explode(":", $data_completa[1], 3);
		
		$mk= mktime($hora[0], $hora[1], $hora[2], $data[1], $data[2], $data[0]);
	}
	elseif (strpos($var, "/")) {
		//31/07/2008 13:25:05 
		$data_completa= explode(" ", $var, 2);
		
		$data= explode("/", $data_completa[0], 3);
		$hora= explode(":", $data_completa[1], 3);
		
		$mk= mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);
	}
	
	return($mk);
}

function desformata_datetime($var) {
	$var= explode(" ", $var, 2);
	
	return(desformata_data($var[0]) ." às ". substr($var[1], 0, 5) );
}

function desformata_data($var) {
	if (($var!="") && ($var!="0000-00-00")) {
		//2006-10-12
		$var= explode("-", $var, 3);
		
		//10/10/2007
		$var= $var[2] .'/'. $var[1] .'/'. $var[0];
		return($var);
	}
}

function pega_dia($var) {
	return(substr($var, 6, 2));
}

function pega_mes($var) {
	return(substr($var, 4, 2));
}

function pega_ano($var) {
	return(substr($var, 0, 4));
}

function aumenta_dia($var) {
	//22-10-2007
	$var= explode("-", $var, 3);
	
	$data_ano= date("Y", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	$data_mes= date("m", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	$data_dia= date("d", mktime(0, 0, 0, $var[1], $var[0]+1, $var[2]));
	
	$var[0]= $data_dia;
	$var[1]= $data_mes;
	$var[2]= $data_ano;
	
	//2006-10-12
	//$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function soma_mes($var, $valor) {
	
	if (strpos($var, "-")) {
		//2008-07-31
		$data_completa= explode(" ", $var, 2);
		$data= explode("-", $data_completa[0], 3);
		
		$mk= mktime(0, 0, 0, $data[1]+($valor), $data[2], $data[0]);
	}
	elseif (strpos($var, "/")) {
		//31/07/2008
		$data_completa= explode(" ", $var, 2);
		$data= explode("/", $data_completa[0], 3);
		
		$mk= mktime(0, 0, 0, $data[1]+($valor), $data[0], $data[2]);
	}
	
	$var= date("Y-m-d", $mk);
	//2006-10-12
	//$var= $var[2] . $var[1] . $var[0];
	return($var);
}

function formata_valor($var) {
	$var= str_replace(',', '.', str_replace('.', '', $var));
	return($var);
}

function data_extenso_param($data) {
	$data= explode('-', $data);
	
	$data_extenso .= $data[2];
	$data_extenso .= " de ";
	
	switch($data[1]) {
		case 1: $data_extenso .= "Janeiro"; break;
		case 2: $data_extenso .= "Fevereiro"; break;
		case 3: $data_extenso .= "Março"; break;
		case 4: $data_extenso .= "Abril"; break;
		case 5: $data_extenso .= "Maio"; break;
		case 6: $data_extenso .= "Junho"; break;
		case 7: $data_extenso .= "Julho"; break;
		case 8: $data_extenso .= "Agosto"; break;
		case 9: $data_extenso .= "Setembro"; break;
		case 10: $data_extenso .= "Outubro"; break;
		case 11: $data_extenso .= "Novembro"; break;
		case 12: $data_extenso .= "Dezembro"; break;
	}
	$data_extenso .= " de ";
	$data_extenso .= $data[0];
	return($data_extenso);
}

function traduz_dia($dia) {
	switch($dia) {
		case 0: $retorno= "Domingo"; break;
		case 1: $retorno= "Segunda"; break;
		case 2: $retorno= "Terça"; break;
		case 3: $retorno= "Quarta"; break;
		case 4: $retorno= "Quinta"; break;
		case 5: $retorno= "Sexta"; break;
		case 6: $retorno= "Sábado"; break;
	}
	return($retorno);
}

function pega_prioridade($i) {
	$vetor= array();
	
	$vetor[1]= "Baixa";
	$vetor[2]= "Média";
	$vetor[3]= "Alta";
	$vetor[4]= "Urgente";
	
	if ($i=="l") return($vetor);
	else return($vetor[$i]);
}

function enviar_email($email, $titulo, $corpo) {
	$enviado= @mail($email, $titulo, $corpo, "From: Sistema <sistema@sistema.com> \nContent-type: text/html\n");
}

?>