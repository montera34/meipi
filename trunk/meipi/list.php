<?
	header("Content-type: text/html; charset=iso-8859-1");
	//$configsPath = "../";
	require_once("functions/meipi.php");

	$aParams = getEntriesParamsFromRequest($_REQUEST);

	if(!isset($aParams["order by"]))
	{
		$aParams["order by"]="date";
		$aParams["order desc"]="desc";
	}
	$aParams["getRows"]="yes";
	if(!isset($aParams["page"]))
	{
		$aParams["page"]="1";
	}
	$aParams["content"] = "yes";
	$aEntries = getEntries($aParams);
	$sEntriesIntro = "";

	if(isset($aParams["search"]))
	{
		$sEntriesIntro = getString("searching").$aParams["search"];
	}
	else
	{
		if(isset($aParams["id_user"]))
		{
			if(!isset($user))
			{
				$user = getUser($aParams["id_user"]);
			}
			$sEntriesIntro.=getString("preUser").$user;
		}
		if(isset($aParams["category"]))
		{
			$category = getCategory($aParams["category"]);
			$sEntriesIntro.=getString("preCategory").$category;
		}
		if(isset($aParams["id_tag"]))
		{
			$tag = getTag($aParams["id_tag"]);
			$sEntriesIntro.=getString("preTag").$tag;
		}
	}
	$iRows = dbGetTotalRows($aEntries);

	$sBestRanked = getString("Best ranked").$sEntriesIntro;
	$sLastEntries = getString("lastEntries").$sEntriesIntro;

	// for setParams
	$iPage = intval($_REQUEST["page"]);
	$tab = $_REQUEST["tab"];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $webName ?>: <?= $webTitle ?> - <?= getString("list") ?></title>
	<? getMeipiHead() ?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script src="<?= setParams("languageJs.php", null) ?>" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS" href="<?= $commonFiles.setParams("rss.php", null) ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - comments" href="<?= $commonFiles.setParams("rssAllComments.php", null) ?>" />
<?
	if(strlen($sEntriesIntro)>0)
	{
?>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - <?= $sEntriesIntro ?>" href="<?= $commonFiles.setParams("rss.php?".$_SERVER["QUERY_STRING"], null) ?>" />
<?
	}
?>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<?
	$onLoadScript = getOnLoadScript($_REQUEST);
	echo $onLoadScript;
?></head>
<body <?= getOnLoadCall($onLoadScript) ?> onunload="GUnload()">
	<?= getNavigationBar($_REQUEST, "list") ?>
	<div id="meipi">
	<? getMeipiDescription() ?>

	<div id="entradas">
		<div class="paginas">
			<a class="pagina<?= ($tab=="" || $tab=="1" ? "-actual" : "") ?>" href="<?= setParams("list.php?".$_SERVER["QUERY_STRING"], Array("page" => "", "tab" => "", "order" => "", "open_entry" => "")) ?>"><?= $sLastEntries ?></a>
			<a class="pagina<?= ($tab==2 ? "-actual" : "") ?>" href="<?= setParams("list.php?".$_SERVER["QUERY_STRING"], Array("order" => "rank_desc", "page" => "", "tab" => "2", "open_entry" => "")) ?>"><?= $sBestRanked ?></a>
		</div> <!-- end id paginas -->
<?
		global $archiveParam;
		if(strlen($archiveParam)>0)
		{
?>
<div id="archived">
<?
			if($_REQUEST["all"]=="true")
			{
?>			<a class="" id="archive2" href="<?= setParams("list.php?".$_SERVER["QUERY_STRING"], Array("page" => "", "open_entry" => "", "all" => "")) ?>"><?= getString("viewOnlyActive") ?></a> 
<?
			}
			else
			{
?>			<a class="" id="archive2" href="<?= setParams("list.php?".$_SERVER["QUERY_STRING"], Array("page" => "", "open_entry" => "", "all" => "true")) ?>"><?= getString("viewArchived") ?></a>
<?
			}
?>
</div> <!--end id archived -->
<?
		}
?>
		<div class="suscrip"><a href="<?= $commonFiles.setParams("rss.php", null) ?>"><img src="<?= $commonFiles ?>images/rss.png" /><?= getString("Subscription entries") ?></a></div><div class="suscrip"><a href="<?= $commonFiles.setParams("rssAllComments.php", null) ?>"><img src="<?= $commonFiles ?>images/rss.png" /><?= getString("Subscription comments") ?></a></div><? if(strlen($sEntriesIntro)>0) { ?><div class="suscrip"><a href="<?= $commonFiles.setParams("rss.php?".$_SERVER["QUERY_STRING"], null) ?>"><img src="<?= $commonFiles ?>images/rss.png" /><?= getString("Subscription entries") ?><?= $sEntriesIntro ?></a></div><? } ?>


		<div id="entradas-pag"> 
			<div class="entradasNumber">
				<?= $iRows ?> <?= getString("entries") ?>
			</div>
			<div class="numerosdepagina" style="text-align: left;">
				<? getPageNumbers($iRows,$aParams["page"],"list.php"); ?>
			</div> <!-- end class numerosdepagina -->
		</div> <!-- end id entradas-pagina -->
<div id="entradas-list">
<?
  if(isLogged())
    $logged = "yes";
  else
    $logged = "no";

  $userId = getIdUser();

	$iEntries = dbGetSelectedRows($aEntries);
	for($iEntry=0; $iEntry<$iEntries; $iEntry++)
	{
		$id_entry = $aEntries[$iEntry]["id_entry"];
		$lat = $aEntries[$iEntry]["latitude"];
		$lon = $aEntries[$iEntry]["longitude"];
		$title = $aEntries[$iEntry]["title"];
		$text = $aEntries[$iEntry]["text"];
		$date = $aEntries[$iEntry]["dateFormatted"];
		$id_user = $aEntries[$iEntry]["id_user"];
		$id_category = $aEntries[$iEntry]["id_category"];
		$category = getCategory($id_category);
		$login = $aEntries[$iEntry]["login"];
		$ranking = $aEntries[$iEntry]["ranking"];
		$votes = $aEntries[$iEntry]["votes"];
		$url = $aEntries[$iEntry]["url"];
		$comments = $aEntries[$iEntry]["comments"];
		$id_content = $aEntries[$iEntry]["id_content"];
		$content = $aEntries[$iEntry]["file"];
		$type = $aEntries[$iEntry]["type"];
		$cssClass = $aEntries[$iEntry]["css_class"];

		$content = getPreview($type, $content);

		$aTags = getTags($id_entry);

?>
	<div class="entrada <?= $cssClass ?>" id="<?= $id_entry ?>">
		<div class="entrada-img"><? if(strlen($content)>0) { ?><img src="<?= $content ?>" width="100" height="100" /><? } else { echo getString("no image"); } ?></div> <!-- entrada-img -->
		<div class="entrada-txt">
				<h1>
				<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= getSubString($title,$charsInListTitle,$lineInListTitle) ?></a>
				</h1>
			<div class="entrada-data">
				<?= getString("by") ?> <strong><a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $login ?></a></strong> -- <?= $date ?>
				<ul>
				<li><?
				if(count($aTags)>0)
				{
					echo getString("tags").":";
					for($iTag=0; $iTag<count($aTags); $iTag++)
					{
						$id_tag = $aTags[$iTag]["id_tag"];
						$tag_name = $aTags[$iTag]["tag_name"];
?>					<a href="<?= setParams("list.php", Array("id_tag" => $id_tag)) ?>"><?= $tag_name ?></a>
<?
					}
				}
?>
				</li>
				<li><?= getString("category") ?>: <a href="<?= setParams("list.php", Array("category" => $id_category)) ?>"><?= $category ?></a></li>
				</ul>
			</div> <!-- entrada-data -->
			
			<div class="entrada-desc">
				<?= getSubString(basicHtml(allowedHtml($text)),$charsInList,$lineInList) ?> <a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><span class="extend">[<?= getString("Extend") ?>]</span></a>
			</div> <!-- entrada-desc -->

			<div class="entrada-party">
				<ul>
				<li><a href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= getString("Comment") ?></a> [<?= $comments ?> <?= getString("comment".($comments!=1 ? "s" : "")) ?>]</li>
				<li><?
					$ranking1to5 = (round($ranking/2.5)/2)+3;
					for($rank = 1; $rank<=5; $rank++)
					{
						if($ranking1to5>=$rank)
						{
							?><img src="<?= $commonFiles ?>images/star_on.png" /><?
						}
						else if($ranking1to5>=$rank-0.5)
						{
							?><img src="<?= $commonFiles ?>images/star_half.png" /><?
						}
						else
						{
							?><img src="<?= $commonFiles ?>images/star_off.png" /><?
						}
					}
				?></li>
<?
				if(isValidLatLon($lat, $lon))
				{
?>
				<li><a href="<?= setParams("map.php", Array("id_entry" => $id_entry)) ?>"><?= getString("View in map") ?></a></li>
<?
				}

				if(strlen($id_content)>0 && $type=="0")
				{
?>
					<li><a class="amosac" title="<?= getString("Add to mosaic") ?>" href="javascript:addToMosaic('<?= $idMeipi ?>', '<?= $id_content ?>');"><img src="<?= $commonFiles ?>images/header-mosac-anadir.gif" /><?= getString("Add to mosaic") ?></a></li>
<?
				}
?>
					<li><a href="<?= setParams("meipi.php", Array("open_entry" => $id_entry)) ?>"><?= getString("Permalink") ?></a></li>
					</ul>
			</div> <!-- end class entrada-party -->
		</div> <!-- end class entrada-txt -->
	</div> <!-- end class entrada -->
<?
  }

	$rss = setParams("rss.php?".$_SERVER["QUERY_STRING"], Array("page" => ""));
?>
  </div> <!--entradas-list-->
	</div> <!-- entradas -->
	</div> <!-- end id meipi -->
<!--/div-->


	<?= getFooter("list") ?>
	<? getOverEntry("list"); ?>
	<? getEntryWindow($_REQUEST); ?>
	<? getLoginForm($_REQUEST); ?>
	<? getMessageWindow(); ?>
	<? getNewEntryForm(); ?>
	<?= getStatisticsScript() ?>
</body>
</html>
<?
	endRequest();
?>
