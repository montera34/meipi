<?
	header("Content-type: text/xml; charset=iso-8859-1");

	//$configsPath = "../";
	require_once("functions/meipi.php");

	global $dirThumbnail;

	$aParams = getEntriesParamsFromRequest($_REQUEST);
	unset($aParams["page"]);
	$aParams["content"]="yes";
	$aParams["order by"]="date";
	$aParams["order desc"]="desc";
	$aParams["limit"]="50";
 	$aEntries = getEntries($aParams);

	$webTitle = decode($webTitle);
	$webTitle = str_replace("<", "&lt;", $webTitle);
	
	if(substr($mainUrl, -1)=="/" && substr($commonFiles, 0, 1)=="/") {
		$baseUrl = $mainUrl.substr($commonFiles, 1);
	} else {
		$baseUrl = $mainUrl.$commonFiles;
	}

	endRequest();
echo '<?xml version="1.0" encoding="iso-8859-1" ?>';
?>
<rss version="2.0">
	<channel>
		<title><![CDATA[<?= $webName ?>: <?= $webTitle ?>]]></title>
		<link><![CDATA[<?= $baseUrl ?>]]></link>
		<description><![CDATA[<?= getString("meipi entries") ?>]]></description>
<?
	for($iEntry=0; $iEntry<dbGetSelectedRows($aEntries); $iEntry++)
	{
		$id_entry = $aEntries[$iEntry]["id_entry"];
		$lat = $aEntries[$iEntry]["latitude"];
		$lon = $aEntries[$iEntry]["longitude"];
		$title = $aEntries[$iEntry]["title"];
		$title = decode($title);
		$title = str_replace("<", "&lt;", $title);
		$text = $aEntries[$iEntry]["text"];
		$date = $aEntries[$iEntry]["dateFormatted"];
		$dateRFC = $aEntries[$iEntry]["dateRFC"];
		$id_user = $aEntries[$iEntry]["id_user"];
		$id_category = $aEntries[$iEntry]["id_category"];
		$category = getCategory($id_category);
		$login = $aEntries[$iEntry]["login"];
		$content = $aEntries[$iEntry]["file"];
		$type = $aEntries[$iEntry]["type"];
		$url = $aEntries[$iEntry]["url"];
		$edited = $aEntries[$iEntry]["edited"];
		$last_edited = $aEntries[$iEntry]["dateLastEditedFormatted"];
		$last_editor = $aEntries[$iEntry]["last_editor"];

		$text = getString("Category").": <a href=\"".$baseUrl.setParams("list.php", Array("category" => $id_category))."\">".$category."</a><br/>".allowedHtml($text);

		if(strlen($content)>0)
		{
			switch($type)
			{
				case 0:
					$text = "<img src=\"$baseUrl$dirThumbnail$content\"/><br/>$text";
					break;
				case 1:
					$text = "<object height=\"350\" width=\"425\"><param name=\"movie\" value=\"http://www.youtube.com/v/$content\"><param name=\"wmode\" value=\"transparent\"><embed src=\"http://www.youtube.com/v/$content\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" height=\"350\" width=\"425\"></object><br/>".$text;
					break;
				case 2:
					$text = "<embed style=\"width:400px; height:326px;\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=".$content."\" wmode=\"transparent\"></embed><br/>".$text;
					break;
				default:
					$text = getEmbedCode($type, $content)."<br/>".$text;
					break;
			}
		}

		if(strlen($url)>0)
		{
			$text .= "<br/><a href=\"".safeForJavascript($url)."\">www</a>";
		}

		if($edited>0)
		{
			$text .= "<br/>".getString("Last edited at")." ".$last_edited." ".getString("by")." ".getUser($last_editor);
		}

?>		<item>
			<title><![CDATA[<?= $title ?>]]></title>
			<link><![CDATA[<?= $baseUrl.setParams("meipi.php", Array("open_entry" => $id_entry)) ?>]]></link>
			<author><![CDATA[<?= $login ?>]]></author>
			<category><![CDATA[<?= $category ?>]]></category>
			<pubDate><?= $dateRFC ?></pubDate>
			<description><![CDATA[<?= $text ?>]]></description>
			<guid><?= $baseUrl.setParams("meipi.php", Array("open_entry" => $id_entry)) ?></guid>
		</item>
<?
  }
?>
	</channel>
</rss>
