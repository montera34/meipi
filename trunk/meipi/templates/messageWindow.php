<?
	global $commonFiles, $_REQUEST;
	$msg = getString($_REQUEST["msg"]);
	$msg = allowedHtml($msg);
	$bMsg = strlen($msg)>0;
?>
		<div id="messageWindow" style="<?= ($bMsg ? "" : "display:none;") ?>z-index:1000;"><!-- style="position: absolute; left: 50%; top: 50%; border: 5px solid blue; display: none; z-index: 1000; background-color: #CCC;"-->	<div class="logh">
				<a href="javascript:hideMessage()">
					<img src="<?= $commonFiles ?>images/cancel.gif" title="<?= getString("Close") ?>" alt="<?= getString("Close") ?>" /></a>&nbsp;
		</div><!-- strin: xxx cerrar-->
		<div id="messageText">
			<?= $msg ?>
			</div>
		</div>
