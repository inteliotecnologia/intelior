<?
if (pode("1", $_SESSION["perfil"])) {
	
	$sql= "select * from acessos order by id_acesso desc";
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
			<h1>Acessos ao sistema <small>Listando todos</small></h1>
		</div>
		<br />
		
		<table cellspacing="0" width="100%" class="table table-striped table-hover">
			<thead>
		        <tr>
		            <th width="5%">#</th>
		            <th width="20%" align="left">Usuário</th>
		            <th width="10%" align="left">Data/hora</th>
		            <th width="20%">IP</th>
		            <th width="40%">User agent</th>
		        </tr>
		    </thead>
		    <tbody>
			<?
			$i= 0;
			while ($rs= mysql_fetch_object($result)) {
			?>
			<tr>
				<td><a href="./?pagina=acesso/log&amp;id_acesso=<?= $rs->id_acesso; ?>"><?= $rs->id_acesso; ?></a></td>
				<td><a href="./?pagina=acesso/log&amp;id_usuario=<?= $rs->id_usuario; ?>"><?= pega_nome_usuario($rs->id_usuario); ?></a>
				</td>
				<td><small><a href="./?pagina=acesso/log&amp;data=<?= $rs->data; ?>"><?= desformata_data($rs->data) .'</a><br /> '. $rs->hora; ?></small></td>
				<td>
				<?
		        if ($rs->ip!="") {
					echo '<a href="./?pagina=acesso/log&amp;ip='. $rs->ip .'">'. $rs->ip .'</a>';
					if ($rs->ip!=$rs->ip_reverso)
						echo " <small>(". $rs->ip_reverso .")</small>";
				}
				else
					echo "anônimo";
				?>
		        </td>
		        <td><small><?= $rs->user_agent; ?></small></td>
			</tr>
			<? $i++; } ?>
			</tbody>
		</table>
		<br />
<?
$texto_paginacao='';
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		echo "<div class='pagination pagination-centered'> <ul>"; 
		
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i) $texto_paginacao .= "<li class='disabled'><a href='#'>". $link ."</a></li>";
			else $texto_paginacao .=  "<li><a href=\"./?pagina=acesso/acessos&num_pagina=". $link ."\">". $link ."</a></li> ";
		}

		echo $texto_paginacao .'</ul>';
	}
}
?>
<br /><br /><br /><br /><br />
<? } ?>