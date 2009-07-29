<?
	global $lang;
?>
<?		if($type=="map")
		{
// it's map.php:
?>
	<div id="mappie">
		<div id="trasmappie">
<?
		} else
		{
// otherwise:
?>
	<div id="pie">
		<div id="traspie">
<?	}
?>
			<ul class="pielist">
				<li><a title="Meipi" href="<?= $meipiUrl ?>">meipi.org</a> </li>
				<li><a title="About" href="<?= $aboutUrl ?>"><?= getString("About meipi") ?></a> </li>
				<li><a href="<?= $blogUrl ?>"><?= getString("Weblog") ?></a> </li>
				<li><a href="<?= $legalUrl ?>"><?= getString("Legal advice") ?></a>  </li>
				<li><a href="<?= $faqUrl ?>"><?= getString("FAQ") ?></a> </li>
				<li><a href="<?= $licenseUrl ?>"><?= getString("Content License") ?></a> </li>
				<li><?= getString("Choose language") ?> <select name="language" onChange="selectLanguage(this)" style="width:80px;font-size:1em;" >
<? 
	global $lang, $meipiLangs, $meipiLanguages;
	for ($j=0; $j<count($meipiLangs); $j++)
	{
		echo "<option value=\"".$meipiLangs[$j]."\"".($lang==$meipiLangs[$j] ? " selected" : "").">".$meipiLanguages[$j]."</option>\n";
	}
?>
				</select></li>
			</ul>
		</div>
	</div>
