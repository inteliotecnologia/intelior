<?
if (isset($_GET["pagina"])) $pagina= $_GET["pagina"];
else $pagina= $pagina;

$paginar= $pagina;
if (strpos($paginar, "/")) {
	$parte_pagina= explode("/", $paginar);
	
	if (file_exists("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php"))
		include("_". $parte_pagina[0] ."/". "__". $parte_pagina[1] .".php");
	else include("404.php");
}
else {
	if (file_exists("__". $paginar .".php"))
		include("__". $paginar .".php");
	else include("404.php");
}
?>