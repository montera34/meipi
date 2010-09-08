<?
	header("Content-type: text/xml; charset=iso-8859-1");

	//$configsPath = "../";
	require_once("functions/meipi.php");

	$aComments = getComments("ALL", 100);

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
		<title><![CDATA[<?= $webName ?> <?= getString("rssComments") ?>: <?= $webTitle ?>]]></title>
		<link><![CDATA[<?= $baseUrl ?>]]></link>
		<description><![CDATA[<?= getString("meipi comments") ?>]]></description>
<?
	for($iComment=0; $iComment<count($aComments); $iComment++)
	{
		$comment_id_entry = $aComments[$iComment]["id_entry"];
		$comment_id_comment = $aComments[$iComment]["id_comment"];
		$comment_subject = $aComments[$iComment]["subject"];
		$comment_subject = decode($comment_subject);
		$comment_subject = str_replace("<", "&lt;", $comment_subject);
		$comment_text = $aComments[$iComment]["text"];
		$comment_id_user = $aComments[$iComment]["id_user"];
		$comment_login = $aComments[$iComment]["login"];
		$comment_date = $aComments[$iComment]["dateFormatted"];
		$comment_dateRFC = $aComments[$iComment]["dateRFC"];
?>		<item>
			<title><![CDATA[<?= $comment_subject ?>]]></title>
			<author><![CDATA[<?= $comment_login ?>]]></author>
			<link><![CDATA[<?= $baseUrl.setParams("meipi.php", Array("open_entry" => $comment_id_entry)) ?>]]></link>
			<pubDate><?= $comment_dateRFC ?></pubDate>
			<description><![CDATA[<?= allowedHtml($comment_text) ?>]]></description>
			<guid><?= $id_entry ?></guid>
		</item>
<?
  }
?>
	</channel>
</rss>
