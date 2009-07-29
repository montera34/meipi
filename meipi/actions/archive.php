<?
	header("Content-type: text/html; charset=iso-88859-1");
	$configsPath = "../";
	require_once("../functions/meipi.php");
	
	$aArchive = getArchiveFromRequest($_REQUEST);
	if($aArchive["ok"])
	{
		archive($aArchive);
	}
	Header("Location: ".setParams($commonFiles."meipi.php", Array("open_entry" => intval($_REQUEST["id_entry"]))));
	return;
?>
