<?
require_once("includes/funcoes.php");
if (!$conexao) require_once("includes/conexao.php");

header("Content-type: text/html; charset=utf-8", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//anyone
if (isset($_GET["formLogin"])) {
	
	//$_POST= array_map('trim', $_POST);
	//$_POST= array_map('htmlentities', $_POST);
	$_POST= array_map('prepara', $_POST);
	
	$email= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["email"]));
	$senha= str_replace("'", "xxx", str_replace('"', 'xxx', $_POST["senha"]));
	
	$erros='';
	if ($email=='') $erros.='E-mail não pode estar em branco.<br>';
	if ($senha=='') $erros.='Senha não pode estar em branco.<br>';
	if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
	
	if ($_POST[redirecionar]!='') $redir_add= "&redirecionar=". $_POST[redirecionar];
	
	$result= mysql_query("select *, pessoas.status as status_usuario from pessoas, usuarios
							where usuarios.id_pessoa = pessoas.id_pessoa
							and   pessoas.email= '$email'
							and   usuarios.senha= '". md5($senha) ."'
							/* and   usuarios.status = '1' */
							") or die('1:'.mysql_error());
	
	if (mysql_num_rows($result)==0) {
		
		logs(0, 0, 0, 0, 0, 'login', 'Dados inválidos', 'E-mail: '. prepara($_POST[email]), 'Senha: '. prepara($_POST[senha]), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
		
		header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&erro=l". $redir_add);
	}
	else {
		$rs= mysql_fetch_object($result);
		
		if ($rs->status_usuario=='0') {
			logs(0, 0, 0, 0, $rs->id_usuario, 'login', 'Usuário excluído', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&erro=i". $redir_add);
		}
		elseif ($rs->status_usuario=='2') {
			logs(0, 0, 0, 0, $rs->id_usuario, 'login', 'Usuário desativado', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			header("location: ./". $_GET[pre] ."?pagina=". $_GET[pagina] ."&erro=j". $redir_add);
		}
		else {
			session_start();	
			
			$_SESSION["id_usuario"]= $rs->id_usuario;
			$_SESSION["perfil"]= $rs->perfil;
			$_SESSION["nome"]= $rs->nome;
			$_SESSION["tema"]= $rs->tema;
			$_SESSION["foto"]= $rs->foto;
			$_SESSION["ultimo_login"]= $rs->ultimo_login;
			
			$id_acesso= grava_acesso($_SESSION[id_usuario], $_SESSION["perfil"], date('Y-m-d'), date('H:i:s'), $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER], $_SESSION["id_setor"], session_id());
			
			$_SESSION["id_acesso"]= $id_acesso;
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $_SESSION[id_setor], 'login', 'Login com sucesso', '', '', $_SERVER[REMOTE_ADDR], gethostbyaddr($_SERVER[REMOTE_ADDR]), $_SERVER[HTTP_USER_AGENT], $_SERVER[HTTP_REFERER]);
			
			if ($_POST[redirecionar]!='')
				$redir= "./?". base64_decode($_POST[redirecionar]);
			else
				$redir= "./?pagina=acesso/clientes" ;
			
			setcookie ("tema", $rs->tema, time()+(90*24*3600));
			setcookie ("email", $email, time()+(90*24*3600));
			
			$result_ul= mysql_query("update usuarios
									set ultimo_login = '". date("Y-m-d H:i:s") . "'
									where id_usuario= '". $rs->id_usuario ."'
									limit 1
									") or die(mysql_error());
			
			header("location: ". $redir);
		}
	}
}//fim login

//Administrador ou setor nível 1
if (pode("1", $_SESSION["perfil"])) {
	
	/*
		
	if (isset($_GET["formUsuario"])) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		if ($_GET["acao"]=="i") {	
			
			$result_pre= mysql_query("select * from usuarios, pessoas
										where pessoas.email = '". prepara($_POST[email]) ."'
										and   pessoas.id_pessoa = usuarios.id_pessoa
										and   pessoas.status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
			if ($_POST[email]=='') $erros.='E-mail não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Usuário com este e-mail (<strong>'. $_POST[email] .'</strong>) já cadastrado.<br>';
			
			if ($_POST[senha]=='') $erros.='Senha não pode estar em branco.<br>';
			if ($_POST[perfil]=='') $erros.='Perfil não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into usuarios (nome, email, senha, senha_sem, perfil, sexo,
									cpf, cargo, data_nasc, auth, id_usuario_criou, id_acesso,
									ultimo_login, data_cadastro, hora_cadastro, tema, status) values
									('". prepara($_POST["nome"]) ."', '". prepara($_POST["email"]) ."', '". md5($_POST["senha"]) ."', '". prepara($_POST["senha"]) ."', '". prepara($_POST["perfil"]) ."', 
										'". prepara($_POST["sexo"]) ."', '". prepara($_POST["cpf"]) ."', '". prepara($_POST["cargo"]) ."', '". formata_data(prepara($_POST["data_nasc"])) ."', '". gera_auth() ."',
										'". $_SESSION["id_usuario"] ."', '". $_SESSION["id_acesso"] ."', '0', '". date('Y-m-d') ."', '". date('H:i:s') ."', 'Normal', '1') ") or die("1: ". mysql_error());
			if (!$result1) $var++;
			$id_usuario= mysql_insert_id();
			
			if ($_FILES["foto"]["name"]!="") {
				$caminho= CAMINHO . "". $id_usuario ."_". $_FILES["foto"]["name"];
				move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
				
				$result_atualiza= mysql_query("update usuarios set foto = '$caminho'
												where id_usuario = '". $id_usuario ."'
												limit 1
												") or die(mysql_error());
				if (!$result_atualiza) $var++;
				
				$str_log.= ' | envia foto: '. $caminho;
			}
			
			$str_log_oculto.= ' | senha: '. $_POST[senha];
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $id_usuario, 'usuarios', 'Insere usuário', 'Nome: '. prepara($_POST[nome]) . $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/usuarios&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
		
			$result_pre= mysql_query("select * from usuarios
										where email = '". prepara($_POST[email]) ."'
										and   id_usuario <> '". prepara($_POST[id_usuario]) ."'
										and   status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
			if ($_POST[email]=='') $erros.='E-mail não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Já existe outro usuário cadastrado com este e-mail.<br>';
			if ($_POST[perfil]=='') $erros.='Perfil não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			
			inicia_transacao();
			
			if ($_POST["senha"]!="") {
				$linha_senha= ", senha= '". md5($_POST["senha"]) ."', senha_sem= '". prepara($_POST["senha"]) ."' ";
				
				$str_log.= ' | muda a senha';
				$str_log_oculto.= ' | nova senha: '. prepara($_POST[senha]);
			}
			
			$result1= mysql_query("update usuarios set
									usuarios.nome= '". prepara($_POST["nome"]) ."',
									usuarios.email= '". prepara($_POST["email"]) ."',
									usuarios.sexo= '". prepara($_POST["sexo"]) ."',
									usuarios.cpf= '". prepara($_POST["cpf"]) ."',
									usuarios.cargo= '". prepara($_POST["cargo"]) ."',
									usuarios.data_nasc= '". formata_data(prepara($_POST["data_nasc"])) ."'
									". $linha_senha ."
									where usuarios.id_usuario = '". prepara($_POST[id_usuario]) ."'
									". $str ."
									") or die('1:'.mysql_error());
			if (!$result1) $var++;
			
			if ($_FILES["foto"]["name"]!="") {
				$caminho= CAMINHO . "". prepara($_POST["id_usuario"]) ."_". $_FILES["foto"]["name"];
				move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
				
				$result_atualiza= mysql_query("update usuarios set foto = '$caminho'
												where id_usuario = '". prepara($_POST["id_usuario"]) ."'
												") or die('2:'.mysql_error());
				if (!$result_atualiza) $var++;
				
				$str_log.= ' | envia foto: '. $caminho;
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_POST[id_usuario]), 'usuarios', 'Edita usuário', 'Nome: '. prepara($_POST[nome]) .' | '. $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/usuarios&erros=". $var);
			
		}//e
		
	}//formUsuario
	*/
	
	if (isset($_GET["formNota"])) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		if ($_GET["acao"]=="i") {	
			
			$result_pre= mysql_query("select * from notas
										where numero = '". prepara($_POST[numero]) ."'
										and   id_pessoa = '". prepara($_POST[id_pessoa]) ."'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[id_pessoa]=='') $erros.='Pessoa não pode estar em branco.<br>';
			if ($_POST[numero]=='') $erros.='Número não pode estar em branco.<br>';
			if ($_POST[data]=='') $erros.='Data não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Nota <strong>'. $_POST[numero] .'</strong> já cadastrada.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			inicia_transacao();
			
			$hash= gera_auth();
			
			$result1= mysql_query("insert into notas (tipo_nota, id_carteira, id_projeto,
														id_pessoa, id_proposta, numero,
														data, valor, descricao,
														hash, status, id_usuario, id_acesso ) values
									(
									'". prepara($_POST["tipo_nota"]) ."', '". prepara($_POST["id_carteira"]) ."', '". prepara($_POST["id_projeto"]) ."',
									'". prepara($_POST["id_pessoa"]) ."', '". prepara($_POST["id_proposta"]) ."', '". prepara($_POST["numero"]) ."', 
									'". formata_data($_POST["data"]) ."', '". formata_valor($_POST["valor"]) ."', '". prepara($_POST["descricao"]) ."', 
									'". $hash ."', '1', '". ($_SESSION["id_usuario"]) ."', '". ($_SESSION["id_acesso"]) ."'
									
									) ") or die("1: ". mysql_error());
			if (!$result1) $var++;
			$id_nota= mysql_insert_id();
			
			$total_arquivos= count($_POST[arquivo]);
			
			if ($total_arquivos>0) {			
				$mes= date('m');
				$dia= date('d');
				
				//uploads para seus devidos lugares
				$pasta_destino= CAMINHO ."/anexos/notas/". $hash ."/";
				
				if (!is_dir($pasta_destino)) {
					
					$str_log.=' | cria a pasta '. $pasta_destino .' ';
					
					$cria= @mkdir($pasta_destino, 0775, true);
					
					@file_put_contents($pasta_destino . "index.php", "");
					
					if (!$cria) die('Não foi possível enviar os arquivos anexos. Consulte um administrador do sistema e informe o código: ERRO_007 <br><br>');
				}
				
				$pasta_origem= CAMINHO. "anexos_temp/". session_id() ."/";
				
				$apaga_pasta_temp= true;
				
				$i=0;
				while ($_POST[arquivo][$i]) {
					
					$copia[$i]= @copy($pasta_origem . prepara($_POST[arquivo][$i]), $pasta_destino . prepara($_POST[arquivo][$i]));
					
					if (!$copia[$i]) {
						$apaga_pasta_temp= false;
						
						$str_log.=' | não foi possível copiar arquivo '. prepara($_POST[arquivo][$i]) .' em '. $pasta_destino .' ';
					}
					else {
						$str_log.=' | anexa arquivo de '. $pasta_origem . prepara($_POST[arquivo][$i]) .' -> '. $pasta_destino . prepara($_POST[arquivo][$i]) .' / tipo: '. $_POST[tipo][$i] .' / tamanho: '. format_bytes(prepara($_POST[tamanho][$i])) .' ';
						
						$hash_arquivo= gera_auth();
						
						$result3[$i]= mysql_query("insert into arquivos
													(tabela, id, tamanho,
													arquivo, tipo, hash_arquivo, status,
													id_usuario, id_acesso)
													values
													('notas', '". $id_nota ."', '". prepara($_POST[tamanho][$i]) ."',
													'". prepara($_POST[arquivo][$i]) ."', '". prepara($_POST[tipo][$i]) ."', '". $hash_arquivo ."', '1',
													'". $_SESSION[id_usuario] ."', '". $_SESSION[id_acesso] ."' )
													
													") or die("1: ". mysql_error());
						if (!$result3[$i]) $var++;
							
					}
					
					$i++;
				}
				
				if ($apaga_pasta_temp) limpa_pasta_temp(session_id());
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $id_usuario, 'notas', 'Insere nota', 'Nota: '. $id_nota .' | número: '. prepara($_POST[numero]) . $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/caixa&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
					
			$erros='';
			if ($_POST[id_pessoa]=='') $erros.='Pessoa não pode estar em branco.<br>';
			if ($_POST[numero]=='') $erros.='Número não pode estar em branco.<br>';
			if ($_POST[data]=='') $erros.='Data não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			
			inicia_transacao();
			
			$result1= mysql_query("update notas set
									id_carteira= '". prepara($_POST["id_carteira"]) ."',
									id_projeto= '". prepara($_POST["id_projeto"]) ."',
									id_pessoa= '". prepara($_POST["id_pessoa"]) ."',
									id_proposta= '". prepara($_POST["id_proposta"]) ."',
									
									numero= '". prepara($_POST["numero"]) ."',
									data= '". formata_data($_POST["data"]) ."',
									valor= '". formata_valor($_POST["valor"]) ."',
									descricao= '". prepara($_POST["descricao"]) ."'
									
									where id_nota = '". prepara($_POST[id_nota]) ."'
									". $str ."
									") or die('1:'.mysql_error());
			if (!$result1) $var++;
			
			$total_arquivos= count($_POST[arquivo]);
			
			if ($total_arquivos>0) {			
				
				//uploads para seus devidos lugares
				$pasta_destino= CAMINHO ."/anexos/notas/". $_POST[hash] ."/";
				
				if (!is_dir($pasta_destino)) {
					
					$str_log.=' | cria a pasta '. $pasta_destino .' ';
					
					$cria= @mkdir($pasta_destino, 0775, true);
					
					@file_put_contents($pasta_destino . "index.php", "");
					
					if (!$cria) die('Não foi possível enviar os arquivos anexos. Consulte um administrador do sistema e informe o código: ERRO_007 <br><br>');
				}
				
				$pasta_origem= CAMINHO. "anexos_temp/". session_id() ."/";
				
				$apaga_pasta_temp= true;
				
				$i=0;
				while ($_POST[arquivo][$i]) {
					
					$copia[$i]= @copy($pasta_origem . prepara($_POST[arquivo][$i]), $pasta_destino . prepara($_POST[arquivo][$i]));
					
					if (!$copia[$i]) {
						$apaga_pasta_temp= false;
						
						$str_log.=' | não foi possível copiar arquivo '. prepara($_POST[arquivo][$i]) .' em '. $pasta_destino .' ';
					}
					else {
						$str_log.=' | anexa arquivo de '. $pasta_origem . prepara($_POST[arquivo][$i]) .' -> '. $pasta_destino . prepara($_POST[arquivo][$i]) .' / tipo: '. $_POST[tipo][$i] .' / tamanho: '. format_bytes(prepara($_POST[tamanho][$i])) .' ';
						
						$hash_arquivo= gera_auth();
						
						$result3[$i]= mysql_query("insert into arquivos
													(tabela, id, tamanho,
													arquivo, tipo, hash_arquivo, status,
													id_usuario, id_acesso)
													values
													('notas', '". $_POST[id_nota] ."', '". prepara($_POST[tamanho][$i]) ."',
													'". prepara($_POST[arquivo][$i]) ."', '". prepara($_POST[tipo][$i]) ."', '". $hash_arquivo ."', '1',
													'". $_SESSION[id_usuario] ."', '". $_SESSION[id_acesso] ."' )
													
													") or die("1: ". mysql_error());
						if (!$result3[$i]) $var++;
							
					}
					
					$i++;
				}
				
				if ($apaga_pasta_temp) limpa_pasta_temp(session_id());
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_POST[id_usuario]), 'notas', 'Edita nota', 'Nota: '. prepara($_POST[id_nota]) .' | '. $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/caixa&erros=". $var);
			
		}//e
		
	}//formNota
	
	if (isset($_GET["formProposta"])) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		if ($_GET["acao"]=="i") {	
			
			$erros='';
			if ($_POST[id_pessoa]=='') $erros.='Pessoa não pode estar em branco.<br>';
			if ($_POST[proposta]=='') $erros.='Proposta não pode estar em branco.<br>';
			if ($_POST[data]=='') $erros.='Data não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			inicia_transacao();
			
			$hash= gera_auth();
			
			$ano= substr($_POST[data], 6, 4);
			
			$result_num= mysql_query("select num from propostas
										where DATE_FORMAT(data, '%Y') = '". $ano ."'
										and   subnum = '0'
										order by data desc limit 1
										");
			$rs_num= mysql_fetch_object($result_num);
			
			$num= $rs_num->num+1;
			
			$result1= mysql_query("insert into propostas (tipo_proposta, id_pessoa, num, subnum,
														ano, proposta, descricao,
														data, hora, valor, valido_ate,
														hash, status, id_usuario, id_acesso ) values
									(
									'". prepara($_POST["tipo_nota"]) ."', '". prepara($_POST["id_pessoa"]) ."', '". $num ."', '". $subnum ."',
									'". $ano ."', '". prepara($_POST["proposta"]) ."', '". prepara($_POST["descricao"]) ."', 
									'". formata_data($_POST["data"]) ."', '". $_POST["hora"] ."', '". formata_valor($_POST["valor"]) ."', '". formata_data($_POST["valido_ate"]) ."',
									
									'". $hash ."', '1', '". ($_SESSION["id_usuario"]) ."', '". ($_SESSION["id_acesso"]) ."'
									
									) ") or die("1: ". mysql_error());
			if (!$result1) $var++;
			$id_proposta= mysql_insert_id();
			
			$total_arquivos= count($_POST[arquivo]);
			
			if ($total_arquivos>0) {			
				$mes= date('m');
				$dia= date('d');
				
				//uploads para seus devidos lugares
				$pasta_destino= CAMINHO ."/anexos/propostas/". $hash ."/";
				
				if (!is_dir($pasta_destino)) {
					
					$str_log.=' | cria a pasta '. $pasta_destino .' ';
					
					$cria= @mkdir($pasta_destino, 0775, true);
					
					@file_put_contents($pasta_destino . "index.php", "");
					
					if (!$cria) die('Não foi possível enviar os arquivos anexos. Consulte um administrador do sistema e informe o código: ERRO_007 <br><br>');
				}
				
				$pasta_origem= CAMINHO. "anexos_temp/". session_id() ."/";
				
				$apaga_pasta_temp= true;
				
				$i=0;
				while ($_POST[arquivo][$i]) {
					
					$copia[$i]= @copy($pasta_origem . prepara($_POST[arquivo][$i]), $pasta_destino . prepara($_POST[arquivo][$i]));
					
					if (!$copia[$i]) {
						$apaga_pasta_temp= false;
						
						$str_log.=' | não foi possível copiar arquivo '. prepara($_POST[arquivo][$i]) .' em '. $pasta_destino .' ';
					}
					else {
						$str_log.=' | anexa arquivo de '. $pasta_origem . prepara($_POST[arquivo][$i]) .' -> '. $pasta_destino . prepara($_POST[arquivo][$i]) .' / tipo: '. $_POST[tipo][$i] .' / tamanho: '. format_bytes(prepara($_POST[tamanho][$i])) .' ';
						
						$hash_arquivo= gera_auth();
						
						$result3[$i]= mysql_query("insert into arquivos
													(tabela, id, tamanho,
													arquivo, tipo, hash_arquivo, status,
													id_usuario, id_acesso)
													values
													('propostas', '". $id_proposta ."', '". prepara($_POST[tamanho][$i]) ."',
													'". prepara($_POST[arquivo][$i]) ."', '". prepara($_POST[tipo][$i]) ."', '". $hash_arquivo ."', '1',
													'". $_SESSION[id_usuario] ."', '". $_SESSION[id_acesso] ."' )
													
													") or die("1: ". mysql_error());
						if (!$result3[$i]) $var++;
							
					}
					
					$i++;
				}
				
				if ($apaga_pasta_temp) limpa_pasta_temp(session_id());
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $id_usuario, 'propostas', 'Insere proposta', 'Proposta: '. $id_proposta .' | Proposta: '. prepara($_POST[proposta]) . $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/propostas&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
					
			$erros='';
			if ($_POST[id_pessoa]=='') $erros.='Pessoa não pode estar em branco.<br>';
			if ($_POST[proposta]=='') $erros.='Proposta não pode estar em branco.<br>';
			if ($_POST[data]=='') $erros.='Data não pode estar em branco.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			
			inicia_transacao();
			
			$result1= mysql_query("update propostas set
									
									id_pessoa= '". prepara($_POST["id_pessoa"]) ."',
									proposta= '". prepara($_POST["proposta"]) ."',
									
									descricao= '". prepara($_POST["descricao"]) ."',
									
									data= '". formata_data($_POST["data"]) ."',
									hora= '". ($_POST["hora"]) ."',
									valido_ate= '". formata_data($_POST["valido_ate"]) ."',
									
									valor= '". formata_valor($_POST["valor"]) ."',
									descricao= '". prepara($_POST["descricao"]) ."'
									
									where id_proposta = '". prepara($_POST[id_proposta]) ."'
									". $str ."
									") or die('1:'.mysql_error());
			if (!$result1) $var++;
			
			$total_arquivos= count($_POST[arquivo]);
			
			if ($total_arquivos>0) {			
				
				//uploads para seus devidos lugares
				$pasta_destino= CAMINHO ."/anexos/propostas/". $_POST[hash] ."/";
				
				if (!is_dir($pasta_destino)) {
					
					$str_log.=' | cria a pasta '. $pasta_destino .' ';
					
					$cria= @mkdir($pasta_destino, 0775, true);
					
					@file_put_contents($pasta_destino . "index.php", "");
					
					if (!$cria) die('Não foi possível enviar os arquivos anexos. Consulte um administrador do sistema e informe o código: ERRO_007 <br><br>');
				}
				
				$pasta_origem= CAMINHO. "anexos_temp/". session_id() ."/";
				
				$apaga_pasta_temp= true;
				
				$i=0;
				while ($_POST[arquivo][$i]) {
					
					$copia[$i]= @copy($pasta_origem . prepara($_POST[arquivo][$i]), $pasta_destino . prepara($_POST[arquivo][$i]));
					
					if (!$copia[$i]) {
						$apaga_pasta_temp= false;
						
						$str_log.=' | não foi possível copiar arquivo '. prepara($_POST[arquivo][$i]) .' em '. $pasta_destino .' ';
					}
					else {
						$str_log.=' | anexa arquivo de '. $pasta_origem . prepara($_POST[arquivo][$i]) .' -> '. $pasta_destino . prepara($_POST[arquivo][$i]) .' / tipo: '. $_POST[tipo][$i] .' / tamanho: '. format_bytes(prepara($_POST[tamanho][$i])) .' ';
						
						$hash_arquivo= gera_auth();
						
						$result3[$i]= mysql_query("insert into arquivos
													(tabela, id, tamanho,
													arquivo, tipo, hash_arquivo, status,
													id_usuario, id_acesso)
													values
													('propostas', '". $_POST[id_proposta] ."', '". prepara($_POST[tamanho][$i]) ."',
													'". prepara($_POST[arquivo][$i]) ."', '". prepara($_POST[tipo][$i]) ."', '". $hash_arquivo ."', '1',
													'". $_SESSION[id_usuario] ."', '". $_SESSION[id_acesso] ."' )
													
													") or die("1: ". mysql_error());
						if (!$result3[$i]) $var++;
							
					}
					
					$i++;
				}
				
				if ($apaga_pasta_temp) limpa_pasta_temp(session_id());
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_POST[id_usuario]), 'notas', 'Edita nota', 'Nota: '. prepara($_POST[id_nota]) .' | '. $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/proposta&acao=e&id_proposta=". $_POST["id_proposta"] ."&erros=". $var);
			
		}//e
		
	}//formProposta
	
	if (isset($_GET["formCliente"])) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		if ($_GET["acao"]=="i") {	
			
			$result_pre= mysql_query("select * from clientes, pessoas
										where clientes.id_pessoa = pessoas.id_pessoa
										and   pessoas.email = '". prepara($_POST[email]) ."'
										and   pessoas.status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Cliente com este e-mail (<strong>'. $_POST[email] .'</strong>) já cadastrado.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into pessoas (nome, nome2, email, cpf_cnpj, status, id_usuario, id_acesso) values
									('". prepara($_POST["nome"]) ."', '". prepara($_POST["nome2"]) ."', '". $_POST["email"] ."', '". $_POST["cpf_cnpj"] ."', '1', '". $_SESSION["id_usuario"] ."', '". $_SESSION["id_acesso"] ."' ) ") or die("1: ". mysql_error());
			if (!$result1) $var++;
			$id_pessoa= mysql_insert_id();
			
			$result2= mysql_query("insert into clientes (id_pessoa, tags, id_usuario) values
									('". $id_pessoa ."', '". prepara($_POST["tags"]) ."', '". $_SESSION["id_usuario"] ."' ) ") or die("2: ". mysql_error());
			if (!$result2) $var++;
			$id_cliente= mysql_insert_id();
			
			$i=0;
			while ($_POST[meta][$i]!='') {
				
				$result_at[$i]= mysql_query("insert into pessoas_meta
											(id_pessoa, meta, valor, id_usuario)
											values
											('". $id_pessoa ."', '". $_POST[meta][$i] ."', '". $_POST[valor][$i] ."', '". $_SESSION["id_usuario"] ."')
										");
				if (!$result_at[$i]) $var++;
				
				$i++;
			}
			
			$str_log_oculto.= ' | senha: '. $_POST[senha];
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $id_usuario, 'clientes', 'Insere cliente', 'Cliente: '. prepara($_POST[nome]) . $str_log, $str_log_oculto, '', '', '', '');
			
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/clientes&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			
			$result_pre= mysql_query("select * from pessoas, clientes
										where pessoas.id_pessoa = clientes.id_pessoa
										and   pessoas.email = '". prepara($_POST[email]) ."'
										and   clientes.id_cliente <> '". prepara($_POST[id_cliente]) ."'
										and   pessoas.status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='Cliente não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Já existe outro cliente cadastrado com este e-mail.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			
			inicia_transacao();
			
			$result1= mysql_query("update pessoas, clientes set
									clientes.tags= '". prepara($_POST["tags"]) ."',
									pessoas.nome= '". prepara($_POST["nome"]) ."',
									pessoas.nome2= '". prepara($_POST["nome2"]) ."',
									pessoas.email= '". prepara($_POST["email"]) ."',
									pessoas.cpf_cnpj= '". prepara($_POST["cpf_cnpj"]) ."'
									where clientes.id_cliente = '". prepara($_POST[id_cliente]) ."'
									and   clientes.id_pessoa = pessoas.id_pessoa
									". $str ."
									") or die('1:'.mysql_error());
			if (!$result1) $var++;
			
			$i=0;
			while ($_POST[meta][$i]!='') {
				
				$result_teste[$i]=  mysql_query("select * from pessoas_meta
											where id_pessoa = '". prepara($_POST[id_pessoa]) ."'
											and   meta = '". $_POST[meta][$i] ."'
										");
				$linhas_teste[$i]= mysql_num_rows($result_teste[$i]);
				
				if ($linhas_teste[$i]==0) {
					$result_in[$i]= mysql_query("insert into pessoas_meta
											(id_pessoa, meta,
											valor, id_usuario)
											values
											('". prepara($_POST[id_pessoa]) ."', '". $_POST[meta][$i] ."',
											'". $_POST[valor][$i] ."', '". $_SESSION["id_usuario"] ."')
										");
					if (!$result_in[$i]) $var++;				
				}
				else {
					$result_at[$i]= mysql_query("update pessoas_meta
												set valor = '". $_POST[valor][$i] ."'
												where id_pessoa = '". prepara($_POST[id_pessoa]) ."'
												and   meta = '". $_POST[meta][$i] ."'
											");
					if (!$result_at[$i]) $var++;
				}
				
				$i++;
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_POST[id_cliente]), 'clientes', 'Edita cliente', 'Cliente: '. prepara($_POST[nome]) .' | '. $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/clientes&erros=". $var);
			
		}//e
		
	}//formCliente
	
	if (isset($_GET["formFornecedor"])) {
		
		//$_POST= array_map('trim', $_POST);
		//$_POST= array_map('htmlentities', $_POST);
		//$_POST= array_map('addslashes', $_POST);
		
		if ($_GET["acao"]=="i") {	
			
			$result_pre= mysql_query("select * from fornecedores, pessoas
										where fornecedores.id_pessoa = pessoas.id_pessoa
										and   pessoas.email = '". prepara($_POST[email]) ."'
										and   pessoas.status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='fornecedor com este e-mail (<strong>'. $_POST[email] .'</strong>) já cadastrado.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into pessoas (nome, nome2, email, cpf_cnpj, status, id_usuario, id_acesso) values
									('". prepara($_POST["nome"]) ."', '". prepara($_POST["nome2"]) ."', '". $_POST["email"] ."', '". $_POST["cpf_cnpj"] ."', '1', '". $_SESSION["id_usuario"] ."', '". $_SESSION["id_acesso"] ."' ) ") or die("1: ". mysql_error());
			if (!$result1) $var++;
			$id_pessoa= mysql_insert_id();
			
			$result2= mysql_query("insert into fornecedores (id_pessoa, tags, id_usuario) values
									('". $id_pessoa ."', '". prepara($_POST["tags"]) ."', '". $_SESSION["id_usuario"] ."' ) ") or die("2: ". mysql_error());
			if (!$result2) $var++;
			$id_fornecedor= mysql_insert_id();
			
			$i=0;
			while ($_POST[meta][$i]!='') {
				
				$result_at[$i]= mysql_query("insert into pessoas_meta
											(id_pessoa, meta, valor, id_usuario)
											values
											('". $id_pessoa ."', '". $_POST[meta][$i] ."', '". $_POST[valor][$i] ."', '". $_SESSION["id_usuario"] ."')
										");
				if (!$result_at[$i]) $var++;
				
				$i++;
			}
			
			$str_log_oculto.= ' | senha: '. $_POST[senha];
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $id_usuario, 'fornecedores', 'Insere fornecedor', 'fornecedor: '. prepara($_POST[nome]) . $str_log, $str_log_oculto, '', '', '', '');
			
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/fornecedores&erros=". $var);
		}
		
		if ($_GET["acao"]=="e") {
			
			$result_pre= mysql_query("select * from pessoas, fornecedores
										where pessoas.id_pessoa = fornecedores.id_pessoa
										and   pessoas.email = '". prepara($_POST[email]) ."'
										and   fornecedores.id_fornecedor <> '". prepara($_POST[id_fornecedor]) ."'
										and   pessoas.status <> '0'
										limit 1
										");
			$num_pre= mysql_num_rows($result_pre);
			
			$erros='';
			if ($_POST[nome]=='') $erros.='fornecedor não pode estar em branco.<br>';
			if ($num_pre>0) $erros.='Já existe outro fornecedor cadastrado com este e-mail.<br>';
			if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
			
			$var=0;
			
			inicia_transacao();
			
			$result1= mysql_query("update pessoas, fornecedores set
									fornecedores.tags= '". prepara($_POST["tags"]) ."',
									pessoas.nome= '". prepara($_POST["nome"]) ."',
									pessoas.nome2= '". prepara($_POST["nome2"]) ."',
									pessoas.email= '". prepara($_POST["email"]) ."',
									pessoas.cpf_cnpj= '". prepara($_POST["cpf_cnpj"]) ."'
									where fornecedores.id_fornecedor = '". prepara($_POST[id_fornecedor]) ."'
									and   fornecedores.id_pessoa = pessoas.id_pessoa
									". $str ."
									") or die('1:'.mysql_error());
			if (!$result1) $var++;
			
			$i=0;
			while ($_POST[meta][$i]!='') {
				
				$result_teste[$i]=  mysql_query("select * from pessoas_meta
											where id_pessoa = '". prepara($_POST[id_pessoa]) ."'
											and   meta = '". $_POST[meta][$i] ."'
										");
				$linhas_teste[$i]= mysql_num_rows($result_teste[$i]);
				
				if ($linhas_teste[$i]==0) {
					$result_in[$i]= mysql_query("insert into pessoas_meta
											(id_pessoa, meta,
											valor, id_usuario)
											values
											('". prepara($_POST[id_pessoa]) ."', '". $_POST[meta][$i] ."',
											'". $_POST[valor][$i] ."', '". $_SESSION["id_usuario"] ."')
										");
					if (!$result_in[$i]) $var++;				
				}
				else {
					$result_at[$i]= mysql_query("update pessoas_meta
												set valor = '". $_POST[valor][$i] ."'
												where id_pessoa = '". prepara($_POST[id_pessoa]) ."'
												and   meta = '". $_POST[meta][$i] ."'
											");
					if (!$result_at[$i]) $var++;
				}
				
				$i++;
			}
			
			logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_POST[id_fornecedor]), 'fornecedores', 'Edita fornecedor', 'fornecedor: '. prepara($_POST[nome]) .' | '. $str_log, $str_log_oculto, '', '', '', '');
			
			finaliza_transacao($var);
			
			header("location: ./?pagina=acesso/fornecedores&erros=". $var);
			
		}//e
		
	}//formUsuario
	
}//fim Administrador

//Qualquer usuário no sistema
if (pode("1234", $_SESSION["perfil"])) {
	
	
	if (isset($_GET["formTema"])) {
		
		$erros='';
		if ($_POST[tema]=='') $erros.='Tema não pode estar em branco.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update usuarios
								set usuarios.tema= '". prepara($_POST["tema"]) ."'
								where usuarios.id_usuario = '". $_SESSION[id_usuario] ."'
								") or die(mysql_error());
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, 0, 'temas', 'Altera tema', 'Tema: '. prepara($_POST[tema]), $str_log_oculto, '', '', '', '');
		
		$_SESSION[tema]= prepara($_POST[tema]);
		@setcookie ("tema", prepara($_POST[tema]), ((time()+3600)*24)*1000);
		
		header("location: ./?pagina=acesso/temas&erros=". $var);
	}
	
	if (isset($_GET["formDadosPessoais"])) {
		
		$result_pre= mysql_query("select * from usuarios, pessoas
									where pessoas.email = '". prepara($_POST[email]) ."'
									and   usuarios.id_pessoa = pessoas.id_pessoa
									and   usuarios.id_usuario <> '". $_SESSION[id_usuario] ."'
									limit 1
									");
		$num_pre= mysql_num_rows($result_pre);
		
		$erros='';
		if ($_POST[nome]=='') $erros.='Nome não pode estar em branco.<br>';
		if ($_POST[email]=='') $erros.='E-mail não pode estar em branco.<br>';
		if ($num_pre>0) $erros.='Já existe outro usuário cadastrado com este e-mail.<br>';
		if ($erros!='') die($erros ."<a href='javascript:history.back(-1);'>&laquo; voltar</a>");
		
		$var=0;
		
		inicia_transacao();
		
		if ($_POST["senha"]!="") {
			$linha_senha= ", usuarios.senha= '". md5($_POST["senha"]) ."', usuarios.senha_sem= '". prepara($_POST["senha"]) ."' ";
			
			$str_log .= ' | altera senha';
			$str_log_oculto .= ' | altera senha para '. addslashes($_POST[senha]);
		}
		
		$result1= mysql_query("update pessoas, usuarios set
								pessoas.nome= '". prepara($_POST["nome"]) ."',
								pessoas.email= '". prepara($_POST["email"]) ."',
								pessoas.cpf_cnpj= '". prepara($_POST["cpf_cnpj"]) ."'
								
								". $linha_senha ."
								where usuarios.id_usuario = '". $_SESSION[id_usuario] ."'
								and   pessoas.id_pessoa = usuarios.id_pessoa
								") or die(mysql_error());
		if (!$result1) $var++;
				
		if ($_FILES["foto"]["name"]!="") {
			$caminho= CAMINHO . "". $_SESSION[id_usuario] ."_". $_FILES["foto"]["name"];
			move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
			
			$result_atualiza= mysql_query("update usuarios set foto = '$caminho'
											where id_usuario = '". $_SESSION[id_usuario] ."'
											limit 1
											") or die(mysql_error());
			if (!$result_atualiza) $var++;
			
			$_SESSION[foto]=$caminho;
			
			$str_log .= ' | envia foto: '. $caminho;
		}
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, $_SESSION[id_usuario], 'dados_pessoais', 'Altera dados', $str_log, $str_log_oculto, '', '', '', '');
		
		finaliza_transacao($var);
		
		header("location: ./?pagina=acesso/dados&erros=". $var);
	}
}

if (pode("123", $_SESSION["perfil"])) {
	
	if (isset($_GET["uploadArquivos"])) {
		
		//error_reporting(E_ALL | E_STRICT);
		require('includes/UploadHandler.php');
		$upload_handler = new UploadHandler();
		
	}

}



?>