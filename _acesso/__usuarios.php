<?
if (pode("12", $_SESSION["perfil"])) {
	
	$subtit= "Listando todos";
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status=='') $status='1';
	
	$str.= "and   pessoas.status = '". $status ."' ";	
	$subtit_status= ' - '. pega_status_generico($status);

	$result= mysql_query("select *, usuarios.id_usuario as id_usuario from pessoas, usuarios
							where 1 = 1
							and   pessoas.id_pessoa = usuarios.id_pessoa
							". $str ."
							order by usuarios.id_usuario asc
							") or die('1:'.mysql_error());
	
	$num= 100;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, usuarios.id_usuario as id_usuario, usuarios.id_usuario_criou as id_usuario_criou from pessoas, usuarios 
							where 1 = 1
							and   pessoas.id_pessoa = usuarios.id_pessoa
							". $str ."
							order by usuarios.id_usuario asc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		<div class="page-header">
			<h1>Usuários <small><?=$subtit . $subtit_status;?></small></h1>
		</div>
		
		<p><strong><?=$total;?></strong> usuários.</p>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/usuario&amp;acao=i">Novo usuário</a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
				Listar
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="./?pagina=acesso/usuarios&amp;status=1">Ativos</a></li>
				<li><a href="./?pagina=acesso/usuarios&amp;status=2">Suspensos</a></li>
			</ul>
		</div>
		<br /><br />
		
		<?php
		if ($total==0) {
		?>
		<p>Nenhum usuário encontrado.</p>
		<?php
		}
		else {
		?>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/usuarios";
		?>
			<div class="pagination pagination-centered">
				<ul>
					<?
					/*if ($num_pagina > 0) {
						echo "<li><a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $num_pagina. "\">&laquo; Anterior</a></li>";
					}*/
				
					for ($i=0; $i<$num_paginas; $i++) {
						$link = $i + 1;
						if ($num_pagina==$i)
							echo "<li class='disabled'> <a href='#'>". $link ."</a></li>";
						else
							echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $link. "\">". $link ."</a></li>";
					}
					/*
					if ($num_pagina < ($num_paginas - 1)) {
						$mais = $num_pagina + 1;
						echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $mais ."\">Pr&oacute;xima &raquo;</a></li>";
					}*/
					?>
				</ul>
		    </div>
		<? } ?>
		
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">#</th>
		            <th width="26%" align="left">Nome</th>
		            <th width="20%" align="left">E-mail</th>
		            <th width="17%">Tipo</th>
		            <th width="12%"># acessos</th>
		            <th width="22%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysql_fetch_object($result)) {
		        ?>
		        <tr id="linha_<?=$rs->id_usuario;?>">
		            <td align="center">
		            <?php if ($_SESSION[perfil]=='1') { ?>
		            <a href="./?pagina=acesso/log&amp;id_usuario=<?=$rs->id_usuario;?>"><?= $rs->id_usuario; ?></a>
		            <?php
		            }
		            else echo $rs->id_usuario; ?>
		            </td>
		            <td><abbr class="tt" title="Cadastrado por <?= pega_usuario($rs->id_usuario_criou, 'nome'); ?> em <?= desformata_data($rs->data_cadastro) .' '. $rs->hora_cadastro; ?>"><?= $rs->nome; ?></abbr>
		            </td>
		            <td><?= $rs->email; ?></td>
		            <td><small><?= pega_perfil_resumido($rs->perfil); ?></small></td>
		            <td>
		            <?
		            $result_qt= mysql_query("select count(id_acesso) as total from acessos
		            							where id_usuario = '". $rs->id_usuario ."'
		            							");
		            $rs_qt= mysql_fetch_object($result_qt);
		            ?>
		            <small><?=$rs_qt->total;?> acessos</small>
		            </td>
		            <td>
		                <? if ( (pode("1", $_SESSION["perfil"])) || ( (pode("2", $_SESSION["perfil"])) && ($_SESSION[id_usuario]!=$rs->id_usuario)) ) { ?>
		                <a class="btn btn-xs btn-success" href="./?pagina=acesso/usuario&amp;acao=e&amp;id_usuario=<?= base64_encode($rs->id_usuario); ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                <? } ?>
		                
		                <? if ($_SESSION[id_usuario]!=$rs->id_usuario) { ?>
		                <? if ($status=='1') { ?>
		                <a class="btn btn-xs btn-danger" href="javascript:apagaLinha('usuarioExcluir','<?=($rs->id_usuario);?>');" onclick="return confirm('Tem certeza que deseja suspender o usuário?');">
		                    <i class="icon-white icon-trash"></i> Suspender
		                </a>
		                <? } else { ?>
		                <a class="btn btn-xs btn-info" href="javascript:apagaLinha('usuarioReativar','<?=($rs->id_usuario);?>');" onclick="return confirm('Tem certeza que deseja reativar o acesso?');">
		                    <i class="icon-white icon-share"></i> Reativar
		                </a>
		                <? } ?>
		                <? } ?>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/usuarios";
		?>
			<div class="pagination pagination-centered">
				<ul>
					<?
					/*if ($num_pagina > 0) {
						echo "<li><a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $num_pagina. "\">&laquo; Anterior</a></li>";
					}*/
				
					for ($i=0; $i<$num_paginas; $i++) {
						$link = $i + 1;
						if ($num_pagina==$i)
							echo "<li class='disabled'> <a href='#'>". $link ."</a></li>";
						else
							echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $link. "\">". $link ."</a></li>";
					}
					/*
					if ($num_pagina < ($num_paginas - 1)) {
						$mais = $num_pagina + 1;
						echo "<li> <a href=\"./?pagina=". $link_pagina ."&amp;num_pagina=". $mais ."\">Pr&oacute;xima &raquo;</a></li>";
					}*/
					?>
				</ul>
		    </div>
		<? } ?>
		<? } ?>
	</div>
	

<? } ?>