	<div id="meipiDesc">
		<? /* div id="paginaTitle"><span class="webtitle"><?= (($longDesc=="true") ? "<a href=\"javascript:toggleLongDesc();\">".$webTitle."</a>" : $webTitle) ?></span></div */ ?>
		<div id="paginaDesc">
			<?= allowedHtml($webDescription) ?>
			<?= (($longDesc=="true") ? "<a href=\"javascript:showLongDesc();\">[".getString("read more")."]</a>" : "") ?>
		</div>
		<div id="paginaLongDesc" style="display:none">
			<?= allowedHtml($longDescription) ?>
			<?= (($longDesc=="true") ? "<a href=\"javascript:hideLongDesc();\">[".getString("hide desc")."]</a>" : "") ?>
		</div>
	</div>
