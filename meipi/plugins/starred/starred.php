<?
	function starred_getEntries($aEntries)
	{
		foreach($aEntries as $id => $aEntry)
		{
			if($aEntry["extra"]["extra_starred"]=="yes")
			{
				$aEntries[$id]["css_class"] = "starred";
				$aEntries[$id]["iconImage"] = $commonFiles."plugins/starred/images/star_".$aEntry["id_category"].".png";
				$aEntries[$id]["iconImageStand"] = $commonFiles."plugins/starred/images/star_".$aEntry["id_category"]."_out.png";
				$aEntries[$id]["iconWidth"] = 25;
				$aEntries[$id]["iconHeight"] = 32;
			}
		}
		return $aEntries;
	}

	addPlugin("getEntries", "starred_getEntries");

/* TODO:

	- in BD:
		alter table meipi_extra add column extra_starred varchar(30) NOT NULL;
		insert into meipi_params values('meipi', 'starred', 'Destacado', 'select', 'no,yes', '');

	- in config file:
		add "starred" to plugins list
*/
?>
