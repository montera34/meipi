<?
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$result = "0";
	$aParams = getPermissionParamsFromRequest($_REQUEST);
	if($aParams["ok"])
	{
		$bResult=modifyPermission($aParams);
		if($bResult)
		{
			$result = "1";
		}
	}
	else 
	{
		$sErrors = getErrors($aParams);
	}

	header("Content-type: text/xml");
	//echo 'xml version="1.0" encoding="UTF-8"
	echo '<?xml version="1.0" ?>';
	?><results>
	<result code="<?= $result ?>"><![CDATA[<?= (strlen($sErrors)>0 ? $sErrors : $result) ?>]]></result>
</results>
<?
	endRequest();
?>
