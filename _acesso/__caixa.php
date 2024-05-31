<?
if (pode("12", $_SESSION["perfil"])) {
	
	$subtit= "Listando todas";
	
	if ($_GET["status"]!="") $status= $_GET["status"];
	if ($_POST["status"]!="") $status= $_POST["status"];
	if ($status=='') $status='1';
	
	$str.= "and   notas.status = '". $status ."' ";	
	$subtit_status= ' - '. pega_status_generico($status);

	$result= mysql_query("select *, notas.id_nota as id_nota from notas
							where 1 = 1
							". $str ."
							order by notas.data desc
							") or die('1:'.mysql_error());
	
	$num= 9999;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	
	if ($_GET["num_pagina"]=="") $num_pagina= 1;
	else $num_pagina= $_GET["num_pagina"];
	$num_pagina--;
	
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, notas.id_nota as id_nota from notas 
							where 1 = 1
							". $str ."
							order by notas.data desc
							limit $inicio, $num
							");
	
?>
	
	<div class="span12">
		<div class="page-header">
			<h1>Fluxo de caixa</h1>
		</div>
		
		<p><strong><?=$total;?></strong> movimentaçõe(s).</p>
		
		<div class="btn-group">
			<a class="btn btn-primary" href="./?pagina=acesso/nota&amp;acao=i">Nova nota</a>
		</div>
		<br /><br />
		
		<?php
		if ($total==0) {
		?>
		<p>Nenhuma nota encontrado.</p>
		<?php
		}
		else {
		?>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/caixa";
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
		            <th width="30%" align="left">Quem</th>
		            <th width="26%" align="left">Infos</th>
		            <th width="19%">Valor</th>
		            <th width="22%">Ações</th>
		        </tr>
		    </thead>
		    <tbody>
				<?
		        $i=0;
		        while ($rs= mysql_fetch_object($result)) {
		        	if ($rs->tipo_nota=='1') $cl='text-success';
		        	else $cl='text-danger';
		        ?>
		        <tr id="linha_<?=$rs->id_nota;?>">
		            <td>
		            <? echo $rs->id_nota; ?>
		            </td>
		            <td>
			            
			            	<?= pega_pessoa($rs->id_pessoa, 'nome'); ?> <br/>
							<small><strong><?= pega_pessoa($rs->id_pessoa, 'cpf_cnpj'); ?></strong>
			            </small>
		            </td>
		            <td>
		            	<small>
		            	<?= desformata_data($rs->data); ?> <br/>
		            	
		            	Nota nº <strong><?= $rs->numero; ?></strong> <br/>
			            
			            <small><em><?= $rs->descricao;?></em></small>
			            
		            	</small>
		            </td>
		            <td>
		            	<strong class="<?=$cl;?>">R$<?= fnum($rs->valor); ?></strong>
		            	<?
		            	$result_anexo= mysql_query("select count(id_arquivo) as total
		            								from arquivos
		            								where id = '". $rs->id_nota ."'
		            								and   tabela = 'notas'
		            								");
		            	$rs_anexo= mysql_fetch_object($result_anexo);
		            	
		            	if ($rs_anexo->total>0) echo '<br /><i title="Contém anexo" class="tt glyphicon glyphicon-file"></i> <br />';
		            	?>
		            </td>
		            <td>
		                <? if ( (pode("1", $_SESSION["perfil"])) || ( (pode("2", $_SESSION["perfil"])) && ($_SESSION[id_nota]!=$rs->id_nota)) ) { ?>
		                <a class="btn btn-xs btn-success" href="./?pagina=acesso/nota&amp;acao=e&amp;id_nota=<?= $rs->id_nota; ?>">
		                	<i class="icon-white icon-pencil"></i> Editar
		                </a>
		                <? } ?>
		                
		                <a class="btn btn-xs btn-danger" href="javascript:apagaLinha('notaExcluir','<?=($rs->id_nota);?>');" onclick="return confirm('Tem certeza que deseja suspender o nota?');">
		                    <i class="icon-white icon-trash"></i> Apagar
		                </a>
		            </td>
		        </tr>
		        <? $i++; } ?>
		    </tbody>
		</table>
		
		<?
		if ($num_paginas > 1) {
			$link_pagina= "acesso/notas";
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