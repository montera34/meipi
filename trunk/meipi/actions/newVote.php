<?
	$configsPath = "../";
	require_once("../functions/meipi.php");

	$aVote=getVoteFromRequest($_REQUEST);

	if($aVote["ok"])
	{
		$aInserted = insertVote($aVote);
		$description = "Inserted";
		$code = "1";
	}
	else
	{
		$description = "Not inserted";
		$code = "0";
	}
	?><results>
	<result code="<?= $code ?>" description="<?= $description ?>" />
</results>
<?
	endRequest();
?>
