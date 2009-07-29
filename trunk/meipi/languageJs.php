<?
	//$configsPath = "../";
	require_once("functions/meipi.php");
?>
var languageStrings = new Array();
<?
	$aJavascriptLanguage=
		Array(
			"Vote",
			"Voted",
			"View in map",
			"Delete entry",
			"Edit entry",
			"Post a comment",
			"Log in to write a comment",
			"Comments RSS",
			"Submit",
			"Added to mosaic",
			"Missing information",
			"Title",
			"Category",
			"Position",
			"Invalid latitude",
			"Invalid longitude",
			"Wrong video type",
			"tags",
			"DeleteConfirmation",
		);

	foreach($aJavascriptLanguage as $id)
	{
		echo "languageStrings[\"".safeForJavascript("$id")."\"] = '".safeForJavascript(str_replace("'", "\'", getString($id)))."';\n";
	}
	global $centerAddress, $city;
	echo "languageStrings[\"centerAddress\"] = '".safeForJavascript(str_replace("'", "", decode($centerAddress)))."';\n";
	echo "languageStrings[\"City\"] = '".safeForJavascript(str_replace("'", "", decode($city)))."';\n";
	echo "languageStrings[\"commonFiles\"] = '".safeForJavascript($commonFiles)."';\n";
?>

function getString(id)
{
	if(languageStrings[id]!=null && languageStrings[id].length>0)
		return languageStrings[id];
	return id;
}
