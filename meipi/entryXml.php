<?
	//header("Content-type: text/xml; charset=UTF-8");
	header("Content-type: text/xml");

	$configsPath = "../";
	require_once("../functions/meipi.php");
	
	$id_entry = $_REQUEST["id_entry"];
	
	if($id_entry == -1)
	{
		$id_entry =intval(getRandomIdEntry());
	}
	
	$aParams["id_entry"]=intval($id_entry);
	$aParams["content"]="yes";
	$aEntries = getEntries($aParams);
	if(dbGetSelectedRows($aEntries)>0)
	{
		$lat = $aEntries[0]["latitude"];
		$lon = $aEntries[0]["longitude"];
		$title = $aEntries[0]["title"];
		$text = $aEntries[0]["text"];
		$date = $aEntries[0]["dateFormatted"];
		$id_user = $aEntries[0]["id_user"];
		$id_category = $aEntries[0]["id_category"];
		$category = getCategory($id_category);
		$login = $aEntries[0]["login"];
		$content = $aEntries[0]["file"];
		$type = $aEntries[0]["type"];
		$url = $aEntries[0]["url"];
		$address = $aEntries[0]["address"];
		$canEdit = "false";
		$edited = $aEntries[0]["edited"];
		$last_edited = $aEntries[0]["dateLastEditedFormatted"];
		$last_editor = $aEntries[0]["last_editor"];
		$extra = $aEntries[0]["extra"];
		if(intval($last_editor)>0)
		{
			$lastEditedMsg = getString("Last edited at")." ".$last_edited." ".getString("by")." ".getUser($last_editor);
		}

		$aComments = getComments($id_entry);

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
		$aTagString = getStringSize($tagString);
		$tagString = htmlEncode($tagString);
		$iconWidth = addslashes($aTagString[width]);
		$iconHeight = addslashes($aTagString[height]);

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

			if(canEditEntry($id_entry))
			{
				$canEdit = "true";
			}
		}
	}
	//echo 'xml version="1.0" encoding="UTF-8"
	echo '<?xml version="1.0" ?>';
?><markers>
<?
	if(dbGetSelectedRows($aEntries)>0)
	{
?>
	<marker id="<?= $id_entry ?>">
		<lat><?= $lat ?></lat>
		<lng><?= $lon ?></lng>
		<title><![CDATA[<?= $title ?>]]></title>
		<text><![CDATA[<?= $text ?>]]></text>
		<idCategory><?= $id_category ?></idCategory>
		<category><![CDATA[<?= $category ?>]]></category>
		<file><![CDATA[<?= $content ?>]]></file>
		<type><?= $type ?></type>
		<date><?= $date ?></date>
		<login><![CDATA[<?= encode($login) ?>]]></login>
		<id_user><![CDATA[<?= $id_user ?>]]></id_user>
		<tagString><![CDATA[<?= $tagString ?>]]></tagString>
		<iconWidth><?= $iconWidth ?></iconWidth>
		<iconHeight><?= $iconHeight ?></iconHeight>
		<url><![CDATA[<?= $url ?>]]></url>
		<address><![CDATA[<?= $address ?>]]></address>
		<userVote><?= $iVote ?></userVote>
		<lastEditedMsg><![CDATA[<?= $lastEditedMsg ?>]]></lastEditedMsg>
		<canEdit><?= $canEdit ?></canEdit>
<?
		if(count($extra)>0)
		{
?>
			<extra>
<?
				foreach($extra as $extraName => $extraValue)
				{
?>
					<param>
						<paramName><![CDATA[<?= $extraName ?>]]></paramName>
						<paramValue><![CDATA[<?= $extraValue ?>]]></paramValue>
					</param>
<?
				}
?>
			</extra>
<?
		}
?>
		<comments>
<?
	for($iComment=0; $iComment<count($aComments); $iComment++)
	{
		$id_comment = $aComments[$iComment]["id_comment"];
		$subject = $aComments[$iComment]["subject"];
		$text = $aComments[$iComment]["text"];
		$id_user = $aComments[$iComment]["id_user"];
		$login = $aComments[$iComment]["login"];
		$date = $aComments[$iComment]["dateFormatted"];
?>			<comment id="<?= $id_comment ?>">
				<subject><![CDATA[<?= $subject ?>]]></subject>
				<text><![CDATA[<?= $text ?>]]></text>
				<date><?= $date ?></date>
				<id_user><![CDATA[<?= $id_user ?>]]></id_user>
				<login><![CDATA[<?= encode($login) ?>]]></login>
			</comment>
<?
	}
?>
		</comments>
		<tags>
<?
	for($iTag=0; $iTag<count($aTags); $iTag++)
	{
?>			<tag><![CDATA[<?= $aTags[$iTag]["tag_name"] ?>]]></tag>
<?
	}
?>
		</tags>
	</marker>
<?
	}
?>
</markers>
