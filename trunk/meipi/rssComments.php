<?
	header("Content-type: text/xml; charset=iso-8859-1");

	$configsPath = "../";
	require_once("../functions/meipi.php");

	$id_entry = $_REQUEST["id_entry"];
	$aParams["id_entry"]="$id_entry";
	$aParams["content"]="yes";
	$aEntries = getEntries($aParams);
	$lat = $aEntries[0]["latitude"];
	$lon = $aEntries[0]["longitude"];
	$title = $aEntries[0]["title"];
	$title = decode($title);
	$title = str_replace("<", "&lt;", $title);
	$text = $aEntries[0]["text"];
	$date = $aEntries[0]["date"];
	$id_user = $aEntries[0]["id_user"];
	$id_category = $aEntries[0]["id_category"];
	$login = $aEntries[0]["login"];
	$content = $aEntries[0]["file"];

	if(isset($id_entry))
  $aComments = getComments($id_entry);

	endRequest();
echo '<?xml version="1.0" encoding="iso-8859-1" ?>';
?>
<rss version="2.0">
	<channel>
		<title><![CDATA[<?= $webName ?> <?= getString("rssEntry") ?>: <?= $title ?>]]></title>
		<link><![CDATA[<?= $mainUrl.setParams("meipi.php", Array("open_entry" => $id_entry)) ?>]]></link>
		<description><![CDATA[<?= allowedHtml($text) ?>]]></description>
<?
	for($iComment=0; $iComment<count($aComments); $iComment++)
  {
    $comment_id_comment = $aComments[$iComment]["id_comment"];
    $comment_subject = $aComments[$iComment]["subject"];
		$comment_subject = decode($comment_subject);
		$comment_subject = str_replace("<", "&lt;", $comment_subject);
    $comment_text = $aComments[$iComment]["text"];
    $comment_id_user = $aComments[$iComment]["id_user"];
    $comment_login = $aComments[$iComment]["login"];
    $comment_dateRFC = $aComments[$iComment]["dateRFC"];
?>		<item>
			<title><![CDATA[<?= $comment_subject ?>]]></title>
			<link><![CDATA[<?= $mainUrl.setParams("meipi.php", Array("open_entry" => $id_entry)) ?>]]></link>
			<author><![CDATA[<?= $comment_login ?>]]></author>
			<pubDate><?= $comment_dateRFC ?></pubDate>
			<description><![CDATA[<?= allowedHtml($comment_text) ?>]]></description>
			<guid><?= $id_entry ?></guid>
		</item>
<?
  }
?>
	</channel>
</rss>
