<?
//  require_once($configsPath."functions/common.php");
	$aCategories = getCategories();
	global $idMeipi;
?>
	<script type="text/javascript">
		//<![CDATA[
			var mapOptions = {};
			<? executePlugins("getMapConfig", Array("options" => "mapOptions")); ?>
		//]]>
	</script>
		
<div id="newEntry" style="display: none;">
	<form name="newEntryForm" method="post" action="actions/newEntry.php" enctype="multipart/form-data">
		<input type="hidden" name="id_meipi" value="<?= $idMeipi ?>" />
		<div class="alertWindow">
		  <div id="windowHeight">
				<div class="logh">
					<a href="javascript: cancel()"><img src="<?= $commonFiles ?>images/cancel.gif" /></a><?= getString("New entry") ?>
				</div>
			<div id="newEntryText">
<div class="fo-tabla">
						<div class="fo-fila">
							<div class="fo-type">
								 * <?= getString("Title") ?>
							</div>
							<div class="fo-input">
					  		<input class="input" type="text" name="title" />
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
								* <?= getString("Description") ?>
							</div>
							<div class="fo-input">
								<textarea class="input-medium" name="description" style="height:100px; width:400px;" wrap="virtual"></textarea>
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
								<?= getString("Web") ?>
							</div>
							<div class="fo-input">
								<input class="input" type="text" name="url" value="http://" />
							</div>							
							<div class="fo-desc"><?= getString("http://www.yourweb.com") ?>
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type"><?= getString("TypeArchive") ?>
							</div>
							<div class="fo-input" id="filetype_div">
								<select class="input" name="filetype" onChange="javascript:showFileType(this.options[selectedIndex].value);">
									<option value="photo"><?= getString("Image") ?></option>
									<option value="without" selected><?= getString("WithoutImage") ?></option>
									<option value="video"><?= getString("Video") ?></option>
									<?
										global $livelyTypeEnabled;
										if("true"==$livelyTypeEnabled)
										{
									?>
									<option value="lively"><?= getString("Lively") ?></option>
									<?
										}
									?>
								</select>
							</div>
							<div class="fo-input" style="display:none;" id="keep_file_div">
								<?= getString("Keep content") ?>
							</div>
						</div>

						<div id="file_photo" style="display:none">
							<div class="fo-fila">
								<div class="fo-type">
									<?= getString("Image") ?>
								</div>
								<div class="fo-input">
									<input class="input" type="file" name="uploaded" />
								</div>
								<div class="fo-desc"><div style="padding-left:60px;"><?= getString("max2Mb") ?></div>
							</div><!-- string: Max file size 2Mb. -->

							</div>
						</div>

						<div id="file_without">
							<!--<div class="fo-fila">
								<div class="fo-type">
								</div>
								<div class="fo-input">
								</div>
							</div>-->	
						</div>

						<div id="file_video" style="display:none">
							<div class="fo-fila">
								<div class="fo-type">
									<?= getString("Video ID") ?>
								</div>
								<div class="fo-input">
									<select class="input" name="videotype" >
									<option value=""></option>
									<option value="youtube">Youtube</option>
									<option value="googlevideo">Google Video</option>
									<option value="vimeo">Vimeo</option>
                                    <option value="archiveaudio">Archive.org Audio</option>
                                    <option value="bliptv">Blip.tv</option>
									</select>
									<input class="input" type="text" name="video" />
								</div>
								<div class="fo-desc"><?= getString("Select video...") ?>: <i><nobr>http://www.youtube.com/watch?v=SuBgGqGDPVE</nobr></i>
							</div>

							</div>
						</div>

						<div id="file_lively" style="display:none">
							<div class="fo-fila">
								<div class="fo-type">
									<?= getString("Lively ID") ?>
								</div>
								<div class="fo-input">
									<input class="input" type="text" name="lively" />
								</div>
								<div class="fo-desc"><?= getString("Select lively...") ?>: <i><nobr>http://www.lively.com/dr?rid=3399687792768660690</nobr></i>
							</div>

							</div>
						</div>

						<div class="fo-fila">
							<div class="fo-type">
								* <?= getString("Category") ?>
							</div>
							<div class="fo-input">
								<select class="input" name="category" onChange="javascript:showCategoryDesc(<?= count($aCategories) ?>,this.options[selectedIndex].value);">
								<option value=""></option>
					<?
						for($iCategory=0; $iCategory<count($aCategories); $iCategory++)
						{
							$idCategory = $aCategories[$iCategory]["id_category"];
							$categoryName = $aCategories[$iCategory]["category_name"];
							echo "<option value=\"$idCategory\">$categoryName</option>\n";
						}
					?>
								</select>
							</div>
							<? global $aCategoriesDesc; ?>
							<div class="fo-desc">
								<div id="categoryDesc_0"><?= getString("Select category...") ?></div>
					<?
						for($iCategory=1; $iCategory<=count($aCategories); $iCategory++)
						{
							$categoryDesc = $aCategoriesDesc[$iCategory]["longDesc"];
							echo "<div id=\"categoryDesc_".$iCategory."\" style=\"display:none\">".$categoryDesc."</div>\n";
						}
					?>
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">
								<?= getString("Address") ?>
							</div>
							<div class="fo-input">
								<input class="input" type="text" name="address" /><input class="boton-p" type="button" onClick="goTo()" value="<?= getString("Go") ?>"/>
							</div>
							<div class="fo-desc"><?= getString("Example Elm Street 12, Buenos Aires. Argentina") ?>
							</div><!-- string: Ejemplo de direccion -->
						</div>
						<div class="fo-fila">
							<div class="fo-type">
								<input type="checkbox" name="no_location" onclick="if(this.checked) removeNewEntryMarker();" />
							</div>
							<div class="fo-input">
								 <?= getString("Check this to post without location") ?>
				  				 <input type="hidden" name="latitude" readonly />
									<input type="hidden" name="longitude" readonly />
									<input type="hidden" name="edition" value="no" readonly />
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type"><?= getString("Tags") ?>
							</div>
							<div class="fo-input">
									<input class="input" type="text" name="tags" rows="2" style="height:60px;" wrap="virtual" cols="45"/>
							</div>
							<div class="fo-desc"><?= getString("tag explain") ?>
							</div>
						</div>
						<div class="fo-fila">
							<div class="fo-type">&nbsp;
							</div>
							<div class="fo-input">
					<!-- Tag Cloud Start -->
					<p id="tagCloud">
					<?
						$aTags = getTagsCloud();
						for($iTag=0; $iTag<count($aTags); $iTag++)
						{
					?>
					<a href="javascript:switchTag('<?= $aTags[$iTag]["tag_name"]?>')" class="tag_cloud_<?= $aTags[$iTag]["class"] ?>"><?= $aTags[$iTag]["tag_name"] ?></a>
					<?
						}
					?>				</p>
					<!-- Tag Cloud End -->
							</div>
						</div>
<?
	global $extraTable;
	global $aExtraConfig;
	if($extraTable=="true")
	{
		for($iExtraParam=0; $iExtraParam<count($aExtraConfig); $iExtraParam++)
		{
			$paramType = $aExtraConfig[$iExtraParam]['type'];
			$paramName = $aExtraConfig[$iExtraParam]['name'];
			$paramDescription = $aExtraConfig[$iExtraParam]['description'];
			$paramId = "extra_".$aExtraConfig[$iExtraParam]['name'];
			$paramValue = $aExtraConfig[$iExtraParam]['value'];
			parse_str($aExtraConfig[$iExtraParam]['config'], $aParamConfig);

			if($aParamConfig["needsEditor"] == "true")
			{
				// Check if user is EDITOR
				if(!isEditor($idMeipi))
				{
					if(!canEditMeipimatic($idMeipi))
					{
						continue;
					}
				}
			}

			if($paramType == "text")
			{
?>
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
								<input class="input" type="text" name="<?= $paramId ?>" />
							</div>
						</div>
<?
			}
			elseif($paramType=="select")
			{
?>
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
								<select class="input" name="<?= $paramId ?>">
								<option value=""></option>
					<?
						$aSelect = explode(',',$paramValue);
						for($iSelect=0; $iSelect<count($aSelect); $iSelect++)
						{
							$select = $aSelect[$iSelect];
							echo "<option value=\"$select\"$selected>$select</option>\n";
						}
					?>
								</select>
							</div>
						</div>
<?
			}
			else if($paramType=="special")
			{
				switch($paramValue)
				{
					case "cities":
?>
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
								<select class="input" name="<?= $paramId ?>">
								<option value=""></option>
					<?
						global $aCities;
						for($i=0; $i<count($aCities); $i++)
						{
							$aCity = $aCities[$i];
							$cityValue = safeForJavascript($aCity["city"]);
							$cityTitle = safeForJavascript($aCity["title"]);
							if(strlen($cityTitle)==0)
							{
								$cityTitle = $cityValue;
							}
							$cityAddress = safeForJavascript($aCity["address"]);
							echo "<option value=\"$cityValue\"$selected>$cityTitle</option>\n";
						}
					?>
								</select>
							</div>
						</div>
<?
						break;
				}
			}
		}
	}
?>
						<div class="fo-fila">
							<div class="fo-type">&nbsp;
							</div>
							<div class="fo-input"><input class="boton" type="button" onClick="javascript: submitNewEntry(<?= doubleValueOrNull($minLat) ?>,<?= doubleValueOrNull($maxLat) ?>,<?= doubleValueOrNull($minLon) ?>,<?= doubleValueOrNull($maxLon) ?>)" value="<?= getString("Submit") ?>" />
					<input class="boton" type="button" onClick="javascript: cancel()" value="<?= getString("Cancel") ?>" />
				
							</div>
						</div>

</div><!-- cierre tabla -->
									</div>
			</div>
		</div>
	</form>
<? // <div id="newEntryMap" style="display: none;"></div> ?>
<div id="newEntryMap" style="width:200; height: 100;"></div>
</div>
