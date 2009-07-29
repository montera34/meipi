<?
	header("Content-type: text/html; charset=iso-8859-1");
	$configsPath = "../";
	require_once("../functions/meipi.php");
	
	$id_entry = intval($_REQUEST["id_entry"]);
	$nextPage=$commonFiles."meipi.php";

	$aComment = getCommentFromRequest($_REQUEST);
	if($aComment["ok"])
	{
		insertComment($aComment);
	}
	Header("Location: ".setParams($nextPage, Array("open_entry" => $id_entry)));
	return;
?>
