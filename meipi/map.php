<?
	header("Content-type: text/html; charset=iso-8859-1");
	//$configsPath = "../";
	require_once "functions/meipi.php";

global $centerAddress, $zoomLevel, $labelMaxLength;

$labelMaxLength = intval($labelMaxLength);
if($labelMaxLength<=0)
{
	$labelMaxLength=15;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $webName ?>: <?= $webTitle ?> - <?= getString("") ?></title>
	<link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/map.css" />
	<? getMeipiHead() ?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script src="<?= setParams("languageJs.php", null) ?>" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>

	<script src="<?= $commonFiles ?>js/legend.js" type="text/javascript"></script>
<?
	global $aCities;
	if(isset($aCities[1]))
	{
?>
	<script src="<?= $commonFiles ?>js/map.js" type="text/javascript"></script>
<?
	}
	if(strlen($archiveParam)>0)
	{
?>
	<script src="<?= $commonFiles ?>js/archive.js" type="text/javascript"></script>
<?
	}
?>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS" href="<?= $mainUrl.setParams("rss.php", null) ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - comments" href="<?= $mainUrl.setParams("rssAllComments.php", null) ?>" />
	
	<script type="text/javascript">
		//<![CDATA[

			var map = null;
			var geocoder = null;
			//var entries = new Array();
			var entries = {};

			var draggableEntry = null;

			var logged = "<?= (isLogged() ? "yes" : "no") ?>";
			var userId = "<?= getIdUser() ?>";

			var activeMarkerId = null;

			function load() {
				if (GBrowserIsCompatible()) {
				 	map = new GMap2(document.getElementById("map"),G_SATELLITE_MAP);
<?
					if($_REQUEST["embedded"]=="true")
					{
?>
						map.addControl(new GSmallMapControl());
						<? if ($viewButtons == "show") { ?>
							map.addControl(new GMapTypeControl({titleSize: 1}));
						<? } ?>
<?
					}
					else
					{
?>
						map.addControl(new GLargeMapControl(),new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,55)));
						<? if ($viewButtons == "show") { ?>
							map.addControl(new GMapTypeControl({titleSize: 1}),new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,55)));
						<? } ?>
<?
					}
?>
					GEvent.addListener(map, "moveend", function() {
						var center = map.getCenter();
						//document.getElementById("message").innerHTML = center.toString();
						loadData();
					});

					// Custom map control: cities list
<?
					if(isset($aCities[1]))
					{
?>
					var aCities = new Array();
<?
						for($i=0; $i<count($aCities); $i++)
						{
							$aCity = $aCities[$i];
							$cityValue = safeForJavascript($aCity["city"]);
							$cityTitle = safeForJavascript($aCity["title"]);
							if(strlen($cityTitle)==0)
							{
								$cityTitle = $cityValue;
							}
							$cityAddress = safeForJavascript($aCity["address"]);
?>
					aCities.push(new Array("<?= $cityTitle ?>", "<?= $cityValue ?>", "<?= $cityAddress ?>"));
<?
						}
?>
					map.addControl(new CityControl(aCities), new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10, 40)));
<?
					}
					
					// Custom map control: Archived entries
					if(strlen($archiveParam)>0)
					{
						$all = ($_REQUEST["all"]=="true" ? "" : "true");
						$allText = ($_REQUEST["all"]=="true" ? getString("viewOnlyActive") : getString("viewArchived"));
?>
					map.addControl(new ArchiveControl("<?= setParams("map.php?".$_SERVER["QUERY_STRING"], Array("all" => $all)) ?>", "<?= $allText ?>"), new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10, 85)));
<?
					}
?>

					<? /* Map legend - start */ ?>
					map.addControl(new LegendControl(
							{categories:
								[
<?
										$categories = getCategories();
										$hasPrevious = FALSE;
										foreach($categories as $categoryId => $category)
										{
											if($hasPrevious)
											{
												echo ",";
											}
											else
											{
												$hasPrevious = TRUE;
											}
?>
												{
													id: "<?= $category["id_category"] ?>",
													name: "<?= $category["category_name"] ?>",
													image: "<?= ($useIcons=="true" ? "/images/icon_".$category["id_category"].".png" : "/meipi/label.php?text=meipi&cat=".$category["id_category"]."&width=38&height=24&stand=0") ?>"
												}
<?
										}
?>
								],
								filter: true
							}));
					<? /* Map legend - end */ ?>

					map.enableContinuousZoom();
					map.enableDoubleClickZoom();
					GEvent.addDomListener(document.getElementById("map"), 'DOMMouseScroll', wheelZoomMap);
					GEvent.addDomListener(document.getElementById("map"), 'mousewheel', wheelZoomMap);

					geocoder = new GClientGeocoder();
<? switch($defaultMapView)
{
	case "sat":
		$mapType=", G_SATELLITE_MAP";
		break;
	case "hyb":
		$mapType=", G_HYBRID_MAP";
		break;
	case "map":
	default:
		$mapType="";
		break;
}
?>
					map.setCenter(new GLatLng(37.4419, -122.1419), <?= $zoomLevel ?>);
					if(typeof startLongitude!="undefined" && startLongitude!=null)
					{
						map.setCenter(new GLatLng(startLatitude, startLongitude), <?= $zoomLevel ?><?= $mapType ?>);
					}
					else
					{
<?
						if(strlen($centerAddress)>0)
						{
?>						showAddress('<?= safeForJavascript(str_replace("'", "", decode($centerAddress))) ?>, <?= safeForJavascript(str_replace("'", "", decode($city))) ?>');
<?
						}
						else
						{
?>						showAddress('<?= safeForJavascript(str_replace("'", "", decode($city))) ?>');
<?
						}
?>
					}
					if(typeof startId!="undefined" && startId!=null)
					{
						//map.addOverlay(entries[startId]["marker"]);
						showMarker(startId);
						activeMarkerId = startId;
						showEntryData('<?= $idMeipi ?>', startId, "<?= $dirThumbnail ?>", '<?= $dirEntry ?>',userId,logged);
					}
					else
					{
						if(logged=="no")
						{
							//showHelpInfo();
						}
					}
				}
<?
	$onLoadContent = getOnLoadContent($_REQUEST);
	echo $onLoadContent;
?>
			}

			function showAddress(address) {
				if (geocoder) {
					geocoder.getLatLng(
						address,
						function(point) {
							if (!point) {
								showMessage(address + " not found");
							} else {
								map.setCenter(point, <?= $zoomLevel ?><?= $mapType ?>);
								//var marker = new GMarker(point);
								//map.addOverlay(marker);
								//marker.openInfoWindowHtml(address);
							}
						}
					);
				}
			}

			function createGIcon(idEntry, text, cat, width, height, stand, img) {
<?
				global $useIcons;
				if($useIcons=="true")
				{
?>				var icon = new GIcon(G_DEFAULT_ICON);
				if(img && img.length>0)
				{
					icon.image = img;
					icon.iconSize = new GSize(width, height);
				}
				else if("1"==stand)
				{
					icon.image = "<?= $commonFiles ?>images/icon_"+cat+"_out.png";
				}
				else
				{
					icon.image = "<?= $commonFiles ?>images/icon_"+cat+".png";
				}
<?
				}
				else
				{
?>				var icon = new GIcon();
				if(img && img.length>0)
				{
					icon.image = img;
					icon.iconAnchor = new GPoint(width/2, height);
				}
				else
				{
					icon.image = "<?= $iconsPath.setParams("label.php", null) ?>?id_entry="+idEntry+"&text="+escape(text)+"&cat="+cat+"&width="+width+"&height="+height+"&stand="+stand;
					icon.iconAnchor = new GPoint(width-5, height);
				}
				icon.iconSize = new GSize(width, height);
<?
				}
?>
				return icon;
			}

			function orderOfCreation(marker,b) {
				return 1;
			}
			function activeMarker(marker,b) {
				return 2;
			}

			function createGMarker(id, lat, lng, icon, stand) {
				var point = new GLatLng(parseFloat(lat), parseFloat(lng));
				//var marker = new GMarker(point,icon);
				if (stand == "1")
				{
					var marker = new GMarker(point,{zIndexProcess:activeMarker, icon:icon});
				}
				else
				{
					var marker = new GMarker(point,{zIndexProcess:orderOfCreation, icon:icon});
				}
				GEvent.addListener(marker, "click", function() {
					//marker.openInfoWindowHtml(text);
					showEntryData('<?= $idMeipi ?>', id, "<?= $dirThumbnail ?>", '<?= $dirEntry ?>',userId,logged);

					if (activeMarkerId != null)
					{
						// TODO: removeOverlay and addOverlay to display it above
						auxMarker = entries[activeMarkerId]["marker"];
						entries[activeMarkerId]["marker"] = createGMarker(activeMarkerId, entries[activeMarkerId]["lat"], entries[activeMarkerId]["lng"], entries[activeMarkerId]["icon"], "0");
						//map.addOverlay(entries[activeMarkerId]["marker"]);
						showMarker(activeMarkerId);
						map.removeOverlay(auxMarker);
					}

					// TODO: removeOverlay and addOverlay to display it above
					auxMarker = entries[id]["marker"];
					entries[id]["marker"] = createGMarker(id, entries[id]["lat"], entries[id]["lng"], entries[id]["iconAct"], "1");
					//map.addOverlay(entries[id]["marker"]);
					showMarker(id);
					map.removeOverlay(auxMarker);

					activeMarkerId = id;
				});
				return marker;
			}
			
			var lastBounds = null;

			function loadData() {
				<?
				if(strlen($_SERVER["QUERY_STRING"])>0)
				{
?>				var params = "?<?= $_SERVER["QUERY_STRING"] ?>&";
<?			}
				else
				{
?>				var params = "?";
<?			}
?>				params += "min_lat="+map.getBounds().getSouthWest().lat();
				params += "&max_lat="+map.getBounds().getNorthEast().lat();
				params += "&min_lon="+map.getBounds().getSouthWest().lng();
				params += "&max_lon="+map.getBounds().getNorthEast().lng();
//alert(params);
//alert(map.getBounds().width);
				GDownloadUrl("<?= setParams("data.php", null) ?>"+params, function(data, responseCode) {
					var xml = GXml.parse(data);
					if(null==xml.documentElement)
						alert(responseCode+" "+data+" "+xml.documentElement);
					var markers = xml.documentElement.getElementsByTagName("marker");
					for (var i = 0; i < markers.length; i++) {
						var id = markers[i].getAttribute("id");
						if(undefined==entries[id])
						{
							entries[id] = new Array();
							entries[id]["id"]=id;
							entries[id]["lat"] = GXml.value(markers[i].getElementsByTagName("lat")[0]);
							entries[id]["lng"] = GXml.value(markers[i].getElementsByTagName("lng")[0]);
							entries[id]["title"] = GXml.value(markers[i].getElementsByTagName("title")[0]);
							//alert(markers[i].getAttribute("text"));
							entries[id]["text"] = GXml.value(markers[i].getElementsByTagName("text")[0]);
							//alert(entries[id]["text"]);
							entries[id]["titleOverEntry"] = GXml.value(markers[i].getElementsByTagName("titleOverEntry")[0]);
							entries[id]["textOverEntry"] = GXml.value(markers[i].getElementsByTagName("textOverEntry")[0]);
							entries[id]["idCategory"] = GXml.value(markers[i].getElementsByTagName("idCategory")[0]);
							entries[id]["category"] = GXml.value(markers[i].getElementsByTagName("idCategory")[0]);
							entries[id]["file"] = GXml.value(markers[i].getElementsByTagName("file")[0]);
							entries[id]["type"] = GXml.value(markers[i].getElementsByTagName("type")[0]);
							entries[id]["id_user"] = GXml.value(markers[i].getElementsByTagName("id_user")[0]);
							entries[id]["login"] = GXml.value(markers[i].getElementsByTagName("login")[0]);
							entries[id]["date"] = GXml.value(markers[i].getElementsByTagName("dateFormatted")[0]);
							entries[id]["tagString"] = GXml.value(markers[i].getElementsByTagName("tagString")[0]);
							entries[id]["iconText"] = GXml.value(markers[i].getElementsByTagName("iconText")[0]);
							entries[id]["iconWidth"] = GXml.value(markers[i].getElementsByTagName("iconWidth")[0]);
							entries[id]["iconHeight"] = GXml.value(markers[i].getElementsByTagName("iconHeight")[0]);
							entries[id]["iconImage"] = GXml.value(markers[i].getElementsByTagName("iconImage")[0]);
							entries[id]["iconImageStand"] = GXml.value(markers[i].getElementsByTagName("iconImageStand")[0]);
							entries[id]["userVote"] = GXml.value(markers[i].getElementsByTagName("userVote")[0]);
							entries[id]["icon"] = createGIcon(id, entries[id]["iconText"], entries[id]["idCategory"], entries[id]["iconWidth"], entries[id]["iconHeight"], "0", entries[id]["iconImage"]);
							entries[id]["iconAct"] = createGIcon(id, entries[id]["iconText"], entries[id]["idCategory"], entries[id]["iconWidth"], entries[id]["iconHeight"], "1", entries[id]["iconImageStand"]);
							entries[id]["marker"] = createGMarker(id, entries[id]["lat"], entries[id]["lng"], entries[id]["icon"], "0");
							//map.addOverlay(entries[id]["marker"]);
							showMarker(id);
						}
					}
				});
			}

<?
			// Set this variables to start centered
				if(isset($_REQUEST["id_entry"]))
				{
					$id_entry = $_REQUEST["id_entry"];
					$aParams = getEntriesParamsFromRequest($_REQUEST);
					$aParams["content"] = "yes";
					$aParams["located"] = "yes";
					$aEntry = getEntries($aParams);
					if(dbGetSelectedRows($aEntry)>0)
					{
						$aTags = getTags($id_entry);
						switch(intval(count($aTags)))
						{
							case "0":
								$tagString = addslashes($aEntry[0]["title"]);
								break;
							case "1":
								$tagString = addslashes($aTags[0]["tag_name"]);
								break;
							default:
								$tagString = addslashes($aTags[0]["tag_name"].",".$aTags[1]["tag_name"]);
								break;
						}
						$aEntry[0]["tagString"] = $tagString;
						$aEntry[0]["iconText"] = getSubString($aEntry[0]["title"], $labelMaxLength);
						$aIconText = getStringSize(htmlDecode($aEntry[0]["iconText"]));
						if(strlen($aEntry[0]["iconImage"])==0)
						{
							$aEntry[0]["iconWidth"] = addslashes($aIconText["width"]);
							$aEntry[0]["iconHeight"] = addslashes($aIconText["height"]) + $iconOffsetH;
						}

?>			var startLongitude = <?= doubleval($aEntry[0]["longitude"]) ?>;
			var startLatitude = <?= doubleval($aEntry[0]["latitude"]) ?>;
			var startId = <?= intval($aEntry[0]["id_entry"]) ?>;

			if(undefined==entries[startId])
			{
				entries[startId] = new Array();
				entries[startId]["id"]=startId;
				entries[startId]["lat"] = <?= $aEntry[0]["latitude"] ?>;
				entries[startId]["lng"] = <?= $aEntry[0]["longitude"] ?>;
				entries[startId]["title"] = '<?= safeForJavascript($aEntry[0]["title"]) ?>';
				entries[startId]["text"] = '<?= safeForJavascript($aEntry[0]["text"]) ?>';
				entries[startId]["titleOverEntry"] = '<?= getSubString(basicHtml(allowedHtml(safeForJavascript($aEntry[0]["title"]))),$charsInOverEntryTitle,$lineInOverEntryTitle) ?>';
				entries[startId]["textOverEntry"] = '<?= getSubString(basicHtml(allowedHtml(safeForJavascript($aEntry[0]["text"]))),$charsInOverEntry,$lineInOverEntry) ?>';
				entries[startId]["idCategory"] = <?= $aEntry[0]["id_category"] ?>;
				entries[startId]["category"] = <?= $aEntry[0]["id_category"] ?>;
				entries[startId]["file"] = '<?= $aEntry[0]["file"] ?>';
				entries[startId]["type"] = '<?= $aEntry[0]["type"] ?>';
				entries[startId]["id_user"] = <?= $aEntry[0]["id_user"] ?>;
				entries[startId]["login"] = '<?= safeForJavascript($aEntry[0]["login"]) ?>';
				entries[startId]["date"] = '<?= $aEntry[0]["dateFormatted"] ?>';
				entries[startId]["tagString"] = '<?= safeForJavascript($aEntry[0]["tagString"]) ?>';
				entries[startId]["iconText"] = '<?= safeForJavascript($aEntry[0]["iconText"]) ?>';
				entries[startId]["iconWidth"] = "<?= $aEntry[0]["iconWidth"] ?>";
				entries[startId]["iconHeight"] = "<?= $aEntry[0]["iconHeight"] ?>";
				entries[startId]["icon"] = createGIcon(startId, entries[startId]["iconText"], entries[startId]["idCategory"], entries[startId]["iconWidth"], entries[startId]["iconHeight"], "0"<?= strlen($aEntry[0]["iconImage"])>0 ? ", \"".$aEntry[0]["iconImage"]."\"" : "" ?>);
				entries[startId]["iconAct"] = createGIcon(startId, entries[startId]["iconText"], entries[startId]["idCategory"], entries[startId]["iconWidth"], entries[startId]["iconHeight"], "1"<?= strlen($aEntry[0]["iconImageStand"])>0 ? ", \"".$aEntry[0]["iconImageStand"]."\"" : "" ?>);

				entries[startId]["marker"] = createGMarker(startId, entries[startId]["lat"], entries[startId]["lng"], entries[startId]["iconAct"], "1");
				//entries[startId]["marker"] = createGMarker(startId, entries[startId]["lat"], entries[startId]["lng"], entries[startId]["title"], entries[startId]["idCategory"], entries[startId]["iconWidth"], entries[startId]["iconHeight"]);
				//alert(startId);
			}
<?				}
				}
				else if(isset($_REQUEST["startLongitude"]) && isset($_REQUEST["startLatitude"]))
				{
?>			var startLongitude = <?= doubleval($_REQUEST["startLongitude"]) ?>;
			var startLatitude = <?= doubleval($_REQUEST["startLatitude"]) ?>;
<?
				}
?>

			function mapGoTo()
			{
				address = document.forms.address.address.value;
				//mapGoToAddress(address, true);
				mapGoToAddress(address, true);
			}

			var mapMarker = null;
			function mapGoToAddress(address, add)
			{
				if (geocoder)
				{
					var fullAddress = address;
					if(add)
					{
						fullAddress += ", "+getString("City");
					}

					geocoder.getLatLng(
						fullAddress,
						function(point) {
								if (!point) {
									if(add)
									{
										//mapGoToAddress(address+", "+getString("City"), false);
										mapGoToAddress(address, false);
									}
									else
									{
										showMessage("Address not found");
									}
								}
								else
								{
									map.setCenter(point, <?= $zoomLevel ?>);
									if(mapMarker==null)
									{
										mapMarker = new GMarker(point);
										map.addOverlay(mapMarker);
									}
									else
									{
										mapMarker.setPoint(point);
									}
								}
						});
					}
				}

				var filtered = {};
				function setCategoryFilter(category, value) {
					filtered[category] = value;
					for(idEntry in entries) {
						showMarker(idEntry);
					}
				}

				function showMarker(idEntry) {
					if(isFiltered(idEntry)) {
						map.removeOverlay(entries[idEntry]["marker"]);
					}
					else {
						if(entries[idEntry]["marker"])
							map.addOverlay(entries[idEntry]["marker"]);
					}
				}
				
				function isFiltered(idEntry) {
					return filtered[entries[idEntry].category]===false;
				}
			//]]>
	</script>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body onload="load()" onunload="GUnload()">
	<div id="map"></div>

	<?= getNavigationBar($_REQUEST, "map") ?>

	<? getOverEntry("map"); ?>

	<?= getFooter("map") ?>
	<? getEntryWindow($_REQUEST); ?>
	<? getLoginForm($_REQUEST); ?>
	<? getNewEntryForm(); ?>
	<? getMessageWindow(); ?>
	<?= getStatisticsScript() ?>
</body>
</html>

