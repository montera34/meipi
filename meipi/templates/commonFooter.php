<div id="epi">
	<ul id="epil">
	<li><a title="Meipi" href="/">meipi.org</a>&nbsp;&nbsp;</li>
	<li><a title="<?= getString("About meipi") ?>" href="/about.php"><?= getString("About meipi") ?></a>&nbsp;&nbsp;</li>
	<li><a href="meipimatic.php" title="<?= getString("create a meipi") ?>"><?= getString("create a meipi") ?></a>&nbsp;&nbsp;</li>

<li><a title="Blog" href="http://meipi.org/blog/index.php"><?= getString("Weblog") ?></a>&nbsp;&nbsp;</li>
	<li><a title="FAQ" href="/faq.php"><?= getString("FAQ") ?></a>&nbsp;&nbsp;</li>
	<li><a title="Contact us" href="/contact-us.php"><?= getString("Contact us") ?></a>&nbsp;&nbsp;</li>
	<li><a title="Privacy" href="/legal.php"><?= getString("legal advice") ?></a></li>
	</ul>

	<ul id="epir">
		<li>	<?= getString("Choose language") ?> <select name="language" onChange="selectLanguage(this)" style="width:80px;font-size:1em;" >
<? 
	global $lang, $meipiLangs, $meipiLanguages;
	for ($j=0; $j<count($meipiLangs); $j++)
	{
		echo "<option value=\"".$meipiLangs[$j]."\"".($lang==$meipiLangs[$j] ? " selected" : "").">".$meipiLanguages[$j]."</option>\n";
	}
?>
		</select></li>
	</ul>
</div><!-- end id epi -->
<?= getStatisticsScript() ?>
