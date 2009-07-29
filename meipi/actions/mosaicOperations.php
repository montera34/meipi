<?
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$aMosaicOperation=getMosaicOperation($_REQUEST);

	if($aMosaicOperation["ok"])
	{
		$aInserted = doMosaicOperation($aMosaicOperation);
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
