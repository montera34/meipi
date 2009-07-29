<?
	if($_REQUEST["output"]!="text")

	header("Content-type: application/vnd.google-earth.kml+xml");

	$configsPath = "../";
	require_once("../functions/meipi.php");

	global $dirThumbnail;

	$webTitle = removeAcutes($webTitle);

	$aParams = getEntriesParamsFromRequest($_REQUEST);
	unset($aParams["page"]);
	$aParams["content"]="yes";
	$aParams["order by"]="date";
	$aParams["order desc"]="desc";
	$aParams["limit"]="50";
 	$aEntries = getEntries($aParams);
	
	endRequest();
echo '<?xml version="1.0" encoding="iso-8859-1" ?>';

?>
<kml xmlns="http://earth.google.com/kml/2.1">
	<Document>
<?
	for($iEntry=0; $iEntry<dbGetSelectedRows($aEntries); $iEntry++)
	{
		$id_entry = $aEntries[$iEntry]["id_entry"];
		$lat = $aEntries[$iEntry]["latitude"];
		$lon = $aEntries[$iEntry]["longitude"];
		$title = $aEntries[$iEntry]["title"];
		$text = $aEntries[$iEntry]["text"];
		$date = $aEntries[$iEntry]["date"];
		$id_user = $aEntries[$iEntry]["id_user"];
		$id_category = $aEntries[$iEntry]["id_category"];
		$login = $aEntries[$iEntry]["login"];
		$content = $aEntries[$iEntry]["file"];
		$type = $aEntries[$iEntry]["type"];
		$url = $aEntries[$iEntry]["url"];
		$edited = $aEntries[$iEntry]["edited"];
		$last_edited = $aEntries[$iEntry]["dateLastEditedFormatted"];
		$last_editor = $aEntries[$iEntry]["last_editor"];

		if(strlen($content)>0)
		{
			switch($type)
			{
				default:
				case 0:
					$text = "<img src=\"$mainUrl$dirThumbnail$content\"/><br/>$text";
					break;
				case 1:
					$text = "<object height=\"350\" width=\"425\"><param name=\"movie\" value=\"http://www.youtube.com/v/$content\"><param name=\"wmode\" value=\"transparent\"><embed src=\"http://www.youtube.com/v/$content\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" height=\"350\" width=\"425\"></object><br/>".$text;
					break;
				case 2:
					$text = "<embed style=\"width:400px; height:326px;\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=".$content."\" wmode=\"transparent\"></embed><br/>".$text;
					break;
			}
		}

		if(strlen($url)>0)
		{
			$text .= "<br/><a href=\"".safeForJavascript($url)."\">www</a>";
		}

		if($edited>0)
		{
			$text .= " <br/>".getString("Last edited at")." ".$last_edited." ".getString("by")." ".getUser($last_editor);
		}

		$text .= " <br/><a href=\"".$mainUrl.setParams("meipi.php?open_entry=$id_entry", null)."\">[ + ]</a>";
?>
		<Placemark>
			<name><![CDATA[<?= $title ?>]]></name>
			<description>
				<![CDATA[<?= $text ?>]]>
			</description>
			<Point>
				<coordinates><?=$lon ?>,<?= $lat ?></coordinates>
			</Point>
			<Style>
				<BalloonStyle>
					<bgColor>ffe2ecf9</bgColor>
				</BalloonStyle>
				<IconStyle>
					<Icon>
						<href><![CDATA[<?= $mainUrl.setParams("label.php?width=32&height=32&cat=$id_category", null) ?>]]></href>
					</Icon>
					<scale>0.2</scale>
					<heading>0</heading>
				</IconStyle>
			</Style>
		</Placemark>
<?
	}
?>
	</Document>
</kml>

