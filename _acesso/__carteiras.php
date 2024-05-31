<?
if (pode("12", $_SESSION["perfil"])) {
	
	$subtit= "Listando todas";
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status=='') $status='1';
	
	$str.= "and   carteiras.status = '". $status ."' ";	
	$subtit_status= ' - '. pega_status_generico($status);

	$result= mysql_query("select *, carteiras.id_carteira as id_carteira from carteiras
							where 1 = 1
							". $str ."
							order by carteiras.id_carteira asc
							") or die('1:'.mysql_error());
	
	$num= 100;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, carteiras.id_carteira as id_carteira from carteiras 
							where 1 = 1
							". $str ."
							order by carteiras.id_carteira asc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		<div class="page-header">
			<h1>Carteiras <small><?=$subtit . $subtit_status;?></small></h1>
		</div>
		
		<p><strong><?=$total;?></strong> carteiras.</p>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/carteira&amp;acao=i">Nova carteira</a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
				Listar
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="./?pagina=acesso/carteiras&amp;status=1">Ativos</a></li>
				<li><a href="./?pagina=acesso/carteiras&amp;status=2">Suspensos</a></li>
			</ul>
		</div>
		<br /><br />
		
		<?php
		if ($total==0) {
		?>
		<p>Nenhuma carteira encontrada.</p>
		<?php
		}
		else {
		?>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/carteiras";
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
		            <th width="35%" align="left">Carteira</th>
		            <th width="30%" align="left">Saldo</th>
		            <th width="30%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysql_fetch_object($result)) {
		        ?>
		        <tr id="linha_<?=$rs->id_carteira;?>">
		            <td>
		            <? echo $rs->id_carteira; ?>
		            </td>
		            <td><abbr class="tt" title="Cadastrado por <?= pega_usuario($rs->id_usuario, 'nome'); ?>"><?= $rs->carteira; ?></abbr> &nbsp;<small><strong><?=$rs->cpf_cnpj; ?></strong></small> <br />
		            <small><em><?=$rs->nome2;?></em></small>
		            </td>
		            <td>R$<?= fnum($rs->saldo); ?></td>
		            <td>
		                
		                <a class="btn btn-xs btn-success" href="./?pagina=acesso/carteira&amp;acao=e&amp;id_carteira=<?= $rs->id_carteira; ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                
		                <? if ($status=='1') { ?>
		                <a class="btn btn-xs btn-danger" href="javascript:apagaLinha('carteiraExcluir','<?=($rs->id_carteira);?>');" onclick="return confirm('Tem certeza que deseja suspender a carteira?');">
		                    <i class="icon-white icon-trash"></i> Suspender
		                </a>
		                <? } else { ?>
		                <a class="btn btn-xs btn-info" href="javascript:apagaLinha('carteiraReativar','<?=($rs->id_carteira);?>');" onclick="return confirm('Tem certeza que deseja reativar a carteira?');">
		                    <i class="icon-white icon-share"></i> Reativar
		                </a>
		                <? } ?>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/carteiras";
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