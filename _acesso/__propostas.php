<?
if (pode("12", $_SESSION["perfil"])) {
	
	$subtit= "Listando todas";
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status=='') $status='1';
	
	$str.= "and   propostas.status = '". $status ."' ";	
	$subtit_status= ' - '. pega_status_generico($status);

	$result= mysql_query("select *, propostas.id_proposta as id_proposta from propostas
							where 1 = 1
							". $str ."
							order by propostas.id_proposta desc
							") or die('1:'.mysql_error());
	
	$num= 100;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, propostas.id_proposta as id_proposta from propostas 
							where 1 = 1
							". $str ."
							order by propostas.id_proposta desc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		<div class="page-header">
			<h1>Propostas <small><?=$subtit . $subtit_status;?></small></h1>
		</div>
		
		<p><strong><?=$total;?></strong> propostas.</p>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/proposta&amp;acao=i">Nova proposta</a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
				Listar
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="./?pagina=acesso/propostas&amp;status=1">Ativos</a></li>
				<li><a href="./?pagina=acesso/propostas&amp;status=2">Suspensos</a></li>
			</ul>
		</div>
		<br /><br />
		
		<?php
		if ($total==0) {
		?>
		<p>Nenhuma proposta encontrada.</p>
		<?php
		}
		else {
		?>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/propostas";
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
		            <th width="8%">#</th>
		            <th width="17%" align="left">Quem</th>
		            <th width="33%" align="left">Proposta</th>
		            <th width="10%">Valor</th>
		            <th width="25%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysql_fetch_object($result)) {
		        	if ($rs->tipo_proposta=='1') $cl='text-success';
		        	else $cl='text-danger';
		        ?>
		        <tr id="linha_<?=$rs->id_proposta;?>">
		            <td>
		            <? echo formata_saida($rs->num, 3) .'/'. $rs->ano; ?>
		            </td>
		            <td>
		            	<small><strong><?= pega_pessoa($rs->id_pessoa, 'nome');?></strong> <br/>
		            	<?= pega_pessoa($rs->id_pessoa, 'cpf_cnpj');?>
		            	
		            	<br />
		            	em <?= desformata_data($rs->data); ?>
		            	</small>
		            	<br />
		            	
		            	
		            </td>
		            <td>
			            <small>
			            	
							<small><?= $rs->proposta; ?></small>
							
							<?
			            	$result_anexo= mysql_query("select count(id_arquivo) as total
			            								from arquivos
			            								where id = '". $rs->id_proposta ."'
			            								and   tabela = 'propostas'
			            								");
			            	$rs_anexo= mysql_fetch_object($result_anexo);
			            	
			            	if ($rs_anexo->total>0) echo '<br /><i title="Contém anexo" class="tt glyphicon glyphicon-file"></i> <br />';
			            	?>
			            	
			            </small>
		            </td>
		            <td class="<?=$cl;?>">
		            	<? if ($rs->valor!='0') { ?>
		            	R$<?= fnum($rs->valor); ?>
		            	<? } ?>
		            </td>
		            <td>
		                <a class="btn btn-xs btn-info" target="_blank" href="./index3.php?pagina=acesso/proposta_gera&amp;id_proposta=<?= $rs->id_proposta; ?>">
		                	<i class="glyphicon glyphicon-share-alt glyphicon-white"></i> Gerar
		                </a>
		                
		                <? if ( (pode("1", $_SESSION["perfil"])) || ( (pode("2", $_SESSION["perfil"])) && ($_SESSION[id_proposta]!=$rs->id_proposta)) ) { ?>
		                <a class="btn btn-xs btn-success" href="./?pagina=acesso/proposta&amp;acao=e&amp;id_proposta=<?= $rs->id_proposta; ?>">
		                	<i class="glyphicon glyphicon-white glyphicon-pencil"></i> Editar
		                </a>
		                <? } ?>
		                
		                <a class="btn btn-xs btn-danger" href="javascript:apagaLinha('propostaExcluir','<?=($rs->id_proposta);?>');" onclick="return confirm('Tem certeza que deseja suspender o proposta?');">
		                    <i class="glyphicon glyphicon-white glyphicon-trash"></i> Excluir
		                </a>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/propostas";
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