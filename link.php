<?
require_once("includes/funcoes.php");

if (!$conexao)
	require_once("includes/conexao.php");

header("Content-type: text/html; charset=utf-8", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_GET["carregaPagina"])) {
	require_once("index2.php");
}
if (isset($_GET["carregaPaginaInterna"])) {
	require_once("index2.php");
}

//Administradores ou setor nível 1
if (pode("1", $_SESSION["perfil"])) {
	if ($_GET["chamada"]=="usuarioExcluir") {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update pessoas, usuarios
								set pessoas.status = '2'
								where usuarios.id_usuario= '". prepara($_GET["id"]) ."'
								and   usuarios.id_usuario <> '". $_SESSION[id_usuario] ."'
								and   usuarios.status <> '2'
								and   pessoas.id_pessoa = usuarios.id_pessoa
								". $str ."
								
								") or die(mysql_error());
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET["id"]), 'usuarios', 'Suspende usuário', 'Usuário: '. pega_usuario(prepara($_GET[id]), 'nome'), $str_log_oculto, '', '', '', '');
			
		echo $var;
	}
	
	if ($_GET["chamada"]=="usuarioReativar") {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update pessoas, usuarios
								set pessoas.status = '1'
								where usuarios.id_usuario= '". prepara($_GET["id"]) ."'
								and   usuarios.id_usuario <> '". $_SESSION[id_usuario] ."'
								and   pessoas.id_pessoa = usuarios.id_pessoa
								and   usuarios.status <> '1'
								". $str ."
								
								") or die(mysql_error());
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET["id"]), 'usuarios', 'Reativa usuário', 'Usuário: '. pega_usuario(prepara($_GET[id]), 'nome'), $str_log_oculto, '', '', '', '');
		
		echo $var;
	}
	
	if ($_GET["chamada"]=="clienteExcluir") {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update pessoas, clientes
								set pessoas.status = '2'
								where clientes.id_cliente= '". prepara($_GET["id"]) ."'
								and   pessoas.id_pessoa = clientes.id_pessoa
								") or die(mysql_error());
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET["id"]), 'clientes', 'Suspende cliente', 'Cliente: '. pega_cliente(prepara($_GET[id]), 'slug'), $str_log_oculto, '', '', '', '');
			
		echo $var;
	}
	
	if ($_GET["chamada"]=="clienteReativar") {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update pessoas, clientes
								set pessoas.status = '1'
								where clientes.id_cliente= '". prepara($_GET["id"]) ."'
								and   pessoas.id_pessoa = clientes.id_pessoa
								") or die(mysql_error());
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET["id"]), 'clientes', 'Reativa cliente', 'Cliente: '. pega_cliente(prepara($_GET[id]), 'slug'), $str_log_oculto, '', '', '', '');
			
		echo $var;
	}
	
	if ($_GET["chamada"]=="propostaExcluir") {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update propostas
								set status = '2'
								where id_proposta= '". prepara($_GET["id"]) ."'
								") or die(mysql_error());
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET["id"]), 'propostas', 'Apaga proposta', 'Proposta: '. $_GET[id], $str_log_oculto, '', '', '', '');
			
		echo $var;
	}
}

//Todos logados
if (pode("1234", $_SESSION["perfil"])) {
	
	if ($_GET["chamada"]=="carregaPessoa") {
		
		if ($_GET[tipo_pessoa]=='1')
			$sql= "select * from  pessoas, clientes
					where pessoas.id_pessoa = clientes.id_pessoa
					order by pessoas.nome asc";
		elseif  ($_GET[tipo_pessoa]=='2')
			$sql= "select * from  pessoas, fornecedores
					where pessoas.id_pessoa = fornecedores.id_pessoa
					order by pessoas.nome asc";
		elseif  ($_GET[tipo_pessoa]=='3')
			$sql= "select * from  pessoas, usuarios
					where pessoas.id_pessoa = usuarios.id_pessoa
					order by pessoas.nome asc";
		?>
		<select class="form-control" id="id_pessoa" name="id_pessoa" required="required">
			<option value="">- selecione -</option>
			<?
			$result_sel= mysql_query($sql) or die(mysql_error());
			while ($rs_sel= mysql_fetch_object($result_sel)) {
			?>
			<option class="tt" <? if ($_GET[id_pessoa]==$rs_sel->id_pessoa) echo 'selected="selected"'; ?> value="<?=$rs_sel->id_pessoa;?>"><?=$rs_sel->nome;?></option>
			<? } ?>
		</select>
		<?
		
	}
	
	if ($_GET["chamada"]=="salvaLog") {
		logs($_SESSION[id_acesso], $_SESSION[id_usuario], $_SESSION[perfil], 1, prepara($_GET[id_emissao]), prepara($_GET[area]), prepara($_GET[acao]), $str_log, $str_log_oculto, '', '', '', '');
	}
	
	if ($_GET["chamada"]=="arquivoExcluir") {
		
		$apagar= @unlink($_GET["src"]);
		
		if ($apagar) {
			echo "0";
			
			$var=0;
			inicia_transacao();
			
			if (pode("1", $_SESSION["perfil"])) $id_usuario= prepara($_GET[id_usuario]);
			else $id_usuario= $_SESSION[id_usuario];
			
			$result= mysql_query("update usuarios set foto= ''
								where id_usuario= '". $id_usuario ."'
								limit 1
								");
			if (!$result) $var++;
			finaliza_transacao($var);
		}
		else echo "1";
		
		//echo $var;
	}
	
}//fim todos

?>