<? if ($_SESSION["id_usuario"]!="") { ?>
	<div class="row-fluid">
		<div class="span6 well">
			<?php
			$result_num= mysql_query("select count(id_emissao) as total
										from  emissoes
										where id_emissao_pai = '0'
										");
			$rs_num= mysql_fetch_object($result_num);
			mysql_free_result($result_num);
			
			$result_num1= mysql_query("select count(id_emissao) as total
										from  emissoes
										where id_emissao_pai = '0'
										and   id_tipo_documento = '1'
										");
			$rs_num1= mysql_fetch_object($result_num1);
			mysql_free_result($result_num1);
			
			$result_num2= mysql_query("select count(id_emissao) as total
										from  emissoes
										where id_emissao_pai = '0'
										and   id_tipo_documento = '2'
										");
			$rs_num2= mysql_fetch_object($result_num2);
			mysql_free_result($result_num2);
			
			$result_mov= mysql_query("select count(id_emissao) as total
										from  emissoes
										where id_emissao_pai <> '0'
										");
			$rs_mov= mysql_fetch_object($result_mov);
			mysql_free_result($result_mov);
			
			$result_vis= mysql_query("select sum(visitas) as total
										from  emissoes
										where id_emissao_pai = '0'
										");
			$rs_vis= mysql_fetch_object($result_vis);
			mysql_free_result($result_vis);
			
			$result_ane= mysql_query("select count(id_emissao_anexo) as total
										from  emissoes_anexos
										");
			$rs_ane= mysql_fetch_object($result_ane);
			mysql_free_result($result_ane);
			?>
			
			<h2>Memorandos</h2>
			<br />
			
			<?php
			$diferenca= (strtotime(date('Y-m-d')) - strtotime(formata_data(DESDE)));
 
			$dias = (ceil($diferenca/86400))+1;
			
			$media= $rs_num->total/$dias;
			?>
			
			<p>Sistema em operação por <big><strong><?=$dias;?></strong></big> dias, desde <big><strong><?=DESDE;?></strong></big>;</p>
			
			<p><big><strong><?=fnumi($rs_num->total);?></strong></big> memorandos enviados (média de <strong><?=ceil($media);?></strong> memorandos por dia);</p>
			
			<ul>
				<li><big><strong><?=fnumi($rs_num1->total);?></strong></big> memorandos;</li>
				<li><big><strong><?=fnumi($rs_num2->total);?></strong></big> memorandos circulares;</li>
				
				<?
				$result_num3= mysql_query("select count(id_emissao) as total
											from  emissoes
											where prioridade = '4'
											");
				$rs_num3= mysql_fetch_object($result_num3);
				
				$result_num4= mysql_query("select count(id_emissao) as total
											from  emissoes
											where privado = '1'
											");
				$rs_num4= mysql_fetch_object($result_num4);
				
				$result_num5= mysql_query("select count(id_emissao) as total
											from  emissoes
											where folha_rosto = '1'
											");
				$rs_num5= mysql_fetch_object($result_num5);
				
				$result_num6= mysql_query("select count(id_emissao) as total
											from  emissoes
											where destino_id_usuario <> '0'
											");
				$rs_num6= mysql_fetch_object($result_num6);
				?>
				
				<li><big><strong><?=fnumi($rs_num3->total);?></strong></big> memorandos urgentes;</li>
				<li><big><strong><?=fnumi($rs_num4->total);?></strong></big> memorandos privados;</li>
				<li><big><strong><?=fnumi($rs_num5->total);?></strong></big> memorandos com folha de rosto;</li>
				<li><big><strong><?=fnumi($rs_num6->total);?></strong></big> memorandos com A/C especificado;</li>
				
			</ul>
			
			<p><big><strong><?=fnumi($rs_mov->total);?></strong></big> movimentações;</p>
			
			<p><big><strong><?=fnumi($rs_vis->total);?></strong></big> visualizações de memorandos;</p>
			
			<p><big><strong><?=fnumi($rs_ane->total);?></strong></big> arquivos anexados.</p>
			
		</div>
		
		<div class="span6 well">
			<h2>Quantitativos</h2>
			<br />
			
			<?
			$result_u= mysql_query("select count(usuarios.id_usuario) as total
									from usuarios, usuarios_setores
									where usuarios.id_usuario = usuarios_setores.id_usuario
									and   usuarios_setores.atual = '1'
									and   usuarios_setores.principal = '1'
									and   usuarios.status = '1'
									and   usuarios_setores.perfil <> '1'
									");
			$rs_u= mysql_fetch_object($result_u);
			
			$result_u2= mysql_query("select count(usuarios.id_usuario) as total
									from usuarios, usuarios_setores
									where usuarios.id_usuario = usuarios_setores.id_usuario
									and   usuarios_setores.atual = '1'
									and   usuarios_setores.principal = '1'
									and   usuarios.status = '1'
									and   usuarios_setores.perfil = '2'
									");
			$rs_u2= mysql_fetch_object($result_u2);
			
			$result_u3= mysql_query("select count(usuarios.id_usuario) as total
									from usuarios, usuarios_setores
									where usuarios.id_usuario = usuarios_setores.id_usuario
									and   usuarios_setores.atual = '1'
									and   usuarios_setores.principal = '1'
									and   usuarios.status = '1'
									and   usuarios_setores.perfil = '3'
									");
			$rs_u3= mysql_fetch_object($result_u3);
			?>
			<p><big><strong><?=fnumi($rs_u->total);?></strong></big> usuários;</p>
			<ul>
				<li><big><strong><?=fnumi($rs_u2->total);?></strong></big> níveis 1;</li>
				<li><big><strong><?=fnumi($rs_u3->total);?></strong></big> níveis 2;</li>
			</ul>
			
			<?
			$result_s= mysql_query("select count(id_setor) as total
									from setores
									where status = '1'
									");
			$rs_s= mysql_fetch_object($result_s);
			
			$result_s1= mysql_query("select count(id_setor) as total
									from setores
									where status = '1'
									and   pai = '0'
									");
			$rs_s1= mysql_fetch_object($result_s1);
			
			$result_s2= mysql_query("select count(id_setor) as total
									from setores
									where status = '1'
									and   pai <> '0'
									");
			$rs_s2= mysql_fetch_object($result_s2);
			
			$result_s3= mysql_query("select count(id_setor) as total
									from setores
									where status = '1'
									and   tipo_setor = '2'
									");
			$rs_s3= mysql_fetch_object($result_s3);
			
			?>
			<p><big><strong><?=fnumi($rs_s->total);?></strong></big> setores;</p>
			
			<ul>
				<li><big><strong><?=fnumi($rs_s1->total);?></strong></big> setores principais;</li>
				<li><big><strong><?=fnumi($rs_s2->total);?></strong></big> sub-setores;</li>
				<li><big><strong><?=fnumi($rs_s3->total);?></strong></big> grupos de trabalho;</li>
			</ul>
			
			<?
			$result_a= mysql_query("select count(id_acesso) as total
									from acessos
									");
			$rs_a= mysql_fetch_object($result_a);
			?>
			<p><big><strong><?=fnumi($rs_a->total);?></strong></big> acessos ao sistema.</p>
			
			<?
			$result_a_in= mysql_query("select count(id_acesso) as total
									from acessos
									where ip = '189.90.55.66'
									");
			$rs_a_in= mysql_fetch_object($result_a_in);
			
			$interno= $rs_a_in->total;
			$externo= $rs_a->total-$interno;
			
			$interno_p= (($interno*100)/$rs_a->total);
			$externo_p= (($externo*100)/$rs_a->total);
			?>
			
			<ul>
				<li><big><strong><?=fnumf($interno_p);?>%</strong></big> na rede interna;</li>
				<li><big><strong><?=fnumf($externo_p);?>%</strong></big> de fora externa;</li>
			</ul>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span6">
			<h4>Setores que mais enviam memorandos, circulares e despachos</h4>
			<br />
			
			<ol>
			<?
			
			/*$result_tt1= mysql_query("CREATE TEMPORARY TABLE emissoes_ranking_uso
						 ( id_setor int, setor varchar(255), sigla varchar(31), enviados int, recebidos int )
						 ENGINE=MEMORY;
						 ") or die(mysql_error());*/
						 
			$result_rk1= mysql_query("select distinct(origem_id_setor) as id_setor
										from  emissoes
										");
			while ($rs_rk1= mysql_fetch_object($result_rk1)) {
				
				$result_rk1_s= mysql_query("select count(id_emissao) as total from emissoes
											where origem_id_setor = '". $rs_rk1->id_setor ."'
											 ");
				$rs_rk1_s= mysql_fetch_object($result_rk1_s);
				
				$result_rk_insere= mysql_query("insert into emissoes_ranking_uso (id_setor, setor, sigla, enviados)
													values
													('". $rs_rk1->id_setor ."', '". pega_setor($rs_rk1->id_setor, 'setor') ."',
													'". pega_setor($rs_rk1->id_setor, 'sigla') ."', '". $rs_rk1_s->total ."')
													");
			}
			
			$result1= mysql_query("select * from emissoes_ranking_uso
									order by enviados desc
									");
			while ($rs1= mysql_fetch_object($result1)) {
			?>
			<li><?= '<strong>'. pega_setor($rs1->id_setor, 'setor_ate_raiz_resumido') .'</strong> - <small>'. pega_setor($rs1->id_setor, 'setor') .'</small> '; ?> <small>(<?= $rs1->enviados; ?> movimentações)</small> </li>
			<? } ?>
			</ol>
			
			<?
			$result_tt3= mysql_query("DELETE FROM emissoes_ranking_uso ") or die(mysql_error());
			?>
			<br /><br />
			
		</div>
		<div class="span6">
			<h4>Setores que mais recebem memorandos, circulares e despachos</h4>
			<br />
			
			<ol>
			<?
			
			/*$result_tt1= mysql_query("CREATE TEMPORARY TABLE emissoes_ranking_uso
						 ( id_setor int, setor varchar(255), sigla varchar(31), enviados int, recebidos int )
						 TYPE=MEMORY;
						 ") or die(mysql_error());*/
						 
			$result_rk1= mysql_query("select distinct(destino_id_setor) as id_setor
										from  emissoes
										where destino_id_setor <> '0'
										");
			while ($rs_rk1= mysql_fetch_object($result_rk1)) {
				
				//consutando os envios diretos para o setor
				$result_rk1_s= mysql_query("select count(id_emissao) as total from emissoes
											where destino_id_setor = '". $rs_rk1->id_setor ."'
											 ");
				$rs_rk1_s= mysql_fetch_object($result_rk1_s);
				
				//consultando os memorandos circulares recebidos
				
				//consutando os envios diretos para o setor
				$result_rk1_c= mysql_query("select count(id_emissao_setor) as total from emissoes, emissoes_setores
											where emissoes.id_emissao = emissoes_setores.id_emissao
											and   emissoes.id_tipo_documento = '2'
											and   emissoes_setores.id_setor = '". $rs_rk1->id_setor ."'
											and   emissoes_setores.tipo_acesso = '2'
											 ");
				$rs_rk1_c= mysql_fetch_object($result_rk1_c);
				
				$total_recebido= $rs_rk1_s->total+$rs_rk1_c->total;
				
				$result_rk_insere= mysql_query("insert into emissoes_ranking_uso (id_setor, setor, sigla, recebidos)
													values
													('". $rs_rk1->id_setor ."', '". pega_setor($rs_rk1->id_setor, 'setor') ."',
													'". pega_setor($rs_rk1->id_setor, 'sigla') ."', '". $total_recebido ."')
													");
			}
			
			$result1= mysql_query("select * from emissoes_ranking_uso
									order by recebidos desc
									");
			while ($rs1= mysql_fetch_object($result1)) {
			?>
			<li><?= '<strong>'. pega_setor($rs1->id_setor, 'setor_ate_raiz_resumido') .'</strong> - <small>'. pega_setor($rs1->id_setor, 'setor') .'</small> '; ?> <small>(<?= $rs1->recebidos; ?> movimentações)</small> </li>
			<? } ?>
			</ol>
			
			<?
			$result_tt3= mysql_query("DELETE FROM emissoes_ranking_uso ") or die(mysql_error());
			?>
		</div>
		
		<?php
		gera_widget();
		?>
	</div>
<? } ?>