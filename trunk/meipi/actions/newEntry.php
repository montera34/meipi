<?
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$nextPage=$commonFiles."meipi.php";
	$aEntry=getEntryFromRequest($_REQUEST);
	$idEntry = $aEntry[0]["id_entry"];
	if($aEntry["ok"])
	{
		$aInserted = insertEntry($aEntry);
		if($aInserted["ok"])
		{
			endRequest();
			Header("location: ".setParams($nextPage, Array("msg" => getString("Inserted"), "open_entry" => $idEntry)));
		}
		else
		{
			endRequest();
			Header("location: ".setParams($nextPage, Array("msg" => getErrors($aInserted), "open_entry" => $idEntry)));
		}
	}
	else
	{
		endRequest();
		Header("location: ".setParams($nextPage, Array("msg", getErrors($aEntry), "open_entry" => $idEntry)));
	}
?>
