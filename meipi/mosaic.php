<?
	header("Content-type: text/html; charset=iso-8859-1");
	//$configsPath = "../";
	require_once("functions/meipi.php");

	global $mosaicLastItemsLimit;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $webName ?>: <?= $webTitle ?> - <?= getString("mosaic") ?></title>
	<? getMeipiHead() ?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script src="<?= setParams("languageJs.php", null) ?>" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS" href="<?= $mainUrl.setParams("rss.php", null) ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - comments" href="<?= $mainUrl.setParams("rssAllComments.php", null) ?>" />

	<meta http-equiv="imagetoolbar" content="no" />
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<?
	$onLoadContent = getOnLoadContent($_REQUEST);
?>
	<script language="javascript">
		function onLoad()
		{
			editMosaic();
			<?= $onLoadContent ?>
		}
	</script>
</head>
<body onload="onLoad()" onunload="GUnload()">
	<?= getNavigationBar($_REQUEST, "mosaic") ?>
<?
	if(isset($_REQUEST["save_mosaic"]))
		$aMosaicData = getSquareThumbnailsFromRequest($_REQUEST);
	else if(isset($_REQUEST["id_mosaic"]))
		$aMosaicData = getSquareThumbnails($_REQUEST["id_mosaic"]);
	else
		$aMosaicData = getSquareThumbnailsDefault();
	$aThumbnails = $aMosaicData["thumbnails"];

	$aLastMosaics = getLastMosaics();
?>

<script type="text/javascript" language="javascript">
	var aMosaic=new Array(<?= $mosaicLimit ?>);
	var aThumbsTitle=new Array();
	var aIdContent=new Array();
	var aIdEntry=new Array();
	aIdContent["0"]="0";

	var logged = "<?= (isLogged() ? "yes" : "no") ?>";
	var userId = "<?= getIdUser() ?>";

	var entries=new Array();
<?
		for($iThumbnail=0; $iThumbnail<count($aThumbnails); $iThumbnail++)
		{
			$idEntry = $aThumbnails[$iThumbnail]["id_entry"];
			$id_content = $aThumbnails[$iThumbnail]["id_content"];
			$title = safeForJavascript($aThumbnails[$iThumbnail]["title"]);
			$text = safeForJavascript($aThumbnails[$iThumbnail]["text"]);
			$category = safeForJavascript($aThumbnails[$iThumbnail]["id_category"]);
			$content = $aThumbnails[$iThumbnail]["file"];
			$type = $aThumbnails[$iThumbnail]["type"];
			$id_user = $aThumbnails[$iThumbnail]["id_user"];
			$login = $aThumbnails[$iThumbnail]["login"];
			$date = $aThumbnails[$iThumbnail]["dateFormatted"];

			if(strlen($id_content)>0 && strlen($idEntry)>0)
			{
?>	
				id = <?= $idEntry ?>;
				entries[id]=new Array();

				entries[id]["text"] = "<?= $text ?>";
				entries[id]["title"] = "<?= $title ?>";
				entries[id]["titleOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($title)),$charsInOverEntryTitle,$lineInOverEntryTitle)) ?>';
				entries[id]["textOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($text)),$charsInOverEntry,$lineInOverEntry)) ?>';
				entries[id]["category"] = "<?= $category ?>";
				entries[id]["date"] = "<?= $date ?>";
				entries[id]["id_user"] = "<?= $id_user ?>";
				entries[id]["login"] = "<?= $login ?>";
				entries[id]["file"] = "<?= $content ?>";
				entries[id]["type"] = "<?= $type ?>";
<?
			}
		}
?>
		var selectedItem = 0;
		var selectedItems=new Array();
<?
		if(isLogged())
		{
			$aSelectedItems = getSelectedItems();
			for($iSelectedItem=0; $iSelectedItem<dbGetSelectedRows($aSelectedItems); $iSelectedItem++)
			{
				$idEntry = $aSelectedItems[$iSelectedItem]["id_entry"];
				$idContent = $aSelectedItems[$iSelectedItem]["id_content"];
				$file = $aSelectedItems[$iSelectedItem]["file"];
				$title = safeForJavascript($aSelectedItems[$iSelectedItem]["title"]);
				$text = safeForJavascript($aSelectedItems[$iSelectedItem]["text"]);
				$category = safeForJavascript($aSelectedItems[$iSelectedItem]["id_category"]);
				$content = $aSelectedItems[$iSelectedItem]["file"];
				$type = $aSelectedItems[$iSelectedItem]["type"];
				$id_user = $aSelectedItems[$iSelectedItem]["id_user"];
				$login = safeForJavascript($aSelectedItems[$iSelectedItem]["login"]);
				$date = $aSelectedItems[$iSelectedItem]["dateFormatted"];
?>
				selectedItems[<?= $iSelectedItem ?>] = new Array();
				selectedItems[<?= $iSelectedItem ?>]["id_entry"] = "<?= $idEntry ?>";
				selectedItems[<?= $iSelectedItem ?>]["id_content"] = "<?= $idContent ?>";
				selectedItems[<?= $iSelectedItem ?>]["file"] = "<?= $file ?>";

				id = <?= $idEntry ?>;
				entries[id]=new Array();

				entries[id]["text"] = "<?= $text ?>";
				entries[id]["title"] = "<?= $title ?>";
				entries[id]["titleOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($title)),$charsInOverEntryTitle,$lineInOverEntryTitle)) ?>';
				entries[id]["textOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($text)),$charsInOverEntry,$lineInOverEntry)) ?>';
				entries[id]["category"] = "<?= $category ?>";
				entries[id]["date"] = "<?= $date ?>";
				entries[id]["id_user"] = "<?= $id_user ?>";
				entries[id]["login"] = "<?= $login ?>";
				entries[id]["file"] = "<?= $content ?>";
				entries[id]["type"] = "<?= $type ?>";

				aIdContent["<?= $content ?>"]="<?= $idContent ?>";

<?
		}
	}
?>
	var lastItem = 0;
	var lastItems=new Array();
<?
	$aSquareThumbnails = getSquareThumbnailsDefaultLimit($mosaicLastItemsLimit);
	$aLastItems = $aSquareThumbnails["thumbnails"];
	for($iLastItem=0; $iLastItem<dbGetSelectedRows($aLastItems); $iLastItem++)
	{
		$idEntry = $aLastItems[$iLastItem]["id_entry"];
		$idContent = $aLastItems[$iLastItem]["id_content"];
		$file = $aLastItems[$iLastItem]["file"];
		$title = safeForJavascript($aLastItems[$iLastItem]["title"]);
		$text = safeForJavascript($aLastItems[$iLastItem]["text"]);
		$category = safeForJavascript($aLastItems[$iLastItem]["id_category"]);
		$content = $aLastItems[$iLastItem]["file"];
		$id_user = $aLastItems[$iLastItem]["id_user"];
		$login = safeForJavascript($aLastItems[$iLastItem]["login"]);
		$date = $aLastItems[$iLastItem]["dateFormatted"];
?>
		lastItems[<?= $iLastItem ?>] = new Array();
		lastItems[<?= $iLastItem ?>]["id_entry"] = "<?= $idEntry ?>";
		lastItems[<?= $iLastItem ?>]["id_content"] = "<?= $idContent ?>";
		lastItems[<?= $iLastItem ?>]["file"] = "<?= $file ?>";

		id = <?= $idEntry ?>;
		entries[id]=new Array();

		entries[id]["text"] = "<?= $text ?>";
		entries[id]["title"] = "<?= $title ?>";
		entries[id]["titleOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($title)),$charsInOverEntryTitle,$lineInOverEntryTitle)) ?>';
		entries[id]["textOverEntry"] = '<?= safeForJavascript(getSubString(basicHtml(allowedHtml($text)),$charsInOverEntry,$lineInOverEntry)) ?>';
		entries[id]["category"] = "<?= $category ?>";
		entries[id]["date"] = "<?= $date ?>";
		entries[id]["id_user"] = "<?= $id_user ?>";
		entries[id]["login"] = "<?= $login ?>";
		entries[id]["file"] = "<?= $content ?>";
		entries[id]["type"] = "<?= $type ?>";

		aIdContent["<?= $content ?>"]="<?= $idContent ?>";
<?
	}
?>
	var draggableEntry = null;

	function saveMosaic()
	{
<?
		if(isLogged())
		{
?>
		mosaicNameWindowDiv = document.getElementById("mosaicNameWindow");
		mosaicNameWindowDiv.style.display='';
<?
		}
		else
		{
?>
			var params = "length=35&save_mosaic=true";
			for(i=0; i<35; i++)
			{
				params += "&x_"+i+"="+(i%7);
				params += "&y_"+i+"="+Math.floor(i/7);
				params += "&c_"+i+"="+aIdContent[aMosaic[i]];
			}

			showLoginFormParams(params);
<?
		}
?>
	}

	function confirmSaveMosaic()
	{
		mosaicNameWindowDiv = document.getElementById("mosaicNameWindow");
		mosaicNameWindowDiv.style.display='none';

		var params = "";
		for(i=0; i<35; i++)
		{
			params += "&x_"+i+"="+(i%7);
			params += "&y_"+i+"="+Math.floor(i/7);
			params += "&c_"+i+"="+aIdContent[aMosaic[i]];
		}

		mosaicName = document.forms.mosaicNameForm.mosaicName.value;
		params += "&name="+escape(mosaicName);
//alert(params);

		GDownloadUrl("<?= setParams($commonFiles."actions/newMosaic.php", Array("length" => "35")) ?>"+params, function(data, responseCode) {
			var xml = GXml.parse(data);
			var results = xml.documentElement.getElementsByTagName("result");
			for (var i = 0; i < results.length; i++) {
				showMessage(results[i].getAttribute("description"));
				//alert(results[i].getAttribute("code"));
				//alert(results[i].getAttribute("description"));
			}
		});
	}

	function showNextLastItem(direction)
	{
		if(lastItems.length==0)
		{
			showMessage("<?= getString("No more last items") ?>");
			lastItemDraggable.destroy();
			document.getElementById("itemLast").innerHTML = "";
		}
		else
		{
			lastItem=(((lastItem+direction)%lastItems.length)+lastItems.length)%lastItems.length;
			document.getElementById("itemLast").innerHTML = "<img src=\"<?= $dirSquare ?>"+lastItems[lastItem]["file"]+"\" />";
		}
	}

	function showNextSelectedItem(direction)
	{
		if(selectedItems.length==0)
		{
			showMessage("<?= getString("No more selected items") ?>");
			selectedItemDraggable.destroy();
			document.getElementById("itemSelected").innerHTML = "";
		}
		else
		{
			selectedItem=(((selectedItem+direction)%selectedItems.length)+selectedItems.length)%selectedItems.length;
			document.getElementById("itemSelected").innerHTML = "<img src=\"<?= $dirSquare ?>"+selectedItems[selectedItem]["file"]+"\" />";
		}
	}

	function editMosaic(iSquare)
	{
<?
		for($iThumbnail=0; $iThumbnail<count($aThumbnails); $iThumbnail++)
		{
			$idEntry = $aThumbnails[$iThumbnail]["id_entry"];
			$title = safeForJavascript($aThumbnails[$iThumbnail]["title"]);
			$text = safeForJavascript($aThumbnails[$iThumbnail]["text"]);
			$content = $aThumbnails[$iThumbnail]["file"];
			$id_content = $aThumbnails[$iThumbnail]["id_content"];
?>	
				aMosaic[<?= $iThumbnail ?>]="<?= $content ?>";
				aThumbsTitle["<?= $content ?>"]="<?= $title ?>";
				aIdContent["<?= $content ?>"]="<?= $id_content ?>";
				aIdEntry["<?= $content ?>"]="<?= $idEntry ?>";

<?
		}
		for($iEmptySquare=$iThumbnail; $iEmptySquare<$mosaicLimit; $iEmptySquare++)
		{
?>
				aMosaic[<?= $iEmptySquare ?>]=0;
<?
		}
		for($iThumbnail=0; $iThumbnail<count($aThumbnails); $iThumbnail++)
		{
			$idEntry = $aThumbnails[$iThumbnail]["id_entry"];
			$id_content = $aThumbnails[$iThumbnail]["id_content"];
			if($id_content!="0")
			{
?>
				draggable<?= $iThumbnail ?> = new Draggable('item<?= $iThumbnail ?>', {revert:true, zindex:1} );
<?
			}
		}
?>
		selectedItemDraggable = new Draggable("itemSelected", {revert:true, zindex:1} );
		lastItemDraggable = new Draggable("itemLast", {revert:true, zindex:1} );
<?
		for($iSquare=0; $iSquare<$mosaicLimit; $iSquare++)
		{
?>
			addDroppableSquare(<?= $iSquare ?>);
<?
		}
?>
		Droppables.add('mosaicTrash', {accept:'mosaicItem', onDrop:function(element)
		{
			if(element.id.substring(4)=="Selected")
			{
				idContent=selectedItems[selectedItem]["id_content"];
				selectedItems.splice(selectedItem, 1);
				showNextSelectedItem(0);
				unselectFromMosaic('<?= $idMosaic ?>', idContent);
			}
			else if(element.id.substring(4)=="Last")
			{
				lastItems.splice(lastItem, 1);
				showNextLastItem(0);
			}
			else
			{
				i = parseInt(element.id.substring(4));
				aMosaic[i] = 0;
				document.getElementById("item"+i).innerHTML = "";
				//Draggables.endDrag();
				eval("draggable" + i + ".destroy()");
			}
		}
		});
		document.getElementById("mosaicTrash").innerHTML = "<?= getString("Trash") ?>";
		document.getElementById("mosaicButtons").innerHTML = "<input type=\"button\" value=\"<?= getString("saveMosaic") ?>\" onClick=\"javascript:saveMosaic()\" />";
	}

	function addDroppableSquare(iSquare)
	{
		Droppables.add('square'+iSquare, {accept:'mosaicItem', hoverclass:'selectedDroppable', onDrop:function(element,element2)
		{
			if(element.id.substring(4)=="Selected")
			{
				tempId = aMosaic[iSquare];
				aMosaic[iSquare] = selectedItems[selectedItem]["file"];
				idEntry = selectedItems[selectedItem]["id_entry"];
				document.getElementById("item"+iSquare).innerHTML = "<a href=\"javascript:showEntryData('<?= $idMeipi ?>', '"+idEntry+"', '<?= $dirThumbnail ?>', '<?= $dirEntry ?>',userId,logged)\"><img style=\"z-index: 0; left: 0px; top: 0px; opacity: 0.99999;\" src=\"<?= $dirSquare ?>"+aMosaic[iSquare]+"\" alt=\"<?= $title ?>\" title=\"\" /></a>";
				if(tempId == 0){
					eval("draggable" + iSquare + " = new Draggable('item" + iSquare + "', {revert:true, zindex:1} )");
				}
			}
			else if(element.id.substring(4)=="Last")
			{
				tempId = aMosaic[iSquare];
				aMosaic[iSquare] = lastItems[lastItem]["file"];
				idEntry = lastItems[lastItem]["id_entry"];
				document.getElementById("item"+iSquare).innerHTML = "<a href=\"javascript:showEntryData('<?= $idMeipi ?>', '"+idEntry+"', '<?= $dirThumbnail ?>', '<?= $dirEntry ?>',userId,logged)\"><img style=\"z-index: 0; left: 0px; top: 0px; opacity: 0.99999;\" src=\"<?= $dirSquare ?>"+aMosaic[iSquare]+"\" alt=\"<?= $title ?>\" title=\"\" /></a>";
				if(tempId == 0){
					eval("draggable" + iSquare + " = new Draggable('item" + iSquare + "', {revert:true, zindex:1} )");
				}
			}
			else
			{
				i = parseInt(element.id.substring(4));
				//document.getElementById("mosaicTrash").innerHTML = "Moved " + element.id + " from square" + i + " over item" + iSquare + " at " + element2.id + " (" + aMosaic[i] + "," + aMosaic[iSquare] + ")";
				tempId = aMosaic[iSquare];
				aMosaic[iSquare] = aMosaic[i];
				aMosaic[i] = tempId;
				//document.getElementById("item"+iSquare).innerHTML = "<img src=\"<?= $dirSquare ?>" + aMosaic[iSquare] + "\" />";
				idEntry = aIdEntry[aMosaic[iSquare]];
				document.getElementById("item"+iSquare).innerHTML = "<a href=\"javascript:showEntryData('<?= $idMeipi ?>', '"+idEntry+"', '<?= $dirThumbnail ?>', '<?= $dirEntry ?>',userId,logged)\"><img style=\"z-index: 0; left: 0px; top: 0px; opacity: 0.99999;\" src=\"<?= $dirSquare ?>"+aMosaic[iSquare]+"\" alt=\"<?= $title ?>\" title=\"\" /></a>";
				if(tempId != 0){
					//document.getElementById("item"+i).innerHTML = "<img src=\"<?= $dirSquare ?>" + aMosaic[i] + "\" />";
					idEntry = aIdEntry[aMosaic[i]];
					document.getElementById("item"+i).innerHTML = "<a href=\"javascript:showEntryData('<?= $idMeipi ?>', '"+idEntry+"', '<?= $dirThumbnail ?>', '<?= $dirEntry ?>',userId,logged)\"><img style=\"z-index: 0; left: 0px; top: 0px; opacity: 0.99999;\" src=\"<?= $dirSquare ?>"+aMosaic[i]+"\" alt=\"<?= $title ?>\" title=\"\" /></a>";
				}
				else{
			  	document.getElementById("item"+i).innerHTML = "";
					eval("draggable" + i + ".destroy()");
					eval("draggable" + iSquare + " = new Draggable('item" + iSquare + "', {revert:true, zindex:1} )");
				}
			}
		}
		});
	}

</script>

<div id="meipi">
	<? getMeipiDescription() ?>

<div id="lienzo">

<div id="mosaicDesc">
	<?= getString("Mosaic") ?>: <strong><?= $aMosaicData["mosaicDesc"]?></strong>
<?
	if(isset($aMosaicData["mosaicAuthorName"]))
	{
?>
		<?= getString("by") ?> <a href="<?= setParams("list.php", Array("id_user" => $aMosaicData["mosaicAuthorId"])) ?>"><?= $aMosaicData["mosaicAuthorName"]?></a>
<?
	}
?>
</div><!-- end id mosaicDesc -->

<div id="mosaic">

<?
	for($iThumbnail=0; $iThumbnail<count($aThumbnails); $iThumbnail++)
	{
		$idEntry = $aThumbnails[$iThumbnail]["id_entry"];
		$title = $aThumbnails[$iThumbnail]["title"];
		// coordinates too?
		$content = $aThumbnails[$iThumbnail]["file"];
		$id_content = $aThumbnails[$iThumbnail]["id_content"];

		?><div class="mosaicSquare" id="square<?= $iThumbnail ?>"><div class="mosaicItem" id="item<?= $iThumbnail ?>"><?
		if($id_content!="0")
		{
		?><a href="javascript:showEntryData('<?= $idMeipi ?>', '<?= $idEntry ?>', '<?= $dirThumbnail ?>', '<?= $dirEntry ?>',userId,logged)"><img style="z-index: 0; left: 0px; top: 0px; opacity: 0.99999;" src="<?= $dirSquare ?><?= $content ?>" alt="<?= $title ?>" title="" /></a><?
		}
		?></div></div><?
	}
	for($iEmptySquare=$iThumbnail; $iEmptySquare<$mosaicLimit; $iEmptySquare++)
	{
?><div class="mosaicSquare" id="square<?= $iEmptySquare ?>"><div class="mosaicItem" id="item<?= $iEmptySquare ?>"></div></div><?
	}
?>
</div><!-- end id mosaic -->

<div id="mosaicSide">

	<div id="mosaicTrash"></div>

	<div id="mosaicButtons">
<?
		/*if(isLogged())
		{
?>		<input type="button" value="<?= getString("editMosaic") ?>" onClick="javascript:editMosaic();" />
<?
		}
		else
		{
?>		<input type="button" value="<?= getString("editMosaic") ?>" onClick="javascript:showLoginForm();" />
<?
		}*/
?>
	</div><!-- end id mosaicButtons -->

	<div id="lastMosaics">
		<h2><?= getString("Last Mosaics") ?></h2>
<?
	for($iLastMosaics=0; $iLastMosaics<count($aLastMosaics); $iLastMosaics++)
	{
    $lastMosaicName = $aLastMosaics[$iLastMosaics]["name"];
    $lastMosaicIdMosaic = $aLastMosaics[$iLastMosaics]["id_mosaic"];
    $lastMosaicUser = $aLastMosaics[$iLastMosaics]["login"];
    $lastMosaicIdUser = $aLastMosaics[$iLastMosaics]["id_user"];
?>
		<p><a href="<?= setParams("mosaic.php", Array("id_mosaic" => $lastMosaicIdMosaic )) ?>"><?= $lastMosaicName ?></a> <?= getString("from") ?> <a href="<?= setParams("list.php", Array("id_user" => $lastMosaicIdUser )) ?>"><?= $lastMosaicUser ?></a> </p>

<?
	}
?>
	</div><!-- end id lastMosaics -->

	<div id="selected_items">
		<h2><?= getString("Selected items") ?></h2>
		<div class="numerosdepagina"><nobr>
			<a href="javascript:showNextSelectedItem(-1);">&larr;</a>
			<a href="javascript:showNextSelectedItem(1);">&rarr;</a>
		</nobr></div>
		<div id="itemSelected" class="mosaicItem">
<?
			if(isLogged() && dbGetSelectedRows($aSelectedItems)>0)
			{
?>
				<img src="<?= $dirSquare.$aSelectedItems[0]["file"] ?>" />
<?
			}
?>
		</div>
	</div>

	<div id="last_items">
		<h2><?= getString("Last items") ?></h2>
		<div class="numerosdepagina"><nobr>
			<a href="javascript:showNextLastItem(-1);">&larr;</a>
			<a href="javascript:showNextLastItem(1);">&rarr;</a>
		</nobr></div>
		<div id="itemLast" class="mosaicItem">
<?
			if(dbGetSelectedRows($aLastItems)>0)
			{
?>
				<img src="<?= $dirSquare.$aLastItems[0]["file"] ?>" />
<?
			}
?>
		</div>
	</div>
</div><!-- end id mosaicSide -->
<div id="entradas-pag"> 
			<div class="entradasNumber">
				<?= $iRows ?> <?= getString("entries") ?>
			</div>
			<div class="numerosdepagina" style="text-align: left;">
<!-- meter codigo de mosaicos previos				<? getPageNumbers($iRows,$aParams["page"],"list.php"); ?> -->
			</div> <!-- end class numerosdepagina -->
		</div> <!-- end id entradas-pagina -->

</div><!-- end id lienzo -->
</div><!-- end id meipi -->

	<? getMosaicNameWindow(); ?>

	<?= getFooter("mosaic") ?>
	<? getOverEntry("mosaic"); ?>

	<? getEntryWindow($_REQUEST); ?>
	<? getLoginForm($_REQUEST); ?>
	<? getNewEntryForm(); ?>
	<? getMessageWindow(); ?>
	<?= getStatisticsScript() ?>
</body>
</html>
