<?
	header("Content-type: text/xml");

	//$configsPath = "../";
	require_once("functions/meipi.php");

	$aParams = getEntriesParamsFromRequest($_REQUEST);
	$aParams["content"]="yes";
	$aParams["located"]="yes";
	//$aParams["limit"]="30";
	unset($aParams["id_entry"]);
	$aEntries = getEntries($aParams);

	global $labelMaxLength;

	$labelMaxLength = intval($labelMaxLength);
	if($labelMaxLength<=0)
	{
	  $labelMaxLength=15;
	}
		
echo '<?xml version="1.0" ?>';
?>
<markers>
<?
	for($iEntry=0; $iEntry<dbGetSelectedRows($aEntries); $iEntry++)
	{
		$id_entry = addslashes($aEntries[$iEntry]["id_entry"]);
		$lat = addslashes($aEntries[$iEntry]["latitude"]);
		$lon = addslashes($aEntries[$iEntry]["longitude"]);
		$title = addslashes($aEntries[$iEntry]["title"]);
		$text = addslashes(allowedHtml($aEntries[$iEntry]["text"]));
		$titleOverEntry = addslashes(getSubString($aEntries[$iEntry]["title"],$charsInOverEntryTitle,$lineInOverEntryTitle));
		$textOverEntry = addslashes(getSubString(basicHtml(allowedHtml($aEntries[$iEntry]["text"])),$charsInOverEntry,$lineInOverEntry));
		$category = addslashes(getCategory($aEntries[$iEntry]["id_category"]));
		$idCategory = addslashes($aEntries[$iEntry]["id_category"]);
		$file = addslashes($aEntries[$iEntry]["file"]);
		$type = addslashes($aEntries[$iEntry]["type"]);
		$date = addslashes($aEntries[$iEntry]["date"]);
		$dateFormatted = addslashes($aEntries[$iEntry]["dateFormatted"]);
		$id_user = addslashes($aEntries[$iEntry]["id_user"]);
		$login = encode(addslashes($aEntries[$iEntry]["login"]));
		$iconImage = addslashes($aEntries[$iEntry]["iconImage"]);
		$iconImageStand = addslashes($aEntries[$iEntry]["iconImageStand"]);

		$aTags = getTags($id_entry);
		switch(count($aTags))
		{
			case "0":
				$tagString = addslashes($title);
				break;
			case "1":
				$tagString = addslashes($aTags[0]["tag_name"]);
				break;
			default:
				$tagString = addslashes($aTags[0]["tag_name"].",".$aTags[1]["tag_name"]);
				break;
		}
		$iconText = getSubString($title, $labelMaxLength);
		$aIconText = getStringSize(htmlDecode($iconText));
		if(strlen($iconImage)==0)
		{
			$iconWidth = addslashes($aIconText[width]);
			$iconHeight = addslashes($aIconText[height]) + $iconOffsetH;
		}
		else
		{
			$iconWidth = $aEntries[$iEntry]["iconWidth"];
			$iconHeight = $aEntries[$iEntry]["iconHeight"];
		}

		if(isLogged())
		{
			$userId=getIdUser();

			$aVoted = dbSelect("SELECT vote FROM ".VOTE." WHERE id_user='$userId' AND id_entry='$id_entry' LIMIT 1", $dbLink);
			if(count($aVoted)>0)
			{
				$iVote = $aVoted[0]["vote"];
			}
			else
			{
				$iVote = 0;
			}
		}

?>	<marker id="<?= $id_entry ?>">
		<lat><?= $lat ?></lat>
		<lng><?= $lon ?></lng>
		<title><![CDATA[<?= $title ?>]]></title>
		<text><![CDATA[<?= $text ?>]]></text>
		<titleOverEntry><![CDATA[<?= $titleOverEntry ?>]]></titleOverEntry>
		<textOverEntry><![CDATA[<?= $textOverEntry ?>]]></textOverEntry>
		<idCategory><?= $idCategory ?></idCategory>
		<category><![CDATA[<?= $category ?>]]></category>
		<file><![CDATA[<?= $file ?>]]></file>
		<type><?= $type ?></type>
		<date><?= $date ?></date>
		<dateFormatted><?= $dateFormatted ?></dateFormatted>
		<login><![CDATA[<?= $login ?>]]></login>
		<id_user><?= $id_user ?></id_user>
		<tagString><![CDATA[<?= $tagString ?>]]></tagString>
		<iconText><![CDATA[<?= $iconText ?>]]></iconText>
		<iconWidth><?= $iconWidth ?></iconWidth>
		<iconHeight><?= $iconHeight ?></iconHeight>
		<iconImage><?= $iconImage ?></iconImage>
		<iconImageStand><?= $iconImageStand ?></iconImageStand>
		<userVote><?= $iVote ?></userVote>
	</marker>
<?
	}

	endRequest();

?>
</markers>
