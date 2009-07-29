<?
	global $commonFiles, $extraStyles;
?>
	<link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/meipi.css" />

<?
	if(strlen($extraStyles)>0)
	{
		$aStyles = split(",", $extraStyles);
		foreach($aStyles as $style)
		{
?>		<link rel="stylesheet" type="text/css" href="<?= $commonFiles ?>styles/style_<?= trim($style) ?>.css" />
<?
		}
	}
?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="<?= $commonFiles ?>js/tiny_mce/tiny_mce.js"></script>
