<?
	header("Content-type: text/html; charset=iso-8859-1");
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$result = deleteEntry($_REQUEST["id_entry"]);

	endRequest();

	$nextPage=$_REQUEST["next"];
	if(strlen($nextPage)==0) 
	{
		$nextPage = getenv('HTTP_REFERER');
	}
	if(strlen($nextPage)==0) 
	{
		$nextPage="index.php";
	}
	if($result=="0")
	{
		$message = getString("Entry deleted");
	}
	else
	{
		$message = $result;
	}
	
	$nextPage = setParams($nextPage, Array("msg" => $message, "open_entry" => ""));

	Header("Location: $nextPage");
	return;
?>
