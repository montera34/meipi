<?
	header("Content-type: text/html; charset=iso-8859-1");
	//$configsPath = "../";
	require_once("functions/meipi.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $webName ?>: <?= $webTitle ?> - <?= getString("categories") ?></title>
	<link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/categories.css" />
	<? getMeipiHead() ?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script src="<?= setParams("languageJs.php", null) ?>" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS" href="<?= $mainUrl.setParams("rss.php", null) ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - comments" href="<?= $mainUrl.setParams("rssAllComments.php", null) ?>" />
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<?
	$onLoadScript = getOnLoadScript($_REQUEST);
	echo $onLoadScript;
?></head>
<body <?= getOnLoadCall($onLoadScript) ?> onunload="GUnload()">
	<?= getNavigationBar($_REQUEST, "categories") ?>
<?

	if(isLogged())
		$logged = "yes";
	else
		$logged = "no";

	$userId = getIdUser();

	$categories = getCategories();

	$aParams = getEntriesParamsFromRequest($_REQUEST);
	unset($aParams["id_entry"]);
	$aParams["order by"]="date";
	$aParams["order desc"]="desc";
	$aParams["limit"]=$catLimit;
	$aParams["content"]="yes";

	for($iCategory=0; $iCategory<count($categories); $iCategory++)
	{
		$idCategory = $categories[$iCategory]["id_category"];
		$categoryName = $categories[$iCategory]["category_name"];
		$aParams["category"]="$idCategory";
		$aEntries[$iCategory] = getEntries($aParams);
	}
?>

<div id="meipi">
	<? getMeipiDescription() ?>

	<!--<div class="paginas">
	</div> end class paginas -->

<div id="canales">
<?
	for($iCategory=0; $iCategory<count($categories); $iCategory++)
	{
		$id_category = $categories[$iCategory]["id_category"];
?>	<div id="canal<?= $id_category ?>">
		<div id="hcanal<?= $id_category ?>">
			<div class="tcanal">
				<a href="<?= setParams("list.php", Array("category" => $id_category)) ?>"><?= $categories[$iCategory]["category_name"] ?></a>
				<a style="float: right;" href="<?= setParams("rss.php", Array("category" => $id_category)) ?>"><img src="<?= $commonFiles ?>images/feed.png"/></a>
			</div>
		</div>
<?
		for($iEntry=0; $iEntry<dbGetSelectedRows($aEntries[$iCategory]); $iEntry++)
		{
			$id_entry = $aEntries[$iCategory][$iEntry]["id_entry"];
			$user = $aEntries[$iCategory][$iEntry]["login"];
			$id_user = $aEntries[$iCategory][$iEntry]["id_user"];
			$title = $aEntries[$iCategory][$iEntry]["title"];
			$text = $aEntries[$iCategory][$iEntry]["text"];
			$content = $aEntries[$iCategory][$iEntry]["file"];
			$type = $aEntries[$iCategory][$iEntry]["type"];
			$date = $aEntries[$iCategory][$iEntry]["dateFormatted"];
			$votes = $aEntries[$iCategory][$iEntry]["votes"];
			$cssClass = $aEntries[$iCategory][$iEntry]["css_class"];

			$aTags = getTags($id_entry);

			if(isLogged())
			{
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

?>		<div class="c<?= $categories[$iCategory]["id_category"] ?>_entrada <?= $cssClass ?>" id="<?= $id_entry ?>">
<?
			if(isset($content) && $type==0)
			{
?>
				<div class="c<?= $categories[$iCategory]["id_category"] ?>_ientrada">
					<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><img width="75px" height="75px" src="<?= $dirSquare ?><?= $content ?>" alt="<?= $title ?>" /></a>
				</div>
<?
			}
			else if(isset($content) && ($type==1 || $type==2))
			{
?>
				<div class="c<?= $categories[$iCategory]["id_category"] ?>_ientrada">
					<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><img width="75px" height="75px" src="<?= $commonFiles ?>images/video.gif" alt="<?= $title ?>" /></a>
				</div><!-- end class _ientrada -->
<?
			}
			else if(isset($content) && $type==3)
			{
?>
				<div class="c<?= $categories[$iCategory]["id_category"] ?>_ientrada">
					<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><img width="75px" height="75px" src="<?= $commonFiles ?>images/lively.gif" alt="<?= $title ?>" /></a>
				</div><!-- end class _ientrada -->
<?
			}
			else
			{
?>
				<div class="c<?= $categories[$iCategory]["id_category"] ?>_ientrada">
					<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><img width="75px" height="75px" src="<?= $commonFiles ?>images/no-img.gif" alt="<?= $title ?>" /></a>
				</div><!-- end class _ientrada -->
<?
			}
?>

			<div class="c<?= $categories[$iCategory]["id_category"] ?>_tentrada">
				<div class="c<?= $categories[$iCategory]["id_category"] ?>_hentrada">
				<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= getSubString($title,$charsInCategoriesTitle,$lineInCategoriesTitle) ?></a>
				</div><!-- end class _hentrada -->

				<div class="c<?= $categories[$iCategory]["id_category"] ?>_pentrada">
					<?= getString("by") ?> <strong><a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $user ?></a></strong> -- <?= $date ?><!--| <?= getString("votes") ?>: <?= $votes ?>-->
<?
			if(count($aTags)>0)
			{
?>				<br/>
				<strong><?= getString("Tags") ?></strong>:
<?			for($iTag=0; $iTag<count($aTags); $iTag++)
		  	{
	  	  	$id_tag = $aTags[$iTag]["id_tag"];
  	  		$tag_name = $aTags[$iTag]["tag_name"];
?>				<a href="<?= setParams("list.php", Array("id_tag" => $id_tag)) ?>"><?= $tag_name ?></a><?= ($iTag<count($aTags)-1 ? ", " : "") ?>
<?
	  		}
			}
?>
			</div><!-- end class _pentrada -->

			<?= getSubString(basicHtml(allowedHtml($text)),$charsInCategories,$lineInCategories) ?> <a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><span class="extend">[<?= getString("Extend") ?>]</span></a>

			</div><!-- end class _tentrada -->
		</div><!-- end class _entrada -->
<?
		}
?>
		<div class="cat<?= $categories[$iCategory]["id_category"] ?>_color cat_color"><a href="<?= setParams("list.php", Array("category" => $id_category)) ?>"><?= getString("More"); ?></a></div>
	</div>
<?
  }
?>
</div>

</div>
  <?= getFooter("categories") ?>
	<? getOverEntry("categories"); ?>
	<? getEntryWindow($_REQUEST); ?>
	<? getLoginForm($_REQUEST); ?>
	<? getNewEntryForm(); ?>
	<? getMessageWindow(); ?>
	<?= getStatisticsScript() ?>
</body>
</html>
<?
	    endRequest();
?>
