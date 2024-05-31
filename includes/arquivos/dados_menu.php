<? if (pode("1", $_SESSION["perfil"])) { ?>
<ul class="nav nav-tabs">
	<li class="<? if ($pagina=='acesso/dados') echo 'active'; ?>"><a href="./?pagina=acesso/dados">Meus dados</a></li>
	<li class="<? if ($pagina=='acesso/temas') echo 'active'; ?>"><a href="./?pagina=acesso/temas">Temas</a></li>
</ul>
<? } ?>