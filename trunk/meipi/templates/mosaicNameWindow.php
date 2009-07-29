<? global $aMosaicData ?>
<div id="mosaicNameWindow" style="display: none;">
<div class="logh">
				<a href="javascript:cancelSaveMosaic()">
					<img src="<?= $commonFiles ?>images/cancel.gif" title="Cerrar" alt="Cerrar" /></a><?= getString("saveMosaic") ?>&nbsp;<?= getString("Mosaic") ?>
</div><!-- strin: xxx cerrar-->
<div id="mosaicConfirm">
		<form name="mosaicNameForm" action="<?= setParams("mosaic.php", null) ?>" onSubmit="return false;" method="post">
	<?= getString("Title") ?>
			<input type="text" name="mosaicName" value="<?= safeForJavascript($aMosaicData["mosaicDesc"]) ?>" />
			<input type="button" value="<?= getString("Confirm") ?>" onClick="javascript:confirmSaveMosaic();" />
			<input type="button" value="<?= getString("Cancel") ?>" onClick="javascript:cancelSaveMosaic();" />
		</form>
</div>
</div>
