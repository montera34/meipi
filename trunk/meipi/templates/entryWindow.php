<?
//	global $configsPath;
//  require_once($configsPath."functions/common.php");

		global $idMeipi;
		global $dirEntry;
		global $commonFiles;
		global $dirProfileSquare;

		$bLoaded = FALSE;

		$openEntry = $request["open_entry"];
		if(strlen($openEntry)>0)
		{
			$openEntry = intval($openEntry);

			$aParams["id_entry"]=$openEntry;
			$aParams["content"]="yes";
			$aEntries = getEntries($aParams);
			if(dbGetSelectedRows($aEntries)>0)
			{
				$id_entry = $aEntries[0]["id_entry"];
				$lat = $aEntries[0]["latitude"];
				$lon = $aEntries[0]["longitude"];
				$title = $aEntries[0]["title"];
				$text = $aEntries[0]["text"];
				$date = $aEntries[0]["dateFormatted"];
				$id_user = $aEntries[0]["id_user"];
				$image = $aEntries[0]["image"];
				$id_category = $aEntries[0]["id_category"];
				$category = getCategory($id_category);
				$login = $aEntries[0]["login"];
				$content = $aEntries[0]["file"];
				$id_content = $aEntries[0]["id_content"];
				$type = $aEntries[0]["type"];
				$url = $aEntries[0]["url"];
				$extra = $aEntries[0]["extra"];
				$ranking = $aEntries[0]["ranking"];
				$votes = $aEntries[0]["votes"];

				$cssClass = $aEntries[0]["css_class"];

				if(canEditEntry($id_entry))
				{
					$canEdit = "true";
				}
				$edited = $aEntries[0]["edited"];
				$last_edited = $aEntries[0]["dateLastEditedFormatted"];
				$last_editor = $aEntries[0]["last_editor"];
				$bLoaded = TRUE;

				$aTags = getTags($id_entry);
				$sTags = "";
				$sTagLinks = "";
				if(count($aTags)>0)
				{
					for($iTag=0; $iTag<count($aTags); $iTag++)
					{
						$id_tag = $aTags[$iTag]["id_tag"];
						$tag_name = $aTags[$iTag]["tag_name"];
						if($sTags=="")
							$sTags .= $tag_name;
						else
							$sTags .= " ".$tag_name;
						$sTagLinks .= " <a href=\"".setParams("list.php", Array("id_tag" => $id_tag))."\">".$tag_name."</a>";
					}
				}

				$viewer = getIdUser();
				$aVoted = dbSelect("SELECT vote FROM ".VOTE." WHERE id_user='$viewer' AND id_entry='$id_entry' LIMIT 1", $dbLink);
				if(count($aVoted)>0)
				{
					$iVote = $aVoted[0]["vote"];
					$voted = "true";
				}
				else
				{
					$iVote = 0;
					$voted = "false";
				}
?>
<div id="entryWindow" class="<?= $cssClass ?>">
	<div class="logh">
		<!--<input type="button" onClick="javascript: cancelEntryWindow()" value="X" />-->
		<a href="javascript: cancelEntryWindow()"><img src="<?= $commonFiles ?>images/cancel.gif" /></a><?= $title ?>
	</div><!-- end logh-->
	<div id="entrada-data-group">
<?
		if(strlen($image)>0)
		{
			$userProfilePicture = $commonFiles.$dirProfileSquare.$image;
		}
		else
		{
			$userProfilePicture = $commonFiles."images/default.jpg";
		}
?>
		<span class="entrada-profile"><a href="<?= getProfilePage($id_user, $login) ?>" title="<?= getString("View user page") ?>" alt="<?= getString("View user page") ?>"><img src="<?= $userProfilePicture ?>" /></a></span>	
	<div class="entrada-data">
				<?= getString("by") ?> <strong><a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $login ?></a></strong>
				-- <?= $date ?>
				<ul>
				<li><?= getString("category") ?>: <a href="<?= setParams("list.php", Array("category" => $id_category)) ?>"><?= $category ?></a></li>
<?
				if(strlen($url)>0)
				{
?>
					<li><?= getString("Web") ?>: <a href="<?= $url ?>" target="_blank"><?= $url ?></a></li>
<?
				}
?>
				</ul>
			</div> <!-- entrada-data -->
			<div class="entrada-party">
				<ul>
<?
				if($edited!="0")
				{
	    		$lastEditedMsg = getString("Last edited at")." ".$last_edited." ".getString("by")." ".getUser($last_editor);
?>
					<li><span class="entryDate"><?= $lastEditedMsg ?></span></li>
<?
				}
?>
				</ul>
			</div> <!-- entrada-data -->
			<div class="entrada-party">
				<ul>
				<li id="rankPlace"><?= $votes ?> <?= getString("votes") ?>: <?
					$ranking1to5 = (round($ranking/2.5)/2)+3;
					for($rank = 1; $rank<=5; $rank++)
					{
						if($ranking1to5>=$rank)
						{
							?><img src="<?= $commonFiles ?>images/star_on.png" /><?
						}
						else if($ranking1to5>=$rank-0.5)
						{
							?><img src="<?= $commonFiles ?>images/star_half.png" /><?
						}
						else
						{
							?><img src="<?= $commonFiles ?>images/star_off.png" /><?
						}
					}
				?></li>
<?
	if(isLogged())
	{
		if($voted=="true")
		{
?>
				<li id="votePlace"><?= getString("Your vote") ?>: <?
					$ranking1to5 = (round($iVote/2.5)/2)+3;
					for($rank = 1; $rank<=5; $rank++)
					{
						?><a onclick='vote("<?= $idMeipi ?>", "<?= $id_entry ?>", <?= (5*($rank-3)) ?>)'><?
						if($ranking1to5>=$rank)
						{
							?><img src="<?= $commonFiles ?>images/star_on.png" /><?
						}
						else if($ranking1to5>=$rank-0.5)
						{
							?><img src="<?= $commonFiles ?>images/star_half.png" /><?
						}
						else
						{
							?><img src="<?= $commonFiles ?>images/star_off.png" /><?
						}
						?></a><?
					}
				?></li>
<?
		} else {
?>
				<li id="votePlace"><?= getString("Vote") ?>: <?
					$ranking1to5 = (round($iVote/2.5)/2)+3;
					for($rank = 1; $rank<=5; $rank++)
					{
						?><a onclick='vote("<?= $idMeipi ?>", "<?= $id_entry ?>", <?= (5*($rank-3)) ?>)'><img src="<?= $commonFiles ?>images/star_off.png" /></a><?
					}
				?></li>
<?
		}
	} else {
?>
			<li><a href='javascript:showLoginFormParams("open_entry=<?= $id_entry ?>")'><?= getString("Log in to vote") ?></a></li>
<?
	}
				if(isValidLatLon($lat, $lon))
				{
?>
				<li><a href="<?= setParams("map.php", Array("id_entry" => $id_entry)) ?>"><?= getString("View in map") ?></a></li>
<?
				}

				if(strlen($id_content)>0 && $type=="0")
				{
?>
					<li><a class="amosac" title="<?= getString("Add to mosaic") ?>" href="javascript:addToMosaic('<?= $idMeipi ?>', '<?= $id_content ?>');"><img src="<?= $commonFiles ?>images/header-mosac-anadir.gif" /><?= getString("Add to mosaic") ?></a></li>
<?
				}
?>
					<li><a href="<?= setParams("meipi.php", Array("open_entry" => $id_entry)) ?>"><?= getString("Permalink") ?></a></li>
				</ul>
<?  if ((isLogged() && ($viewer == $id_user)) || ($canEdit=="true"))
  {
?>
				<ul>
					<li><a href="javascript:showDeleteConfirmation('<?= $id_entry ?>','<?= $idMeipi ?>');"><?= getString("Delete entry") ?></a></li>
					<li><a href="javascript:showNewEntryForm('<?= $id_entry ?>','<?= escapeQuotes($title) ?>','<?= escapeQuotes($text) ?>','<?= $url ?>','<?= $id_category ?>','<?= escapeQuotes($address) ?>','<?= $lon ?>','<?= $lat ?>','<?= escapeQuotes($sTags) ?>', extraParams, '<?= $type ?>', '<?= escapeQuotes($content) ?>');"><?= getString("Edit entry") ?></a></li>
<?
		global $archiveParam;
		if(strlen($archiveParam)>0)
		{
			// User can edit entry and there is an archive-type param
			//echo "status: ".$extra["extra_".$archiveParam."_status"];
			$newStatus = ($extra["extra_".$archiveParam."_status"] == "archived" ? "active" : "archived");
?>
					<li><a href="<?= $commonFiles ?>actions/archive.php?id_meipi=<?= $idMeipi ?>&id_entry=<?= $id_entry ?>&status=<?= $newStatus ?>"><?= getString("Change to ".$newStatus) ?></a></li>
<?
		}
?>
				</ul>
<?
	}
?>
			</div> <!-- end class entrada-party -->
			<div class="entrada-party">
				<ul>
				<li><?
				if(count($aTags)>0)
				{
					echo getString("tags").":";
					echo $sTagLinks;
				}
?>
				</li>
				</ul>
			</div> <!-- end class entrada-party -->
			</div> <!-- end #entrada-data-group -->

  <div id="entryLong">
  	<div class="entry" id="<?= $openEntry ?>">
<? if(isset($content))
{
	switch($type)
	{
		default:
		case 0:
?>
		<p><img src="<?= $dirEntry.$content ?>" alt="<?= $title ?>" align="center"/></p>
<?
			break;
		case 1:
?>
			<object height="350" width="425"><param name="movie" value="http://www.youtube.com/v/<?= $content ?>"><param name="wmode" value="transparent"><embed src="http://www.youtube.com/v/<?= $content ?>&autoplay=1" type="application/x-shockwave-flash" wmode="transparent" height="350" width="425"></object><br/>
<?
			break;
		case 2:
?>
			<embed FlashVars="autoPlay=true" style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=<?= $content ?>" wmode="transparent">
			</embed>
<?
			break;

		case 3:
?>
			<iframe src='http://embed.lively.com/iframe?rid=<?= $content ?>' width='460' height='400' marginwidth='0' marginheight='0' frameborder='0' scrolling='no'></iframe>
<?
			break;
	}
} ?>
 			<p><span class="entryText"><?= allowedHtml($text, TRUE, FALSE) ?></span></p>
		</div>
	</div>

<? /*****/ ?>
<?
	global $extraTable;
	global $aExtraConfig;
	if($extraTable=="true")
	{
?>
		<script type="text/javascript">
			extraParams = new Array();
		</script>
<?
		for($iExtraParam=0; $iExtraParam<count($aExtraConfig); $iExtraParam++)
		{
			$paramType = $aExtraConfig[$iExtraParam]['type'];
			$paramName = $aExtraConfig[$iExtraParam]['name'];
			$paramDescription = $aExtraConfig[$iExtraParam]['description'];
			$paramId = "extra_".$aExtraConfig[$iExtraParam]['name'];
			$paramDefinitionValue = $aExtraConfig[$iExtraParam]['value'];
			$paramValue = $extra[$paramId];
			parse_str($aExtraConfig[$iExtraParam]['config'], $aParamConfig);
			if($aParamConfig["needsEditor"] == "true")
			{
				// TODO: Create function to check this
				// If user has EDITOR permissions for the meipi
				if(!$_SESSION["permission"][getIdUser()][$_SESSION["id_meipi"][$idMeipi]][2])
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
						<script type="text/javascript">
							aParam = new Array();
							aParam.push("<?= $paramId ?>");
							aParam.push("<?= $paramValue ?>");
							extraParams.push(aParam);
						</script>
					
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
								<?= $paramValue ?>
							</div>
						</div>
<?
			}
			elseif($paramType=="select")
			{
?>
						<script type="text/javascript">
							aParam = new Array();
							aParam.push("<?= $paramId ?>");
							aParam.push("<?= $paramValue ?>");
							extraParams.push(aParam);
						</script>
					
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
								<?= $paramValue ?>
							</div>
						</div>
<?
			}
			else if($paramType=="special")
			{
				switch($paramDefinitionValue)
				{
					case "cities":
?>
						<script type="text/javascript">
							aParam = new Array();
							aParam.push("<?= $paramId ?>");
							aParam.push("<?= $paramValue ?>");
							extraParams.push(aParam);
						</script>
					
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
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
							if($cityValue == $paramValue)
							{
								echo $cityTitle;
								break;
							}
						}
					?>
							</div>
						</div>
<?
						break;

					case "archive":
?>
						<script type="text/javascript">
							aParam = new Array();
							aParam.push("<?= $paramId ?>");
							aParam.push("<?= $paramValue ?>");
							extraParams.push(aParam);
						</script>
					
						<div class="fo-fila">
							<div class="fo-type">
								<?= $paramDescription ?>
							</div>
							<div class="fo-input">
<?
								$statusValue = $extra[$paramId."_status"];
								if($statusValue=="archived")
								{
									echo getString("This entry is archived");
								}
								else
								{
									echo getString("This entry is active");
								}
?>
							</div>
						</div>
<?
						break;
				}
			}
		}
	}
?>
<? /*****/ ?>

	<h2><?= getString("comments") ?></h2>
<div id="comments-entry-window">
<?
  $aComments = getComments($id_entry);
	endRequest();
	for($iComment=0; $iComment<count($aComments); $iComment++)
  {
    $id_comment = $aComments[$iComment]["id_comment"];
    $subject = $aComments[$iComment]["subject"];
    $text = $aComments[$iComment]["text"];
    $id_user = $aComments[$iComment]["id_user"];
    $login = $aComments[$iComment]["login"];
		$comment_id_user = $aComments[$iComment]["id_user"];
    $comment_image = $aComments[$iComment]["image"];
    $comment_login = $aComments[$iComment]["login"];
    $comment_date = $aComments[$iComment]["dateFormatted"];
?>		

		<div class="comment" style="clear:both;">
<?
				if(strlen($comment_image)>0)
				{
					$userProfilePicture = $commonFiles.$dirProfileSquare.$comment_image;
				}
				else
				{
					$userProfilePicture = $commonFiles."images/default.jpg";
				}
?>
				<span class="entrada-comment-profile"><a href="<?= getProfilePage($comment_id_user, $login) ?>" title="<?= getString("View user page") ?>" alt="<?= getString("View user page") ?>"><img src="<?= $userProfilePicture ?>" /></a></span>	

			<h3><?= $subject ?></h3>
			<p>
			<?= allowedHtml($text) ?>
		<!--	<br/>
			<span class="entryDate"><?= $date ?></span> - <span class="entryLogin"><a href="<?= setParams("list.php", Array("id_user" => $id_user)) ?>"><?= $login ?></a></span>-->
			</p>
			<div class="entrada-party">
			<?= $comment_date ?>
		-
			<a href="<?= setParams("list.php", Array("id_user" => $comment_id_user)) ?>"><?= $comment_login ?></a>
	
			</div>
			
			<div style="clear: both;"></div>
		</div>
<?
	  }
?>
</div>	<!-- comments-entry-window-->
<?
	if(isLogged())
	{
?>
		<div class="suscrip" style="float:left;"><img src="<?= $commonFiles ?>images/rss.png" /><a href="<?= $idMeipi ?>.rssComments.php?id_entry=<?= $id_entry ?>"><?= getString("Comments RSS") ?></a></div>
	<h2 style="clear:both;"><?= getString("Post a comment") ?></h2>
		<form method="post" action="<?= $commonFiles ?>actions/newComment.php" name="comment">
			<input type="hidden" name="id_entry" value="<?= $id_entry ?>" />
			<input type="hidden" name="id_meipi" value="<?= $idMeipi ?>" />
			<div class="fo-tabla">						
				<div class="fo-fila">
					<div class="fo-type-p"><?= getString("Title") ?></div>
					<div class="fo-input"><input class="input" type="text" name="subject" /></div>
				</div>
				<div class="fo-fila">
					<div class="fo-type-p">	<?= getString("Text") ?></div>
					<div class="fo-input"><textarea class="input-long" name="comment" style="height:100px;" wrap="virtual" cols="70" rows="12" id="comment"></textarea></div>
				</div>
				<div class="fo-fila">
					<div class="fo-type-p">	&nbsp;</div>
					<div class="fo-input">
						<input type="button" onclick="javascript: submitNewComment()" value="Enviar" />
					</div>
				</div>
			</div>
		</form>
<?
	} else {
?>
<div class="boton">		<a href="javascript:showLoginFormParams('open_entry=<?= $openEntry ?>');"><?= getString("Log in to write a comment") ?></a></div>
<?
	}
?>
</div>
<?
			}
		}
		
		if(!$bLoaded)
		{
?>
	<div id="entryWindow" style="display: none;"></div>
<?
		}
?>
