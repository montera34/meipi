<?
	header("Content-type: text/html; charset=iso-8859-1");
	//$configsPath = "../";
	require_once "functions/meipi.php";

global $centerAddress, $zoomLevel, $zoomLevelLastEntry, $webDescription, $longDesc, $longDescription;

$labelMaxLength = intval($labelMaxLength);
if($labelMaxLength<=0)
{
	$labelMaxLength=15;
}
if(intval($zoomLevelLastEntry)==0)
{
	$zoomLevelLastEntry = $zoomLevel;
}

	if(isLogged())
		$logged = "yes";
	else
		$logged = "no";

	$userId = getIdUser();

	// LIST
	$aParamsList = getEntriesParamsFromRequest($_REQUEST);
	$aParamsList["order by"]="date";
	$aParamsList["order desc"]="desc";
	$aParamsList["getRows"]="yes";
	$aParamsList["page"]="1";
	$aParamsList["limit"]="4";
	$aParamsList["content"] = "yes";
	unset($aParamsList["id_entry"]);
	$aEntriesList = getEntries($aParamsList);

	$lastEntry = $aEntriesList[0]["id_entry"];
	$_REQUEST["id_entry"] = $lastEntry;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $webName ?>: <?= $webTitle ?></title>
  <link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/index.css" />
  <link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/categories.css" />
	<? getMeipiHead() ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $google_maps_key ?>" type="text/javascript"></script>
	<script src="<?= setParams("languageJs.php", null) ?>" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="<?= $commonFiles ?>js/functions.js" type="text/javascript"></script>
<?
	global $aCities;
	if(isset($aCities[1]))
	{
?>
	<script src="<?= $commonFiles ?>js/map.js" type="text/javascript"></script>
<?
	}
?>
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS" href="<?= $mainUrl.setParams("rss.php", null) ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?= $webName ?> RSS - comments" href="<?= $mainUrl.setParams("rssAllComments.php", null) ?>" />
	
	<script type="text/javascript">
		//<![CDATA[

			var map = null;
			var geocoder = null;
			var entries = new Array();

			var draggableEntry = null;

			var logged = "<?= (isLogged() ? "yes" : "no") ?>";
			var userId = "<?= getIdUser() ?>";

			var activeMarkerId = null;

			function load() {
				if (GBrowserIsCompatible()) {
				 	map = new GMap2(document.getElementById("map"),G_SATELLITE_MAP);
					map.addControl(new GSmallMapControl(),new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,10)));
					<? if ($viewButtons == "show") { ?>
						map.addControl(new GMapTypeControl({titleSize: 1}),new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,10)));
					<? } ?>
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
?>

					map.enableContinuousZoom();
					map.enableDoubleClickZoom();
					GEvent.addDomListener(document.getElementById("map"), 'DOMMouseScroll', wheelZoomMap);
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
					//map.setCenter(new GLatLng(37.4419, -122.1419), <?= $zoomLevel ?>);
					if(typeof startLongitude!="undefined" && startLongitude!=null)
					{
						map.setCenter(new GLatLng(startLatitude, startLongitude), <?= $zoomLevelLastEntry ?><?= $mapType ?>);
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
						map.addOverlay(entries[startId]["marker"]);
						activeMarkerId = startId;
						//showEntryData('<?= $idMeipi ?>', startId, "<?= $dirThumbnail ?>", '<?= $dirEntry ?>',userId,logged);
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
				var icon = new GIcon(G_DEFAULT_ICON);
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
						auxMarker = entries[activeMarkerId]["marker"];
						entries[activeMarkerId]["marker"] = createGMarker(activeMarkerId, entries[activeMarkerId]["lat"], entries[activeMarkerId]["lng"], entries[activeMarkerId]["icon"], "0");
						map.addOverlay(entries[activeMarkerId]["marker"]);
						map.removeOverlay(auxMarker);
					}

					auxMarker = entries[id]["marker"];
					entries[id]["marker"] = createGMarker(id, entries[id]["lat"], entries[id]["lng"], entries[id]["iconAct"], "1");
					map.addOverlay(entries[id]["marker"]);
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
							map.addOverlay(entries[id]["marker"]);
						}
					}
				});
			}

<?
			// Set this variables to start centered
				if(isset($_REQUEST["id_entry"]))
				{
					$id_entry = $_REQUEST["id_entry"];
					$aParamsEntry = getEntriesParamsFromRequest($_REQUEST);
					$aParamsEntry["content"] = "yes";
					$aParamsEntry["located"] = "yes";
					$aEntry = getEntries($aParamsEntry);
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
						$aTagString = getStringSize(htmlDecode($tagString));
						$aEntry[0]["tagString"] = $tagString;
						//$aEntry[0]["iconWidth"] = addslashes($aTagString["width"]);
						//$aEntry[0]["iconHeight"] = addslashes($aTagString["height"]);
						$iconText = getSubString($aEntry[0]["title"], $labelMaxLength);
						$aIconText = getStringSize(htmlDecode($iconText));
						$iconWidth = addslashes($aIconText[width]);
						$iconHeight = addslashes($aIconText[height]);
						if(strlen($aEntry[0]["iconImage"])==0)
						{
							$aEntry[0]["iconWidth"] = $iconWidth;
							$aEntry[0]["iconHeight"] = $iconHeight + $iconOffsetH;
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
				entries[startId]["titleOverEntry"] = '<?= getSubString(basicHtml(allowedHtml(safeForJavascript($aEntry[0]["title"]),$charsInOverEntryTitle)),$lineInOverEntryTitle) ?>';
				entries[startId]["textOverEntry"] = '<?= getSubString(basicHtml(allowedHtml(safeForJavascript($aEntry[0]["text"]),$charsInOverEntry)),$lineInOverEntry) ?>';
				entries[startId]["idCategory"] = <?= $aEntry[0]["id_category"] ?>;
				entries[startId]["category"] = <?= $aEntry[0]["id_category"] ?>;
				entries[startId]["file"] = '<?= $aEntry[0]["file"] ?>';
				entries[startId]["type"] = '<?= $aEntry[0]["type"] ?>';
				entries[startId]["id_user"] = <?= $aEntry[0]["id_user"] ?>;
				entries[startId]["login"] = '<?= safeForJavascript($aEntry[0]["login"]) ?>';
				entries[startId]["date"] = '<?= $aEntry[0]["dateFormatted"] ?>';
				entries[startId]["tagString"] = '<?= safeForJavascript($aEntry[0]["tagString"]) ?>';
				entries[startId]["iconWidth"] = "<?= $aEntry[0]["iconWidth"] ?>";
				entries[startId]["iconHeight"] = "<?= $aEntry[0]["iconHeight"] ?>";
				entries[startId]["iconText"] = "<?= safeForJavascript($iconText) ?>";
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

			//]]>
	</script>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body onload="load()" onunload="GUnload()">
<?= getNavigationBar($_REQUEST, "meipi") ?>
<div id="meipi">
	<? getMeipiDescription() ?>
	<div id="leftColumn">
  		  <div id="map" style="margin-left:15px;width:95%;height:366px;border:1px solid #000000;">
				</div>
<div id="" class="" style="margin:0px 0px 0px 15px;padding:2px;width:95%;font-size:1.2em;line-height:1.2em;background-color:#AACDE0;"><?= getString("lastEntry") ?></div>

<?
				// CATEGORIES
				$categories = getCategories();
				for($iCategory=0; $iCategory<count($categories); $iCategory++)
				{
					$idCategory = $categories[$iCategory]["id_category"];
					$categoryName = $categories[$iCategory]["category_name"];
					$aParamsCategories = getEntriesParamsFromRequest($_REQUEST);
					$aParamsCategories["category"]="$idCategory";
					$aParamsCategories["limit"] = 1;
					$aParamsCategories["content"]="yes";
					$aParamsCategories["order by"]="date";
					$aParamsCategories["order desc"]="desc";
					unset($aParamsCategories["id_entry"]);
    			$aEntries[$iCategory] = getEntries($aParamsCategories);
				}
?>
  		  <div id="mCanales"> 
				<h2><a href="<?= setParams("categories.php", null) ?>"><?= getString("Categories") ?></a> <img src="<?= $commonFiles ?>images/feed.png" width="12" height="12" /></h2>
<?
					for($iCategory=0; $iCategory<count($categories); $iCategory++)
					{
?>
		  				<div id="mCanal<?= $categories[$iCategory]["id_category"] ?>">
						  	<div id="hcanal<?= $categories[$iCategory]["id_category"] ?>">
								<div class="tcanal">
									<a href="<?= setParams("list.php", Array("category" => $categories[$iCategory]["id_category"])) ?>"><?= $categories[$iCategory]["category_name"] ?></a>
									<a href="<?= setParams("rss.php", Array("category" => $categories[$iCategory]["id_category"])) ?>">
										<img src="<?= $commonFiles ?>images/feed.png"/>
									</a>
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
?>
							<div id="<?= $id_entry ?>" class="c<?= $categories[$iCategory]["id_category"] ?>_entrada <?= $cssClass ?>">
								<div class="c<?= $categories[$iCategory]["id_category"] ?>_hentrada">
										<a href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= dirEntry ?>','<?= $userId ?>','<?= $logged ?>');" title="<?= $title ?>"><?= getSubString($title,$charsInCategoriesTitle,$lineInCategoriesTitle) ?></a>
								</div>
								<div class="c<?= $categories[$iCategory]["id_category"] ?>_tentrada">
<?
								if(isset($content) && $type==0)
								{
?>
								<img src="<?= $dirSquare ?><?= $content ?>" alt="<?= $title ?>" /><br />
<?
								}
								else if(isset($content) && ($type==1 || $type==2))
								{
?>
									<img src="<?= $commonFiles ?>images/video.gif" alt="<?= $title ?>" />
<?
								}
								else if(strlen($content)>0 && $type==3)
								{
?>
									<img src="<?= $commonFiles ?>images/lively.gif" alt="<?= $title ?>" />
<?
								}
?>
									<?= getSubString(basicHtml(allowedHtml($text)),$charsInCategories,$lineInCategories) ?> <a href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= dirEntry ?>','<?= $userId ?>','<?= $logged ?>');" title="<?= $title ?>"><span class="extend">[<?= getString("Extend") ?>]</span></a>
								</div>
								<div class="c<?= $categories[$iCategory]["id_category"] ?>_pentrada">
									<?= getString("by") ?> <a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $user ?></a>
									<br/>
									<?= $date ?> | <?= getString("votes") ?>: <?= $votes ?>
								</div>
							</div>
<?
							}
							if(dbGetSelectedRows($aEntries[$iCategory])==0)
							{
?>
								<div id="<?= $id_entry ?>" class="c<?= $categories[$iCategory]["id_category"] ?>_entrada">
									<?= getString("Sorry, no entries found") ?>
								</div>
<?
					}
?>
						</div> <!-- end category <?= $iCategory ?> -->
<?
					}
?>

		  </div>
  		</div>
 		<div id="rightColumn"> 
		<div id="entradasMeipi">
<?
?>
		<h2><a href="<?= setParams("list.php", null) ?>"><?= getString("lastEntries") ?></a> <a href="<?= setParams("rss.php", null) ?>"><img src="<?= $commonFiles ?>images/feed.png" width="12" height="12" /></a></h2>
<?
		$iEntries = dbGetSelectedRows($aEntriesList);
		for($iEntry=0; $iEntry<$iEntries; $iEntry++)
		{
			$id_entry = $aEntriesList[$iEntry]["id_entry"];
			$lat = $aEntriesList[$iEntry]["latitude"];
			$lon = $aEntriesList[$iEntry]["longitude"];
			$title = $aEntriesList[$iEntry]["title"];
			$text = $aEntriesList[$iEntry]["text"];
			$date = $aEntriesList[$iEntry]["dateFormatted"];
			$id_user = $aEntriesList[$iEntry]["id_user"];
			$id_category = $aEntriesList[$iEntry]["id_category"];
			$category = getCategory($id_category);
			$login = $aEntriesList[$iEntry]["login"];
			$ranking = $aEntriesList[$iEntry]["ranking"];
			$votes = $aEntriesList[$iEntry]["votes"];
			$url = $aEntriesList[$iEntry]["url"];
			$comments = $aEntriesList[$iEntry]["comments"];
			$id_content = $aEntriesList[$iEntry]["id_content"];
			$content = $aEntriesList[$iEntry]["file"];
			$type = $aEntriesList[$iEntry]["type"];
			$cssClass = $aEntriesList[$iEntry]["css_class"];

			if(strlen($content)>0 && $type==0)
			{
				$content = $dirSquare.$content;
			}
			else if(strlen($content)>0 && ($type==1 || $type==2))
			{
				$content = $commonFiles."images/video.gif";
			}
			else if(strlen($content)>0 && $type==3)
			{
				$content = $commonFiles."images/lively.gif";
			}
			else
			{
				$content = $commonFiles."images/no-img.gif";
			}

			$aTags = getTags($id_entry);
?>
				<div class="entrada <?= $cssClass ?>" id="<?= $id_entry ?>">
					<div class="entrada-img"><img src="<?= $content ?>" width="80px" height="80px"></div><!-- entrada-img -->
					<div class="entrada-txt">
					<h1>
						<a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= getSubString($title,$charsInListTitle,$lineInListTitle) ?></a>
					</h1>
					<div class="entrada-data">
						<?= getString("by") ?><strong> <a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $login ?></a></strong> -- <?= $date ?>
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
						<?= getSubString(basicHtml(allowedHtml($text)), $charsInList, $lineInList) ?> <a title="<?= $title ?>" href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>, '<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><span class="extend">[<?= getString("Extend") ?>]</span></a>
					</div> 
					<!-- entrada-desc -->
					<div class="entrada-party">
					<li><a href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $id_entry ?>, '<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= getString("Comment") ?></a> [<?= $comments ?> <?= getString("comment".($comments!=1 ? "s" : "")) ?>]</li>
					<!--<li><img src="<?= $commonFiles ?>images/stars.png" /> [<?= $votes ?> <?= getString("votes") ?>]</li>-->
<?
				if(isValidLatLon($lat, $lon))
				{
?>
					<ul>
				<li><a href="<?= setParams("map.php", Array("id_entry" => $id_entry)) ?>"><?= getString("View in map") ?></a></li>
<?
				}
?>
<?

				if(strlen($id_content)>0 && $type=="0")
				{
?>
					<li><a class="amosac" title="<?= getString("Add to mosaic") ?>" href="javascript:addToMosaic('<?= $idMeipi ?>', '<?= $id_content ?>');"><img src="<?= $commonFiles ?>images/header-mosac-anadir.gif" /><?= getString("Add to mosaic") ?></a></li>
<?
				}

				/*if(strlen($url)>0)
				{
?>
					<li style="display: none;"><a href="<?= $url ?>" target="_blank">www</a></li>
<?
				}*/
?>
					<li><a href="<?= setParams("list.php", Array("open_entry" => $id_entry)) ?>"><?= getString("Permalink") ?></a></li>
					</ul>
			</div> <!-- end class entrada-party -->
 	
					<!-- entrada-party -->
			</div> <!-- entrada-box -->
		</div> <!-- entrada -->
<?
		}
		if($iEntries==0)
		{
?>
				<div class="entrada">
					<?= getString("Sorry, no entries found") ?>
				</div>
<?
		}
?>
	</div> <!-- entradas -->
	<div id="comments">
	<h2><a href="<?= setParams("rssAllComments.php", null) ?>"><?= getString("Last comments") ?></a> <a href="<?= setParams("rssAllComments.php", null) ?>"><img src="<?= $commonFiles ?>images/feed.png" width="12" height="12" /></a></h2>
<?
		$aComments = getComments("ALL", 4);
		$iComments = count($aComments);
		for($iComment=0; $iComment<$iComments; $iComment++)
  	{
    	$comment_id_entry = $aComments[$iComment]["id_entry"];
    	$comment_id_comment = $aComments[$iComment]["id_comment"];
    	$comment_subject = $aComments[$iComment]["subject"];
    	$comment_text = $aComments[$iComment]["text"];
    	$comment_id_user = $aComments[$iComment]["id_user"];
    	$comment_login = $aComments[$iComment]["login"];
    	$comment_date = $aComments[$iComment]["dateFormatted"];
?>
		<div class="comment">
		<h3><a href="javascript:showEntryWindow('<?= $idMeipi ?>', <?= $comment_id_entry ?>,'<?= $dirEntry ?>','<?= $userId ?>','<?= $logged ?>');"><?= $comment_subject ?></a></h3>
		<p><?= basicHtml(allowedHtml($comment_text)) ?><p/>
		<div class="entrada-party">
		<?= $comment_date ?>
		-
			<a href="<?= setParams("list.php", Array("id_user" => $comment_id_user)) ?>"><?= $comment_login ?></a>
		</div>
		
		</div>
<?
		}
		if($iComments==0)
		{
?>
				<div class="entrada">
					<?= getString("Sorry, no comments found") ?>
				</div>
<?
		}
?>
		</div> <!-- comments -->

</div><!-- right column -->
 </div> <!-- meipi.php -->


	<? getOverEntry("meipi"); ?>

	<?= getFooter("meipi") ?>
	<? getEntryWindow($_REQUEST); ?>
	<? getLoginForm($_REQUEST); ?>
	<? getNewEntryForm(); ?>
	<? getMessageWindow(); ?>
	<?= getStatisticsScript() ?>
</body>
</html>

