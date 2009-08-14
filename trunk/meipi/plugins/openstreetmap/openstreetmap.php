<?
	function openstreetmap_getMapConfig($params)
	{
?>
					var copyOSM = new GCopyrightCollection("<a href=\"http://www.openstreetmap.org/\">OpenStreetMap</a>");
					copyOSM.addCopyright(new GCopyright(1, new GLatLngBounds(new GLatLng(-90,-180), new GLatLng(90,180)), 0, " "));

					var tilesMapnik = new GTileLayer(copyOSM, 1, 17, {tileUrlTemplate: 'http://tile.openstreetmap.org/{Z}/{X}/{Y}.png'});
					var tilesOsmarender = new GTileLayer(copyOSM, 1, 17, {tileUrlTemplate: 'http://tah.openstreetmap.org/Tiles/tile/{Z}/{X}/{Y}.png'});

					var mapMapnik = new GMapType([tilesMapnik], G_NORMAL_MAP.getProjection(), "Mapnik");
					var mapOsmarender = new GMapType([tilesOsmarender], G_NORMAL_MAP.getProjection(), "Osmarend");
					<?= $params["options"] ?>.mapTypes = [mapMapnik, mapOsmarender];
<?
	}
	addPlugin("getMapConfig", "openstreetmap_getMapConfig");
?>
