<?
if (pode("1", $_SESSION["perfil"])) {
	
	if ($_GET[id_acesso]!='') {
		$str.= "and   id_acesso = '". $_GET[id_acesso] ."' ";
		$subtit= "Acesso nº ". $_GET[id_acesso] ." | ";
	}
	if ($_GET[id_usuario]!='') {
		$str.= "and   id_usuario = '". $_GET[id_usuario] ."' ";
		$subtit= "<b>Usuário:</b> ". pega_usuario($_GET[id_usuario], 'nome') ." | ";
	}
	if ($_GET[data]!='') {
		$str.= "and   data = '". $_GET[data] ."' ";
		$subtit= "<b>Data:</b> ". desformata_data($_GET[data]) ." | ";
	}
	if ($_GET[ip]!='') {
		$str.= "and   ip = '". $_GET[ip] ."' ";
		$subtit= "<b>IP:</b> ". $_GET[ip] ." | ";
	}
	if ($_GET[area]!='') {
		$str.= "and   area = '". $_GET[area] ."' ";
		$subtit= "<b>Área:</b> ". $_GET[area] ." | ";
	}
	
	$sql= "select * from logs
			where 1=1
			$str
			order by id_log desc";
	
	$result= mysql_query($sql) or die(mysql_error());
	$total_antes= mysql_num_rows($result);
		
	$num= 100;
	$total_linhas = mysql_num_rows($result);
	$num_paginas = ceil($total_linhas/$num);
	if (!isset($_GET[num_pagina])) $num_pagina= 1;
	else $num_pagina= $_GET[num_pagina];
	$num_pagina--;
	
	$inicio= $num_pagina*$num;
	
	$result= mysql_query($sql ." limit $inicio, $num") or die(mysql_error());

?>
	<div class="span12">
		<div class="page-header">
			<h1>Logs</h1>
			<h4><?= substr($subtit, 0, -2); ?></h4>
		</div>
		<br />
		
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">ID #</th>
		            <th width="5%" align="left">Acesso</th>
		            <th width="15%" align="left">Usuário</th>
		            <th width="10%">Data/hora</th>
		            <th width="22%">Área</th>
		            <th width="53%">Detalhamento</th>
		        </tr>
		    </thead>
		    <tbody>
			<?
			$i=0;
			while ($rs= mysql_fetch_object($result)) {
			?>
			<tr>
				<td><?= $rs->id_log; ?></td>
				<td>
					<a href="./?pagina=acesso/log&amp;id_acesso=<?= $rs->id_acesso; ?>"><?= $rs->id_acesso; ?></a>
				</td>
				<td>
					<a href="./?pagina=acesso/log&amp;id_usuario=<?= $rs->id_usuario; ?>"><?= pega_usuario($rs->id_usuario, 'nome'); ?></a>
				</td>
				<td>
					<small><a href="./?pagina=acesso/log&amp;data=<?= $rs->data; ?>"><?= desformata_data($rs->data) .'</a><br /> '. $rs->hora; ?></small>
				</td>
				<td>
					<strong><a href="./?pagina=acesso/log&amp;area=<?= $rs->area; ?>"><?= $rs->area; ?></a></strong> <br />
					<small><?= $rs->acao; ?></small>
		        </td>
		        <td>
		        	<small><?= (nl2br(trim($rs->descricao))); ?>
					
					<? if ($rs->area=='login') { ?>
					<br /><b>IP:</b> <?= $rs->ip; ?>
					<br /><b>User agent:</b> <?= $rs->user_agent; ?>
					<? } ?>
					
					</small>
		        </td>
			</tr>
			<? $i++; } ?>
			</tbody>
		</table>
		<br />
<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		echo "<div class='pagination pagination-centered'> <ul>"; 
		
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i) $texto_paginacao .= "<li class='disabled'><a href='#'>". $link ."</a></li>";
			else $texto_paginacao .=  "<li><a href=\"./?pagina=acesso/log&id_acesso=". $_GET[id_acesso] ."&id_usuario=". $_GET[id_usuario] ."&data=". $_GET[data] ."&ip=". $_GET[ip] ."&num_pagina=". $link ."\">". $link ."</a></li> ";
		}

		echo $texto_paginacao .'</ul>';
	}
}
?>
<br /><br /><br /><br /><br />
<? } ?>