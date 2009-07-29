<?
	$configsPath = "../";
	require_once "../functions/meipi.php";
	global $dirThumbnail;

	$webTitle = removeAcutes($webTitle);

	echo '<?xml version="1.0" encoding="iso-8859-1" ?>';
?><Module>
<ModulePrefs title="<?= $webTitle ?>" title_url="<?= $mainUrl.setParams("meipi.php", null) ?>" description="__MSG_description__ <?= $webTitle ?>" author="Lamb Brothers" author_email="meipiMapplet@lamboratory.com" author_affiliation="Lamboratory.com" author_location="Madrid, Spain"  author_aboutme="Jorge and Guillermo are the Lamb Brothers." author_link="http://www.lamboratory.com" screenshot="<?= str_replace("&", "&amp;", $mainUrl.setParams("screenshot.php?cat=1&width=300&height=100", null)) ?>" thumbnail="<?= str_replace("&", "&amp;", $mainUrl.setParams("screenshot.php?cat=1&width=120&height=60", null)) ?>">
	<Require feature="sharedmap" />
	<Locale messages="http://<?= $_SERVER["HTTP_HOST"] ?>/mapplet/messages/ALL_ALL.xml"/>
	<Locale lang="es" messages="http://<?= $_SERVER["HTTP_HOST"] ?>/mapplet/messages/es_ALL.xml"/>
</ModulePrefs>
<Content type="html"><![CDATA[
	
	<div style="padding-top:0.7em; padding-bottom:0.7em">__MSG_explanation1__</div>
	<div style="padding-bottom:0.7em"><span style="font-size:small;">__MSG_explanation2__</span> <?= $webDescription ?></div>
	<div style="padding-bottom:0.7em">
		<a href="<?= $mainUrl.setParams("meipi.php", null) ?>" target="_blank">__MSG_gotomeipi__</a><br>
		<a href="http://www.meipi.org/" target="_blank">__MSG_meipiurl__</a>
	</div>
	<div>__MSG_searchmeipis__
		<form method="get" action="">
			<input type="text" id="meipisearch" /><input type="button" value="__MSG_search__" onClick="javascript:searchMeipis();" />
		</form>
	</div>
	<div id="searchResults"></div>

	<script type="text/javascript">
		var map = new GMap2();
		var geoXml = new GGeoXml("<?= $mainUrl.setParams("kml.php", null) ?>");
		map.addOverlay(geoXml);

<?	// Center mapplet on last entry
		$aLastEntry = getEntries(Array("order by" => "date", "order desc" => TRUE, "limit" => 1));
		if(dbGetSelectedRows($aLastEntry)>0)
		{
			$lat = $aLastEntry[0]["latitude"];
			$lon = $aLastEntry[0]["longitude"];
			if(strlen($lat)>0 && strlen($lon)>0)
			{
?>
		map.setCenter(new GLatLng(<?= $lat ?>, <?= $lon ?>));
<?
			}
		}
?>

		function searchMeipis()
		{
			html = "<table cellpadding=\"5\">";
			string = _gel('meipisearch').value;
			_IG_FetchXmlContent("http://<?= $_SERVER["HTTP_HOST"] ?>/mapplet/mappletSearch.php?query="+string, function(response) {
				var meipis = response.getElementsByTagName("meipi");
				for (var i = 0; i < meipis.length; i++) {
			    var meipi = meipis.item(i);
			    var id  = meipi.getAttribute("id");
					var title = meipi.getElementsByTagName("title")[0].firstChild.nodeValue;
					var desc = meipi.getElementsByTagName("description")[0].firstChild.nodeValue;
					var meipiUrl = meipi.getElementsByTagName("meipiUrl")[0].firstChild.nodeValue;
					var mappletUrl = meipi.getElementsByTagName("mappletUrl")[0].firstChild.nodeValue;
					html += "<tr><td><b><a href=\""+meipiUrl+"\" target=\"_blank\">"+title+"</a></b><br/><span style=\"font-size:small;\">"+desc+"<br/><a href=\""+meipiUrl+"\" target=\"_blank\">[__MSG_visit__]</a> <a href=\"http://maps.google.com/ig/add?synd=mpl&pid=mpl&moduleurl="+mappletUrl+"\" target=\"_blank\">[__MSG_add__]</a></span></td></tr>";
				}
				html += "</table>";
				var message = document.getElementById("searchResults");
				message.innerHTML = html;
			});
		}
	</script>

]]></Content>
</Module>

