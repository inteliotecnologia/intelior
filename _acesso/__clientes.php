<?
if (pode("12", $_SESSION["perfil"])) {
	
	$subtit= "Listando todos";
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status=='') $status='1';
	
	$str.= "and   pessoas.status = '". $status ."' ";	
	$subtit_status= ' - '. pega_status_generico($status);

	$result= mysql_query("select *, clientes.id_cliente as id_cliente from pessoas, clientes
							where 1 = 1
							and   pessoas.id_pessoa = clientes.id_pessoa
							". $str ."
							order by clientes.id_cliente asc
							") or die('1:'.mysql_error());
	
	$num= 100;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, clientes.id_cliente as id_cliente from pessoas, clientes 
							where 1 = 1
							and   pessoas.id_pessoa = clientes.id_pessoa
							". $str ."
							order by clientes.id_cliente asc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		<div class="page-header">
			<h1>Clientes <small><?=$subtit . $subtit_status;?></small></h1>
		</div>
		
		<p><strong><?=$total;?></strong> clientes.</p>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/cliente&amp;acao=i">Novo cliente</a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
				Listar
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="./?pagina=acesso/clientes&amp;status=1">Ativos</a></li>
				<li><a href="./?pagina=acesso/clientes&amp;status=2">Suspensos</a></li>
			</ul>
		</div>
		<br /><br />
		
		<?php
		if ($total==0) {
		?>
		<p>Nenhum cliente encontrado.</p>
		<?php
		}
		else {
		?>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/clientes";
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
		            <th width="26%" align="left">Cliente</th>
		            <th width="20%" align="left">Contato</th>
		            <th width="29%">Tags</th>
		            <th width="22%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysql_fetch_object($result)) {
		        ?>
		        <tr id="linha_<?=$rs->id_cliente;?>">
		            <td>
		            <? echo $rs->id_cliente; ?>
		            </td>
		            <td><abbr class="tt" title="Cadastrado por <?= pega_usuario($rs->id_usuario, 'nome'); ?> em <?= desformata_data(pega_pessoa_meta($rs->id_pessoa, 'data_cadastro')); ?>"><?= $rs->nome; ?></abbr> <br />
		            
		            <small><?= $rs->nome2; ?> <br />
		            <strong><?=$rs->cpf_cnpj; ?></strong></small>
		            </td>
		            <td>
			            <small>
			            <strong><?= pega_pessoa_meta($rs->id_pessoa, 'contato'); ?></strong> <br />
			            <?= pega_pessoa_meta($rs->id_pessoa, 'telefone'); ?> <br />
		            	<?= $rs->email; ?></small>
		            </td>
		            <td><?= $rs->tags; ?></td>
		            <td>
		                <? if ( (pode("1", $_SESSION["perfil"])) || ( (pode("2", $_SESSION["perfil"])) && ($_SESSION[id_cliente]!=$rs->id_cliente)) ) { ?>
		                <a class="btn btn-xs btn-success" href="./?pagina=acesso/cliente&amp;acao=e&amp;id_cliente=<?= $rs->id_cliente; ?>">
		                	<i class="glyphicon glyphicon-white glyphicon-pencil"></i> Editar
		                </a>
		                <? } ?>
		                
		                <? if ($_SESSION[id_cliente]!=$rs->id_cliente) { ?>
		                <? if ($status=='1') { ?>
		                <a class="btn btn-xs btn-danger" href="javascript:apagaLinha('clienteExcluir','<?=($rs->id_cliente);?>');" onclick="return confirm('Tem certeza que deseja suspender o cliente?');">
		                    <i class="glyphicon glyphicon-white glyphicon-trash"></i> Suspender
		                </a>
		                <? } else { ?>
		                <a class="btn btn-xs btn-info" href="javascript:apagaLinha('clienteReativar','<?=($rs->id_cliente);?>');" onclick="return confirm('Tem certeza que deseja reativar o cliente?');">
		                    <i class="glyphicon glyphicon-white glyphicon-share"></i> Reativar
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
			$link_pagina= "acesso/clientes";
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