<?
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$aMosaic=getMosaicFromRequest($_REQUEST);

	if($aMosaic["ok"])
	{
		$aInserted = insertMosaic($aMosaic);
		$description = getString("Mosaic saved");
		$code = "1";
	}
	else
	{
		$description = getString("Sorry, mosaic couldn't be saved");
		$code = "0";
	}
	?><results>
	<result code="<?= $code ?>" description="<?= $description ?>" />
</results>
<?
	endRequest();
?>
