<?
	header("Content-type: text/xml");
	$configsPath = "../";
	$languagePath = "../";

	require_once("../functions/common.php");
	require_once("../functions/language.php");

	$result = "Missing parameters";
	$aParams = getSendPasswordParamsFromRequest($_REQUEST);
	if($aParams["ok"])
	{
		$result = sendPasswordCode($aParams);
	}


	echo '<?xml version="1.0" ?>';
?>
<result>
	<description><?= $result ?></description>
</result>
