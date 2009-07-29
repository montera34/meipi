  <div id="overEntry" class="entryFloat" style="display: none;">
		<div class="logh">
		<a href="javascript: closeEntry()"><img src="<?= $commonFiles ?>images/cancel.gif" title="Cerrar" alt="Cerrar" /></a><div id="entryTitle"></div><!-- string: xxx cerrar-->
		</div>
<?
		if($hasOverEntry)
		{
?>
		
		<div id="entryContent"></div>
		<div id="entryDescription"></div>
		<div id="entryLatitude"></div>
		<div id="entryLongitude"></div>
<?
		}
?>
		<div id="helpInfo">
			<div id="helpTitle"><?= getString("Help") ?>:</div>
			<div id="helpContent"><?= $helpString ?></div>
		</div>
</div>

