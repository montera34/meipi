<?
	require_once("common.php");
	require_once($configsPath."config/meipiConfig.php");
	require_once("plugins.php");

//	$languagePath = "../";
	$languagePath = $configsPath;
	require_once($configsPath."functions/language.php");

	// media types
	define(MEIPI_MEDIA_YOUTUBE, 1);
	define(MEIPI_MEDIA_GOOGLEVIDEO, 2);
	define(MEIPI_MEDIA_VIMEO, 4);
	define(MEIPI_MEDIA_ARCHIVEAUDIO, 5);
	define(MEIPI_MEDIA_BLIPTV, 6);
	

	function checkMeipiPassword($request)
	{
		global $idMeipi, $meipiPassword;
		
		if(strlen($meipiPassword)==0)
		{
			// No password required for this meipi
			return;
		}

		// User has permissions for this meipi
		if(canViewMeipimatic($idMeipi))
		{
			return;
		}

		// User sent password in a previous request (check session)
		if($_SESSION["meipiPasswords"][$idMeipi]==$meipiPassword)
		{
			return;
		}

		// User sends password (store in session)
		if(md5($request["meipiPassword"])==$meipiPassword)
		{
			$_SESSION["meipiPasswords"][$idMeipi]=$meipiPassword;
			return;
		}

		// Password not yet provided: Show form and stop processing the request
		require("templates/passwordForm.php");
		endRequest();
		die();
	}
	checkMeipiPassword($_REQUEST);

	function getErrors($a)
	{
		$iErrors = $a["errors"];
		if($iErrors>0)
		{
			for($i=1; $i<=$iErrors; $i++)
			{
				$sErrors .= "<b>".$a["error_".$i]."</b><br/>";
			}
		}
		return $sErrors;
	}

	function getEntriesParamsFromRequest($request)
	{
		//$aParams;
		if(isset($request["min_lat"]))
			$aParams["minLat"]=$request["min_lat"];
		if(isset($request["min_lon"]))
			$aParams["minLon"]=$request["min_lon"];
		if(isset($request["max_lat"]))
			$aParams["maxLat"]=$request["max_lat"];
		if(isset($request["max_lon"]))
			$aParams["maxLon"]=$request["max_lon"];
		if(isset($request["id_user"]))
			$aParams["id_user"]=$request["id_user"];
		//if(isset($request["user"]) && strlen($request["user"])>0)
			//$aParams["user"]=$request["user"];
		//if(isset($request["tag"]) && strlen($request["tag"])>0)
			//$aParams["tag"]=$request["tag"];
		if(isset($request["category"]))
			$aParams["category"]=$request["category"];
		if(isset($request["id_tag"]))
			$aParams["id_tag"]=$request["id_tag"];
		if(isset($request["page"]))
			$aParams["page"]=$request["page"];
		if(isset($request["id_entry"]))
			$aParams["id_entry"]=$request["id_entry"];
		if(isset($request["search"]))
			$aParams["search"]=encode($request["search"]);
		if(isset($request["order"]) && strlen($request["order"])>0)
		{
			switch($request["order"])
			{
				case "date_desc":
					$aParams["order by"]="date";
					$aParams["order desc"]=TRUE;
					break;
					
				case "entry_desc":
					$aParams["order by"]="entry date";
					$aParams["order desc"]=TRUE;
					break;

				case "title_desc":
					$aParams["order by"]="title";
					$aParams["order desc"]=TRUE;
					break;

				case "rank_desc":
					$aParams["order by"]="rank";
					$aParams["order desc"]=TRUE;
					break;

				default:
					$aParams["order by"]=$request["order"];
					break;
			}
		}

		if(isset($request["user"]))
		{
			$idUser = getIdFromUser($request["user"]);
			if($idUser>0)
			{
				$aParams["id_user"] = $idUser;
				if(!isset($aParams["id_user"]))
				{
					echo "<div class=\"warning\">".getString("notUser").$aParams["user"].".</div>";
				}
			}
		}
	
		if(isset($request["tag"]))
		{
			$idTag = getTagId($request["tag"], FALSE);
			if($idTag>0)
			{
				$aParams["id_tag"] = $idTag;
				if(!isset($aParams["id_tag"]))
				{
					echo "<div class=\"warning\">".getString("notTag").$aParams["tag"].".</div>";
				}
			}
		}
	
		global $archiveParam;
		if(strlen($archiveParam)>0 && $request["all"]!="true")
		{
			$aParams["archive"] = $archiveParam;
		}

		return $aParams;
	}
	
	function getEntries($params)
	{
		global $pageSize, $dateFormat, $timeDifference;

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		$sWhere = " WHERE (".ENTRY.".id_user = ".USER.".id_user";

		if($params["located"]=="yes")
			$sWhere.=($sWhere==""?"WHERE (":" AND")." latitude > -5000 AND longitude > -5000";

		if(isset($params["minLat"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." latitude >='".sqlEscape($params["minLat"], $dbLink)."'";
		if(isset($params["maxLat"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." latitude <='".sqlEscape($params["maxLat"], $dbLink)."'";
		if(isset($params["minLon"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." longitude >='".sqlEscape($params["minLon"], $dbLink)."'";
		if(isset($params["maxLon"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." longitude <='".sqlEscape($params["maxLon"], $dbLink)."'";

		if(isset($params["category"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." id_category ='".sqlEscape($params["category"], $dbLink)."'";

		if(isset($params["id_user"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." ".ENTRY.".id_user ='".sqlEscape($params["id_user"], $dbLink)."'";

		if(isset($params["id_entry"]))
			$sWhere.=($sWhere==""?"WHERE (":" AND")." ".ENTRY.".id_entry ='".sqlEscape($params["id_entry"], $dbLink)."'";

		if(isset($params["content"]))
		{
			$sExtraSelect.=", ".CONTENT.".id_content, ".CONTENT.".file, ".CONTENT.".type";
			$sContentJoin.=" LEFT JOIN ".CONTENT." ON ".ENTRY.".id_entry = ".CONTENT.".id_entry";
		}

		if(isset($params["id_tag"]))
		{
			$sExtraSelect.=", ".TAG.".tag_name";
			$sExtraFrom.=", ".ENTRY_TAG.", ".TAG;
			$sWhere.=($sWhere==""?"WHERE (":" AND")." ".ENTRY_TAG.".id_tag ='".sqlEscape($params["id_tag"], $dbLink)."' AND ".ENTRY.".id_entry = ".ENTRY_TAG.".id_entry AND ".ENTRY_TAG.".id_tag = ".TAG.".id_tag";
		}

		if(isset($params["search"]))
		{
			$sSearch = $params["search"];
			$sExtraFrom.=" LEFT JOIN ".ENTRY_TAG." entry_tag_search ON ".ENTRY.".id_entry=entry_tag_search.id_entry LEFT JOIN ".TAG." tag_search ON entry_tag_search.id_tag=tag_search.id_tag LEFT JOIN ".COMMENT." ON ".ENTRY.".id_entry=".COMMENT.".id_entry";
			$sWhere.=($sWhere==""?"WHERE (":" AND")." (title like '%$sSearch%' OR ".ENTRY.".text like '%$sSearch%' OR tag_search.tag_name like '%$sSearch%' OR ".COMMENT.".subject like '%$sSearch%' OR ".COMMENT.".text like '%$sSearch%' OR ".USER.".login like '%$sSearch%')";
		}

		// Special params

		// archive
		if(strlen($params["archive"])>0)
		{
			$sSelectExtraArchive = "SELECT id_entry FROM ".EXTRA." WHERE (extra_".$params["archive"]."<>'archived' AND extra_".$params["archive"]."_date>now()) OR (extra_".$params["archive"]."='permanent')";
			$aExtraIds = dbSelect($sSelectExtraArchive, $dbLink);
		}
		if(isset($aExtraIds) && !isset($params["id_entry"]))
		{
			foreach($aExtraIds as $aExtraId)
			{
				$aAndId[] = $aExtraId["id_entry"];
			}
		}
		
		$sWhere.=($sWhere==""?"":")");

		if(count($aAndId)>0)
		{
			$sAndId = join(",", $aAndId);
			if(strlen($sWhere)>0)
			{
				$sWhere.=($sWhere==""?"WHERE (":" AND (").ENTRY.".id_entry in ($sAndId))";
			}
		}
		// Special params (end)

		if(isset($params["order by"]))
		{
			switch($params["order by"])
			{
				case "entry date":
					$sWhereExtra.=" ORDER BY entry_date";
					break;
				case "title":
					$sWhereExtra.=" ORDER BY title";
					break;
				case "rank":
					$sWhereExtra.=" ORDER BY ranking";
					break;
				case "date":
				default:
					$sWhereExtra.=" ORDER BY ".ENTRY.".date";
					break;
			}
			if(isset($params["order desc"]))
				$sWhereExtra.=($params["order desc"]=="desc"?" DESC":"");
		}

		if(isset($params["limit"]))
		{
			$sWhereExtra.=" LIMIT 0, ".$params["limit"];
		}
		else if(isset($params["page"]))
		{
			$iPage = intval($params["page"])-1;
			$sWhereExtra.=" LIMIT ".($iPage*$pageSize).", ".$pageSize;
		}

		$sQueryRows = "SELECT DISTINCT ".ENTRY.".id_entry, ".ENTRY.".*, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR),'%a, %d %b %Y %T') AS dateRFC, ".USER.".login, ".USER.".image, DATE_FORMAT(DATE_ADD(".ENTRY.".last_edited, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateLastEditedFormatted".$sExtraSelect." FROM ".USER.", ".ENTRY.$sContentJoin.$sExtraFrom.$sWhere;
		$sQuery=$sQueryRows.$sWhereExtra;
		$aEntries = dbSelect($sQuery, $dbLink);

		// Extra params
		global $extraTable, $archiveParam;
		if($extraTable=="true" && isset($aEntries[0]))
		{
			$aIdEntries = Array();
			foreach($aEntries as $aEntry)
			{
				$aIdEntries[] = $aEntry["id_entry"];
			}
			$idEntries = join(",", $aIdEntries);
			
			$sExtraSelect = "";
			if(strlen($archiveParam)>0)
			{
				$sExtraSelect .= ", if(extra_".$archiveParam."_date<now() and extra_".$archiveParam."<>'permanent', 'archived', 'active') AS extra_".$archiveParam."_status";
			}

			$sQueryExtra = "SELECT * $sExtraSelect FROM ".EXTRA." WHERE id_entry in ($idEntries)";
			$aExtra = dbSelect($sQueryExtra, $dbLink);
			if(isset($aExtra))
			{
				foreach($aEntries as $id => $aEntry)
				{
					foreach($aExtra as $idExtra => $aEntryExtra)
					{
						if($aEntry["id_entry"] == $aEntryExtra["id_entry"])
						{
							$aEntries[$id]["extra"] = $aEntryExtra;
							unset($aExtra[$idExtra]);
						}
					}
				}
			}
		}

		$aEntries["selectedRows"] = count($aEntries);
		if(isset($params["getRows"]))
		{
			$aEntries["rows"] = dbGetRows($sQueryRows, $dbLink);
		}

		return executePlugins("getEntries", $aEntries);
	}

	function getTags($id_entry)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aTags, getString("Unable to connect to database"));
			return $aTags;
		}
		$aTags = dbSelect("SELECT DISTINCT ".ENTRY_TAG.".id_tag, ".TAG.".tag_name FROM ".ENTRY_TAG.", ".TAG." WHERE ".ENTRY_TAG.".id_entry = '".sqlEscape($id_entry, $dbLink)."' AND ".ENTRY_TAG.".id_tag = ".TAG.".id_tag ORDER BY id_entry_tag", $dbLink);
		return $aTags;
	}

	function getComments($id_entry, $limit=-1)
	{
		global $dateFormat, $timeDifference;

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aComments, getString("Unable to connect to database"));
			return $aComments;
		}
		if($limit>0)
		{
			$sLimit = " LIMIT ".intval($limit);
		}
		if($id_entry=="ALL")
		{
  		$aComments = dbSelect("SELECT ".COMMENT.".*, DATE_FORMAT(DATE_ADD(".COMMENT.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, DATE_FORMAT(DATE_ADD(".COMMENT.".date, INTERVAL ".intval($timeDifference)." HOUR),'%a, %d %b %Y %T') AS dateRFC, login, image FROM ".COMMENT.", ".USER." WHERE ".COMMENT.".id_user=".USER.".id_user ORDER BY ".COMMENT.".date DESC $sLimit", $dbLink);
		}
		else
		{
			$aComments = dbSelect("SELECT ".COMMENT.".*, DATE_FORMAT(DATE_ADD(".COMMENT.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, ".USER.".login, ".USER.".image  FROM ".COMMENT.", ".USER." WHERE ".COMMENT.".id_entry = '".intval($id_entry)."' AND ".COMMENT.".id_user = ".USER.".id_user ORDER BY date $sLimit", $dbLink);
		}
		return $aComments;
	}

	function getMeipiHead()
	{
		require("templates/meipiHead.php");
	}

	function getNavigationBar($request, $type)
	{
		global $webName, $webTitle, $mainUrl, $projectUrl, $commonFiles, $idMeipi;
		if($request["embedded"]=="true")
		{
			return "";
		}
		require("templates/meipiNavigationBar.php");
		return $navigationBar;
	}

	function getFooter($type)
	{
		global $meipiUrl, $blogUrl, $aboutUrl, $licenseUrl, $legalUrl, $faqUrl, $idMeipi;
		// TODO
		global $_REQUEST;
		$request = $_REQUEST;
		if($request["embedded"]=="true")
		{
			return "<a class=\"embeddedFooter\" href=\"".setParams("meipi.php", Array())."\" target=\"_blank\" alt=\"$idMeipi ".getString("in")." meipi.org\" title=\"$idMeipi ".getString("in")." meipi.org\">&nbsp;</a>";
		}
		require("templates/meipiFooter.php");
	}

	function getSelectionBar($request, $type)
	{
		global $idMeipi;

		$user = $request["user"];
		if(strlen($user)<1 && strlen($request["id_user"])>0)
		{
			$user = getUser($request["id_user"]);
		}

		$tag = $request["tag"];
		if(strlen($tag)<1 && strlen($request["id_tag"])>0)
		{
			$tag = getTag($request["id_tag"]);
		}

		require("templates/meipiSelectionBar.php");
		return $selectionBar;
	}

	function getMeipiDescription()
	{
		global $webDescription, $longDesc, $longDescription;
		require("templates/meipiDescription.php");
		return $selectionBar;
	}

	function getCategories()
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}
		
		return dbSelect("SELECT id_category, category_name FROM ".CATEGORY, $dbLink);
	}
	
	function getCategory($id_category)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}
		$aCategory = dbSelect("SELECT category_name FROM ".CATEGORY." WHERE id_category = '".sqlEscape($id_category, $dbLink)."'", $dbLink);
		return $aCategory[0]["category_name"];
	}

	function getIdFromUser($user)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return -1;
		}
		$aUser = dbSelect("SELECT id_user FROM ".USER." WHERE login = '".sqlEscape($user, $dbLink)."'", $dbLink);

		return $aUser["0"]["id_user"];
	}

	function getTag($id_tag)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}
		$aTag = dbSelect("SELECT tag_name FROM ".TAG." WHERE id_tag = '".sqlEscape($id_tag, $dbLink)."'", $dbLink);
		return $aTag["0"]["tag_name"];
	}

	function getSquareThumbnailsDefault()
	{
		global $mosaicLimit;

		return getSquareThumbnailsDefaultLimit($mosaicLimit);
	}

	function getSquareThumbnailsDefaultLimit($limit)
	{
		global $dateFormat, $timeDifference;

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		if(strlen($limit)>0)
		{
			$limit = intval($limit);
		}
		else
		{
			$limit = 10;
		}

		$sQuery = "SELECT ".ENTRY.".*, ".USER.".login, ".CONTENT.".file, ".CONTENT.".type, ".CONTENT.".id_content, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted FROM ".ENTRY.", ".USER.", ".CONTENT." WHERE ".ENTRY.".id_user = ".USER.".id_user AND ".CONTENT.".id_entry = ".ENTRY.".id_entry AND ".CONTENT.".type=0 ORDER BY date DESC LIMIT ".$limit;
		$aThumbnails = dbSelect($sQuery, $dbLink);
		$aMosaicData["mosaicDesc"] = getString("Default Mosaic");
		$aMosaicData["thumbnails"] = $aThumbnails;
		return $aMosaicData;
	}

	function getSquareThumbnails($idMosaic)
	{
		global $mosaicLimit, $dateFormat, $timeDifference;

		$idMosaic = intval($idMosaic);

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		$sQuery = "SELECT ".MOSAIC.".name, ".USER.".id_user, ".USER.".login FROM ".MOSAIC.", ".USER." WHERE ".MOSAIC.".id_user = ".USER.".id_user AND id_mosaic='".$idMosaic."'";
		$aMosaicInfo = dbSelect($sQuery, $dbLink);
		
		$sQuery = "SELECT ".ENTRY.".*, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, ".USER.".login, ".CONTENT.".file, ".CONTENT.".type, ".MOSAIC_ITEM.".id_content FROM ".MOSAIC_ITEM." LEFT JOIN ".CONTENT." ON ".CONTENT.".id_content = ".MOSAIC_ITEM.".id_content LEFT JOIN ".ENTRY." ON ".CONTENT.".id_entry = ".ENTRY.".id_entry LEFT JOIN ".USER." ON ".ENTRY.".id_user = ".USER.".id_user WHERE id_mosaic='".$idMosaic."' ORDER BY y,x";
		$aThumbnails = dbSelect($sQuery, $dbLink);
		$aMosaicData["mosaicDesc"] = $aMosaicInfo["0"]["name"];
		$aMosaicData["mosaicAuthorName"] = $aMosaicInfo["0"]["login"];
		$aMosaicData["mosaicAuthorId"] = $aMosaicInfo["0"]["id_user"];
		$aMosaicData["thumbnails"] = $aThumbnails;
		return $aMosaicData;
	}

	function getSquareThumbnailsFromRequest($request)
	{
		global $mosaicLimit, $dateFormat, $timeDifference;

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		for($i=0; $i<$mosaicLimit; $i++)
		{
			$idContents .= ($idContents=="" ? "" : ", ");
			$idContents .= intval($request["c_".$i]);
		}
		
		$sQuery = "SELECT ".ENTRY.".*, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, ".USER.".login, ".CONTENT.".id_content, ".CONTENT.".file FROM ".CONTENT." LEFT JOIN ".ENTRY." ON ".CONTENT.".id_entry = ".ENTRY.".id_entry LEFT JOIN ".USER." ON ".ENTRY.".id_user = ".USER.".id_user WHERE id_content IN (".$idContents.") ";
		$aThumbnails = dbSelect($sQuery, $dbLink);

		for($i=0; $i<count($aThumbnails); $i++)
		{
			$aIndex[$aThumbnails[$i]["id_content"]] = $i;
		}

		for($i=0; $i<$mosaicLimit; $i++)
		{
			$aThumbnailsCopy[$i] = $aThumbnails[$aIndex[$request["c_".$i]]];
		}
		$aMosaicData["thumbnails"]=$aThumbnailsCopy;
		return $aMosaicData;
	}

	function getLastMosaics()
	{
		global $lastMosaicsLimit;

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		$sQuery = "SELECT ".MOSAIC.".id_mosaic, ".MOSAIC.".name, ".USER.".login, ".USER.".id_user FROM ".MOSAIC.", ".USER." WHERE ".MOSAIC.".id_user = ".USER.".id_user ORDER BY date_saved DESC LIMIT ".$lastMosaicsLimit;
		$aMosaics = dbSelect($sQuery, $dbLink);
		return $aMosaics;
	}

	function canEditEntry($idEntry)
	{
		global $idMeipi;

		// Only logged users can edit
		if(!isLogged())
			return false;

		$aEntry = getEntries(array("id_entry" => $idEntry));

		// If user is the creator of the entry
		if($aEntry[0]["id_user"]==getIdUser())
			return true;

		// If user has EDITOR permissions for the meipi
		if(isEditor($idMeipi))
			return true;

		// If user can edit the meipi
		if(canEditMeipimatic($idMeipi))
			return true;	

		return false;
	}

	function getEntryFromRequest($request)
	{
		global $minLon, $maxLon, $minLat, $maxLat, $idMeipi;

		$valid = true;

		if($request["no_location"]=="on")
		{
			$longitude=-10000;
			$latitude=-10000;
		}
		else
		{
			$longitude=doubleval($request["longitude"]);
			$latitude=doubleval($request["latitude"]);;

			if(strlen($request["longitude"])<=0)
			{
				$valid = false;
				addError($aEntry, getString("Wrong longitude"));
			}
			else
				$submited = true;

			if((strlen($minLon)>0 && $minLon>$request["longitude"]) || (strlen($maxLon)>0 && $maxLon<$request["longitude"]))
			{
				$valid = false;
				addError($aEntry, getString("Wrong longitude"));
			}

			if(strlen($request["latitude"])<=0)
			{
				$valid = false;
				addError($aEntry, getString("Wrong latitude"));
			}
			else
				$submited = true;

			if((strlen($minLat)>0 && $minLat>$request["latitude"]) || (strlen($maxLat)>0 && $maxLat<$request["latitude"]))
			{
				$valid = false;
				addError($aEntry, getString("Wrong latitude"));
			}
		}
	
		if(!isLogged())
		{
			$valid = false;
			addError($aEntry, getString("Not logged in"));
		}

		if(strlen($request["title"])<=0)
		{
			$valid = false;
			addError($aEntry, getString("Wrong title"));
		}
		else
			$submited = true;
		if(strlen($request["description"])<=0)
		{
			//$valid = false;
			//addError($aEntry, getString("Wrong description"));
		}
		else
			$submited = true;
		if(($request["filetype"]=="video")&&(strlen($request["videotype"])<=0))
		{
			$valid = false;
			addError($aEntry, getString("Wrong video type"));
		}
		else
			$submited = true;
		if(strlen($request["category"])<=0)
		{
			$valid = false;
			addError($aEntry, getString("Wrong category"));
		}
		else
			$submited = true;
		if(strlen($request["address"])<=0)
		{
			//$valid = false;
			//addError($aEntry, getString("Wrong address"));
		}
		else
			$submited = true;
		if(strlen($request["date_day"])>0 && strlen($request["date_month"])>0 && strlen($request["date_year"])>0)
		{
			$date = $request["date_year"]."-".$request["date_month"]."-".$request["date_day"];
		}

		if(strlen($request["url"])<=0)
		{
			//$valid = false;
			//addError($aEntry, getString("Wrong url"));
		}
		else if($request["url"]!="http://")
		{
			$submited = true;
		}
		else
		{
			$request["url"] = "";
		}

		$dbLink = dbConnect();

		$edition = ($request["edition"]!="no");
		if($edition)
		{
			$editionIdEntry = intval($request["edition"]);

			$aEntry = getEntries(array("id_entry" => $editionIdEntry, "content" => "yes"));
			$canEditEntry = canEditEntry($editionIdEntry);

			if(!$canEditEntry)
			{
				$valid = FALSE;
				addError($aEntry, getString("You can't edit this entry"));
			}

			if(strlen($aEntry[0]["file"])==0)
			{
				$editionCanAddContent = TRUE;
			}
		}
		
		if($valid)
		{
			if(!$edition || $editionCanAddContent)
			{
				// Content is only added when the entry is inserted, not when it is modified
				if($request["filetype"]=="photo")
				{
					$image = storeImage($_FILES['uploaded']);
				}
				else if($request["filetype"]=="video")
				{
					$video = storeVideo($request["video"]);
				}
				else if($request["filetype"]=="lively")
				{
					$lively = storeLively($request["lively"]);
				}
			}

			$tags = $request["tags"];
			//$aTempTags = explode(" ", $tags);
			$aTempTags = splitTags($tags);
			foreach($aTempTags as $tag)
			{
				$tag = str_replace('"', "'", $tag);
				$tag = trim(encode($tag));
				if(strlen($tag)>0 && !isset($aTags[$tag]))
				{
					$aTags[$tag] = getTagId($tag);
				}
			}
		}
		
		$aEntry["title"] = encode($request["title"]);
		$aEntry["description"] = encode($request["description"]);
		$aEntry["category"] = encode($request["category"]);
		$aEntry["address"] = encode($request["address"]);
		$aEntry["date"] = encode($date);
		$aEntry["date_day"] = encode($request["date_day"]);
		$aEntry["date_month"] = encode($request["date_month"]);
		$aEntry["date_year"] = encode($request["date_year"]);
		$aEntry["url"] = encode($request["url"]);
		
		$aEntry["id_user"] = getIdUser();

		$aEntry["longitude"] = $longitude;
		$aEntry["latitude"] = $latitude;
		
		$aEntry["image"] = $image;
		$aEntry["video"] = $video;
		$aEntry["videotype"] = $request["videotype"];
		$aEntry["lively"] = $lively;
		$aEntry["tags"] = $aTags;
		
		$aEntry["edition"] = $edition;
		$aEntry["editionIdEntry"] = $editionIdEntry;
		
		// Extra params (special meipis)
		global $extraTable, $aExtraConfig;
		if($extraTable=="true")
		{
			for($iExtraParam=0; $iExtraParam<count($aExtraConfig); $iExtraParam++)
			{
				parse_str($aExtraConfig[$iExtraParam]['config'], $aParamConfig);
				if($aParamConfig["needsEditor"] == "true")
				{
					// Check if user is EDITOR
					if(!isEditor($idMeipi))
					{
						if(!canEditMeipimatic($idMeipi))
						{
							$aEntry["extra_".$aExtraConfig[$iExtraParam]['name']] = $aParamConfig["default"];
							continue;
						}
					}
				}

				if ($aExtraConfig[$iExtraParam]['type']=="text" || $aExtraConfig[$iExtraParam]['type']=="select")
				{
					$aEntry["extra_".$aExtraConfig[$iExtraParam]['name']] = encode($request["extra_".$aExtraConfig[$iExtraParam]['name']]);
				}
				else if($aExtraConfig[$iExtraParam]['type']=="special")
				{
					switch($aExtraConfig[$iExtraParam]['value'])
					{
						default:
						case "cities":
							$aEntry["extra_".$aExtraConfig[$iExtraParam]['name']] = encode($request["extra_".$aExtraConfig[$iExtraParam]['name']]);
							break;

						case "archive":
							break;
					}
				}

				// Add taggable elements to tags list
				parse_str($aExtraConfig[$iExtraParam]['config'], $aParamConfig);
				if($aParamConfig["taggable"] == "true")
				{
					$tag = str_replace('"', "'", $request["extra_".$aExtraConfig[$iExtraParam]['name']]);
					$tag = trim($tag);
					if(strlen($tag)>0 && !isset($aTags[$tag]))
					{
						if(strpos($tag, " ")!==FALSE)
						{
							$tag = encode("'".$tag."'");
						}
						$aTags[$tag] = getTagId($tag);
					}
					$aEntry["tags"] = $aTags;
				}
			}
		}

		$aEntry["ok"]=$valid;
		if(!$submited)
			return ;
		return $aEntry;
	}

	function escape($str, $dbLink)
	{
		return encode($str);
	}
	
	function getStringSize($string)
	{
		global $iconFontSize;
		global $iconFont;
		global $baseFolder;
		$iconFontWithPath = $baseFolder.$iconFont;
		$box = imageTTFBbox($iconFontSize,0,$iconFontWithPath,$string);
		$aSize[width] = abs($box[4] - $box[0]) + 5;
		$aSize[height] = abs($box[5] - $box[1]) + 5;
		return $aSize;
	}

	function isDuplicatedEntry($aEntry)
	{
		$dbLink = dbConnect();
		$address = $aEntry["address"];
		$longitude = $aEntry["longitude"];
		$latitude = $aEntry["latitude"];
		$title = $aEntry["title"];
		$text = $aEntry["description"];
		$id_category = $aEntry["category"];
		$id_user = $aEntry["id_user"];
		$rc = dbSelect("SELECT count(*) AS entries FROM ".ENTRY." WHERE title='$title' AND text='$text' AND address='$address' AND id_user='$id_user' AND (longitude>($longitude-0.00001) AND longitude<($longitude+0.00001)) AND (latitude>($latitude-0.00001) AND latitude<($latitude+0.00001))", $dbLink);
		return ($rc[0]["entries"]>0);
	}

	function insertEntry($aEntry)
	{
		if(!$aEntry["ok"])
		{
			addError($aResult, getString("Invalid Entry"));
			return $aResult;
		}

		if($aEntry["edition"])
		{
			editEntry($aEntry);
		}
		else
		{
			insertNewEntry($aEntry);
		}
	}

	function editEntry($aEntry)
	{
		$idEntry = $aEntry["editionIdEntry"];

		$address = $aEntry["address"];
		$longitude = $aEntry["longitude"];
		$latitude = $aEntry["latitude"];
		$title = $aEntry["title"];
		$text = $aEntry["description"];
		$entry_date = $aEntry["date"];
		$id_category = $aEntry["category"];
		$id_user = $aEntry["id_user"];
		$aTags = $aEntry["tags"];
		$url = $aEntry["url"];
		$image = $aEntry["image"];
		$video = $aEntry["video"];
		$videotype = $aEntry["videotype"];
		$lively = $aEntry["lively"];
		$idEditor = getIdUser();

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}

		$rc = dbUpdate("UPDATE `".ENTRY."` SET address='$address', longitude='$longitude', latitude='$latitude', title='$title', text='$text', id_category='$id_category', entry_date='$entry_date', url='$url', edited=1, last_edited=now(), last_editor='$idEditor' WHERE id_entry='$idEntry'", $dbLink);

		if($rc!==FALSE)
		{
			// Add content (image)
			if(strlen($image)>0)
			{
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$image', '$idEntry', 'Photo', now(), 0)", $dbLink);
			}

			// Add content (video)
			if(strlen($video)>0)
			{
				if($videotype=="googlevideo")
				{
					$videotypecode=MEIPI_MEDIA_GOOGLEVIDEO;
				}
				elseif ($videotype=="vimeo")
                {
                    $videotypecode=MEIPI_MEDIA_VIMEO;
                }
                elseif ($videotype=="archiveaudio")
                {
                    $videotypecode=MEIPI_MEDIA_ARCHIVEAUDIO;
                }
                elseif($videotype=="bliptv")
                {
                    $videotypecode=MEIPI_MEDIA_BLIPTV;
                }
				else
				{
					$videotypecode=MEIPI_MEDIA_YOUTUBE;
				}
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$video', '$idEntry', 'Video', now(), $videotypecode)", $dbLink);
			}

			// Add content (lively)
			if(strlen($lively)>0)
			{
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$lively', '$idEntry', 'Lively', now(), 3)", $dbLink);
			}

			$rcTag = dbUpdate("DELETE FROM `".ENTRY_TAG."` WHERE id_entry='$idEntry'", $dbLink);
			if(count($aTags)>0)
			{
				foreach($aTags as $idTag)
				{
					$rcTag = dbUpdate("INSERT INTO `".ENTRY_TAG."`(id_entry, id_tag, position) VALUES('$idEntry', '$idTag', '$idTag')", $dbLink);
				}
			}

			updateExtraParams($idEntry, $aEntry);

			$aResult["ok"] = true;
		}
		else
		{
			addError($aResult, getString("Error"));
		}
		return $aResult;
	}

	function insertNewEntry($aEntry)
	{
		if(!$aEntry["ok"])
		{
			addError($aResult, getString("Invalid Entry"));
			return $aResult;
		}

		if(isDuplicatedEntry($aEntry))
		{
			addError($aResult, getString("Duplicated Entry"));
			return $aResult;
		}

		$address = $aEntry["address"];
		$longitude = $aEntry["longitude"];
		$latitude = $aEntry["latitude"];
		$title = $aEntry["title"];
		$text = $aEntry["description"];
		$entry_date = $aEntry["date"];
		$id_category = $aEntry["category"];
		$id_user = $aEntry["id_user"];
		$image = $aEntry["image"];
		$video = $aEntry["video"];
		$videotype = $aEntry["videotype"];
		$lively = $aEntry["lively"];
		$aTags = $aEntry["tags"];
		$url = $aEntry["url"];

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}

		$rc = dbUpdate("INSERT INTO `".ENTRY."`(address, longitude, latitude, title, text, id_user, id_category, entry_date, url, date) VALUES('$address', '$longitude', '$latitude', '$title', '$text', '$id_user', '$id_category', '$entry_date', '$url', now())", $dbLink);
		if($rc!==FALSE)
		{
			$idEntry = mysql_insert_id($dbLink);

			if(strlen($image)>0)
			{
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$image', '$idEntry', 'Photo', now(), 0)", $dbLink);
			}

			if(strlen($video)>0)
			{
				if($videotype=="googlevideo")
				{
					$videotypecode=MEIPI_MEDIA_GOOGLEVIDEO;
				}
				elseif($videotype=="vimeo")
                {
                    $videotypecode=MEIPI_MEDIA_VIMEO;
                }
                elseif($videotype=="archiveaudio")
                {
                    $videotypecode=MEIPI_MEDIA_ARCHIVEAUDIO;
                }
                elseif($videotype=="bliptv")
                {
                    $videotypecode=MEIPI_MEDIA_BLIPTV;
                }
				else
				{
					$videotypecode=MEIPI_MEDIA_YOUTUBE;
				}
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$video', '$idEntry', 'Video', now(), $videotypecode)", $dbLink);
			}
			// Add content (lively)
			if(strlen($lively)>0)
			{
				$rcContent = dbUpdate("INSERT INTO `".CONTENT."`(file, id_entry, content_name, date, type) VALUES('$lively', '$idEntry', 'Lively', now(), 3)", $dbLink);
			}

			$rcTag = dbUpdate("DELETE FROM `".ENTRY_TAG."` WHERE id_entry='$idEntry'", $dbLink);
			if(count($aTags)>0)
			{
				foreach($aTags as $idTag)
				{
					$rcTag = dbUpdate("INSERT INTO `".ENTRY_TAG."`(id_entry, id_tag, position) VALUES('$idEntry', '$idTag', '$idTag')", $dbLink);
				}
			}

			if(count($aTags)>0)
			{
				foreach($aTags as $idTag)
				{
					$rcTag = dbUpdate("INSERT INTO `".ENTRY_TAG."`(id_entry, id_tag, position) VALUES('$idEntry', '$idTag', '$idTag')", $dbLink);
				}
			}

			updateExtraParams($idEntry, $aEntry);

			// Add new entry to stats
			dbUpdate("UPDATE meipi_global_stats SET entries=entries+1 LIMIT 1", $dbLink);

			$aResult["ok"] = true;
		}
		else
		{
			addError($aResult, getString("Error"));
		}
		return $aResult;
	}

	/*
	* Make sure there's a row in extra params table for this entry id
	* Set default values for special parameters like archive date
	*/
	function insertExtraParams($idEntry, $aEntry)
	{
		global $extraTable, $aExtraConfig;
		if($extraTable=="true")
		{
			$dbLink = dbConnect();

			$extraParams = "";
			$extraValues = "";
			for($iExtraParam=0; $iExtraParam<count($aExtraConfig); $iExtraParam++)
			{
				parse_str($aExtraConfig[$iExtraParam]['config'], $aParamConfig);
				if($aExtraConfig[$iExtraParam]['type']=="special")
				{
					switch($aExtraConfig[$iExtraParam]['value'])
					{
						default:
							$extraParams .= ", extra_".$aExtraConfig[$iExtraParam]['name'];
							$extraValues .= ", '".$aParamConfig['default']."'";
							break;

						case "archive":
							parse_str($aExtraConfig[$iExtraParam]['config'], $aArchiveConfig);
							if(strlen($aArchiveConfig["category_".$aEntry["category"]])>0)
							{
								$archiveValue = encode($aArchiveConfig["category_".$aEntry["category"]]);
							}
							else
							{
								$archiveValue = "new";
							}
							
							$archiveTime = "30 day";
							if(strlen($aParamConfig["archiveTime"])>0)
								$archiveTime = $aParamConfig["archiveTime"];
								
							$extraParams .= ", extra_".$aExtraConfig[$iExtraParam]['name'].", extra_".$aExtraConfig[$iExtraParam]['name']."_date, extra_".$aExtraConfig[$iExtraParam]['name']."_comment";
							$extraValues .= ", '$archiveValue', date_add(now(), interval ".$archiveTime."), ''";
							break;
					}
				}
				else
				{
					$extraParams .= ", extra_".$aExtraConfig[$iExtraParam]['name'];
					$extraValues .= ", '".$aParamConfig['default']."'";
				}
			}

			// Try to insert (for new entries or entries with no extra params yet)
			$rcExtra = dbUpdate("INSERT INTO `".EXTRA."`(id_entry".$extraParams.") VALUES('$idEntry'".$extraValues.")", $dbLink);
		}
	}

	/*
	*	Updates or inserts extra params when submitting the entry form
	*/
	function updateExtraParams($idEntry, $aEntry)
	{
		global $extraTable, $aExtraConfig, $idMeipi;
		if($extraTable=="true")
		{
			insertExtraParams($idEntry, $aEntry);

			$dbLink = dbConnect();

			$set = "id_entry='$idEntry'";
			
			for($iExtraParam=0; $iExtraParam<count($aExtraConfig); $iExtraParam++)
			{
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

				if ($aExtraConfig[$iExtraParam]['type']=="text" || $aExtraConfig[$iExtraParam]['type']=="select")
				{
					$set .= ", extra_".$aExtraConfig[$iExtraParam]['name']."='".$aEntry["extra_".$aExtraConfig[$iExtraParam]['name']]."'";
				}
				else if($aExtraConfig[$iExtraParam]['type']=="special")
				{
					switch($aExtraConfig[$iExtraParam]['value'])
					{
						default:
						case "cities":
							$set .= ", extra_".$aExtraConfig[$iExtraParam]['name']."='".$aEntry["extra_".$aExtraConfig[$iExtraParam]['name']]."'";
							break;

						case "archive":
							// Do not update here: it will be updated with different actions
							break;
					}
				}
			}
			$rcExtra = dbUpdate("UPDATE `".EXTRA."` SET $set WHERE id_entry='$idEntry'", $dbLink);
		}
	}

	function doubleValueOrNull($value)
	{
		if(strlen($value)>0)
		{
			return doubleval($value);
		}
		else
		{
			return "null";
		}
	}

	function isValidImage($file)
	{
		if(!is_uploaded_file($file["tmp_name"]))
		{
			echo "!is_uploaded_file()";
		}

		if(strpos($file["type"], "image")!==0)
		{
			echo "Invalid mime-type";
			print_r($file);
			return FALSE;
		}
		return TRUE;
	}
	
	function storeVideo($videoId)
	{
		$videoId = ereg_replace(".*v=", "", $videoId);
		$videoId = ereg_replace(".*docid=", "", $videoId);
		$videoId = ereg_replace("&.*", "", $videoId);
        $videoId = ereg_replace(".*details/", "", $videoId);
        $videoId = ereg_replace(".*download/", "", $videoId);
        $videoId = ereg_replace(".*play/", "", $videoId);
		$videoId = ereg_replace(".*file/", "", $videoId);
		$videoId = ereg_replace(".*vimeo.com/", "", $videoId);
		if(strlen($videoId)>0)
			return encode($videoId);
	}

	function storeLively($livelyId)
	{
		global $livelyTypeEnabled;

		if("true"==$livelyTypeEnabled)
		{
			$livelyId = ereg_replace(".*rid=", "", $livelyId);
			$livelyId = ereg_replace("&.*", "", $livelyId);
			if(strlen($livelyId)>0)
				return encode($livelyId);
		}
	}

	function storeImage($file)
	{
		global $imageTypes;
		return storeImageTypes($file, $imageTypes);
	}
	
	function storeImageProfile($file)
	{
		global $imageTypesProfile;
		return storeImageTypes($file, $imageTypesProfile);
	}

	function storeImageTypes($file, $types)
	{
		global $baseFolder;

		if(!isset($file["tmp_name"]) || strlen($file["tmp_name"])<1)
		{
			return FALSE;
		}

		if(!isValidImage($file))
		{
			return FALSE;
		}
		$tmp_name = $file["tmp_name"];
		$aFileName = explode("/", $file["tmp_name"]);
		$tempFileName = $aFileName[count($aFileName)-1];

		foreach($types as $imageType)
		{
			$dir = $baseFolder.$imageType["dir"];
			$size = $imageType["size"];
			$aCrop = $imageType["crop"];

			if($size=="100%")
			{
				exec("convert $tmp_name $dir$tempFileName");
			}
			else if(isset($size))
			{
				//convert -resize 1500x1500\> -quality 100 -antialias test1_500x500 test1_1500x1500_gt

				exec("convert -resize $size -quality 100 -antialias $tmp_name $dir$tempFileName");
			}
			else if(isset($aCrop[0]))
			{
				$width = $aCrop[0];
				if(isset($aCrop[1]))
					$height = $aCrop[1];
				else
					$height = $width;

				//"convert -resize x160 -resize 160x\< -resize 50% -gravity center -crop 80x80+0+0 "
				exec("convert -resize x".(2*$height)." -resize ".(2*$width)."x\< -resize 50% -gravity center -crop ".$width."x".$height."+0+0 $tmp_name $dir$tempFileName");
				exec("convert +repage -resize ".$height."x".$width."\!  $dir$tempFileName $dir$tempFileName");
			}
		}
		return $tempFileName;
	}

	function getTagId($tag, $insert=TRUE)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return -1;
		}

		$tag = trim($tag);
		if(strlen($tag)==0)
		{
			return -1;
		}

		$sTag = sqlEscape($tag, $dbLink);

		$aTag = dbSelect("SELECT * FROM ".TAG." WHERE tag_name='$sTag'", $dbLink);
		//echo "SELECT * FROM tag WHERE tag_name='$sTag'";
		if(count($aTag)>0)
		{
			return $aTag[0]["id_tag"];
		}
		else if($insert)
		{

			$rc = dbUpdate("INSERT INTO `".TAG."`(tag_name) VALUES('$sTag')", $dbLink);
			if($rc!==FALSE)
			{
				$idTag = mysql_insert_id($dbLink);

				return $idTag;
			}
		}
		else
		{
			return ;
		}
	}

	function getCommentFromRequest($request)
	{
		$aComment["id_entry"] = $request["id_entry"];
		if(!isset($request["subject"]) || strlen($request["subject"])==0
		|| !isset($request["comment"]) || strlen($request["comment"])==0  
		|| !isset($request["id_entry"]) || strlen($request["id_entry"])==0
		|| !isLogged())
		{
			$aComment["ok"] = false;
			return $aComment;
		}
		$dbLink = dbConnect();
		$aComment["id_entry"] = encode($request["id_entry"]);
		$aComment["subject"] = encode($request["subject"]);
		$aComment["comment"] = encode($request["comment"]);
		$aComment["id_user"] = getIdUser();
		$aComment["ok"] = true;
		return $aComment;
	}

	function isDuplicatedComment($aComment)
	{
		$dbLink = dbConnect();

		$idEntry = $aComment["id_entry"];
		$subject = $aComment["subject"];
		$text = $aComment["comment"];
		$idUser = $aComment["id_user"];

		$rc = dbSelect("SELECT count(*) AS comments FROM ".COMMENT." WHERE subject='$subject' AND text='$text' AND id_user='$idUser' AND id_entry='$idEntry'", $dbLink);
		return ($rc[0]["comments"]>0);
	}

	function insertComment($aComment)
	{
		global $idMeipi;

		if(!$aComment["ok"])
			return;

		if(isDuplicatedComment($aComment))
			return;

		$idEntry = $aComment["id_entry"];
		$subject = $aComment["subject"];
		$text = $aComment["comment"];
		$idUser = $aComment["id_user"];

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return;
		}

		$aEntry = getEntries(array("id_entry" => $idEntry, "content" => "no"));
		if(count($aEntry)<1)
		{
			// Entry not found!
			return;
		}

		dbUpdate("INSERT INTO `".COMMENT."`(subject, text, id_user, id_entry, date) VALUES('$subject', '$text', '$idUser', '$idEntry', now())", $dbLink);
	
		updateComments($idEntry);

		// TODO: Finish and test mail sending functionality for comments
		$mailAddress = getMailAddress($aEntry[0]["id_user"]);
		if(strlen($mailAddress)>0)
		{
			$fromUser = getUser($idUser);

			$body = getString("The following user added a comment to your entries at meipi.org");
			$body .= " <a href=\"http://www.meipi.org".getProfilePage($idUser, $fromUser)."\">$fromUser</a>";
			$body .= "<br/>";
			$body .= "\r\n\r\n";
			$body .= getString("This is the link to your entry");
			$body .= " <a href=\"http://www.meipi.org/$idMeipi.meipi.php?open_entry=$idEntry\">".$aEntry[0]["title"]."</a>";
			$body .= "<br/>";
			$body .= "\r\n\r\n";
			$body .= getString("Here is the comment:");
			$body .= "<hr/>";
			$body .= "\r\n\r\n";
			$body .= $subject;
			$body .= "<br/>";
			$body .= $text;
			$body .= "\r\n\r\n";
			$body .= "<hr/>";

			//sendEmail($mailAddress, getString("Comment to your entry at Meipi.org"), $body, "no-reply@meipi.org", "Meipi comment", "", "");
		}
	}

	function updateComments($idEntry)
	{
		$dbLink = dbConnect();

		$sQueryComments = "SELECT count(id_comment) AS comments FROM ".COMMENT." WHERE id_entry='$idEntry' LIMIT 1";
		$aComments = dbSelect($sQueryComments, $dbLink);
		if(count($aComments)>0)
		{
			$comments = $aComments[0]["comments"];
			$sUpdateComments = "UPDATE `".ENTRY."` SET comments='$comments' WHERE id_entry='$idEntry' LIMIT 1";
			dbUpdate($sUpdateComments, $dbLink);
		}
	}

	function getTagsCloud()
	{
		global $tagCloud;
		foreach($tagCloud as $tagStyle)
		{
			$tagsLimit += $tagStyle;
		}

		$dbLink = dbConnect();
		$aTags = dbSelect("SELECT ".TAG.".*, count(".TAG.".id_tag) AS count FROM ".TAG.", ".ENTRY_TAG." WHERE ".TAG.".id_tag=".ENTRY_TAG.".id_tag GROUP BY ".TAG.".id_tag ORDER BY count DESC LIMIT $tagsLimit", $dbLink);

		$iTag = 0;
		for($iTagStyle=0; $iTagStyle<count($tagCloud); $iTagStyle++)
		{
			for($i=0; $i<$tagCloud[$iTagStyle] && isset($aTags[$iTag]); $i++)
			{
				$aTags[$iTag]["class"] = $iTagStyle+1;
				$iTag++;
			}
		}
		endRequest();
		return $aTags;
	}

	function getVoteFromRequest($request)
	{
		if(!isLogged())
		{
			return ;
		}

		if(strlen($request["id_entry"])<=0)
		{
			return ;
		}
		
		if(strlen($request["vote"])<=0)
		{
			return ;
		}
		
		$iVote = floatval($request["vote"]);
		if($iVote<-10)
		{
			$iVote = -10;
		}
		else if($iVote>10)
		{
			$iVote = 10;
		}	
		$aVote["id_user"] = getIdUser();
		$aVote["id_entry"] = intval($request["id_entry"]);
		$aVote["vote"] = $iVote;
		$aVote["ok"] = TRUE;

		return $aVote;
	}

	function insertVote($aVote)
	{
		if(!$aVote["ok"])
		{
			return ;
		}

		$idUser = $aVote["id_user"];
		$idEntry = $aVote["id_entry"];
		$iVote = $aVote["vote"];

		$dbLink = dbConnect();

		$aVoted = dbSelect("SELECT id_vote, vote FROM ".VOTE." WHERE id_user='$idUser' AND id_entry='$idEntry' LIMIT 1", $dbLink);

		if(count($aVoted)>0)
		{
			$idVote = intval($aVoted[0]["id_vote"]);
			dbUpdate("UPDATE `".VOTE."` SET vote='$iVote' WHERE id_vote='$idVote' LIMIT 1", $dbLink);
		}
		else
		{
			dbUpdate("INSERT INTO `".VOTE."`(id_user, id_entry, vote) VALUES('$idUser', '$idEntry', '$iVote')", $dbLink);
		}

		updateRanking($idEntry);
	}

	function updateRanking($idEntry)
	{
		$dbLink = dbConnect();

		$sQueryRanking = "SELECT sum(vote)/count(vote) AS ranking, count(vote) AS votes FROM ".VOTE." WHERE id_entry='$idEntry' LIMIT 1";
		$aRanking = dbSelect($sQueryRanking, $dbLink);
		if(count($aRanking)>0)
		{
			$ranking = $aRanking[0]["ranking"];
			$votes = $aRanking[0]["votes"];
			$sUpdateRanking = "UPDATE `".ENTRY."` SET ranking='$ranking', votes='$votes' WHERE id_entry='$idEntry' LIMIT 1";
			dbUpdate($sUpdateRanking, $dbLink);
		}
	}

	function getMosaicFromRequest($request)
	{
		$idUser = getIdUser();
		if(!isLogged())
			return ;

		if(!isset($request["length"]) || strlen($request["length"])<1)
			return ;
		if(!isset($request["name"]) || strlen($request["name"])<1)
			return ;
		$length = intval($request["length"]);

		for($i=0; $i<$length; $i++)
		{
			if(!isset($request["x_".$i])) // || strlen($request["x_".$i])<1)
				return ;
			if(!isset($request["y_".$i])) // || strlen($request["y_".$i])<1)
				return ;
			if(!isset($request["c_".$i])) // || strlen($request["c_".$i])<1)
				return ;
			$aMosaic["item"][$i]["x"] = intval($request["x_".$i]);
			$aMosaic["item"][$i]["y"] = intval($request["y_".$i]);
			$aMosaic["item"][$i]["c"] = intval($request["c_".$i]);
		}

		$aMosaic["id_user"] = $idUser;

		$dbLink = dbConnect();
		$aMosaic["name"] = encode($request["name"]);

		$aMosaic["ok"] = TRUE;

		return $aMosaic;
	}

	function insertMosaic($aMosaic)
	{
		if(!$aMosaic["ok"])
			return ;

		$idUser = $aMosaic["id_user"];
		$mosaicName = $aMosaic["name"];

		$dbLink = dbConnect();
		dbUpdate("INSERT INTO `".MOSAIC."`(id_user, name, date_created, date_saved, rows, cols) VALUES('$idUser', '$mosaicName', now(), now(), 5, 7)", $dbLink);

		$aUserMosaic = dbSelect("SELECT * FROM ".MOSAIC." WHERE id_user='$idUser' AND name='$mosaicName' ORDER BY id_mosaic DESC LIMIT 1", $dbLink);
		if(count($aUserMosaic)<1)
			return ;

		$idMosaic = $aUserMosaic["0"]["id_mosaic"];

		dbUpdate("UPDATE `".MOSAIC."` SET date_saved=now() WHERE id_mosaic='$idMosaic'", $dbLink);
		dbUpdate("DELETE FROM ".MOSAIC_ITEM." WHERE id_mosaic='$idMosaic'", $dbLink);

		for($i=0; $i<count($aMosaic["item"]); $i++)
		{
			$idContent = $aMosaic["item"][$i]["c"];
			$x = $aMosaic["item"][$i]["x"];
			$y = $aMosaic["item"][$i]["y"];
			dbUpdate("INSERT INTO `".MOSAIC_ITEM."`(id_mosaic, id_content, x, y) VALUES('$idMosaic', '$idContent', '$x', '$y')", $dbLink);
		}
	}

	/** 0 <= votes <= 20 */
	function votesToColor($votes)
	{
		$iVotes = intval($votes);
		return "rgb(".(10*$iVotes).", ".(10*$iVotes).", ".(255-10*$iVotes).")";
	}

	function splitTags($str)
	{
		$DEFAULT = 0;
		$DOUBLE_QUOTED = 1;
		$SINGLE_QUOTED = 2;
		$estate = $DEFAULT;
		$iItems = 0;
		$aItems = Array();
		$length=strlen($str);
		for($i=0; $i<$length; $i++)
		{
			switch($estate)
			{
				case $SINGLE_QUOTED:
					switch($str[$i])
					{
						case '\'':
							$estate = $DEFAULT;
							$last .= $str[$i];
							break;

						default:
							$last .= $str[$i];
							break;
					}
					break;

				case $DOUBLE_QUOTED:
					switch($str[$i])
					{
						case '"':
							$estate = $DEFAULT;
							$last .= $str[$i];
							break;

						default:
							$last .= $str[$i];
							break;
					}
					break;

				default:
					switch($str[$i])
					{
						case '\'':
							$estate = $SINGLE_QUOTED;
							$last .= $str[$i];
							break;

						case '"':
							$estate = $DOUBLE_QUOTED;
							$last .= $str[$i];
							break;

						case ' ':
						case ',':
							if(strlen($last)>0)
							{
								$aItems[$iItems] = $last;
								$iItems++;
								$last = "";
							}
							break;

						default:
							$last .= $str[$i];
							break;
					}
					break;
			}
		}
		if(strlen($last)>0)
		{
			$aItems[$iItems] = $last;
			$iItems++;
			$last = "";
		}
		return $aItems;
	}

	function getUserInfoParamsFromRequest($request)
	{
		if(isset($request["id_user"]) && strlen($request["id_user"])>0)
		{
			$aUser["id_user"] = intval($request["id_user"]);
			return $aUser;
		}
		else if(isset($request["user"]) && strlen($request["user"])>0)
		{
			$dbLink = dbConnect();
			$aUser["user"] = encode($request["user"]);

			return $aUser;
		}
		else
		{
			return ;
		}
	}

	function getUserInfo($aUser)
	{
		global $dateFormat;

		// $aUserInfo
		$idUser = $aUser["id_user"];
		$user = $aUser["user"];

		$dbLink = dbConnect();

		if(isset($aUser["id_user"]))
		{
			$sUserQuery = "SELECT id_user, login, mail, web, date, DATE_FORMAT(DATE_ADD(".USER.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, fullname, about, image, mail_subscription FROM ".USER." WHERE id_user='$idUser' LIMIT 1";
		}
		else if(isset($aUser["user"]))
		{
			$sUserQuery = "SELECT id_user, login, mail, web, date, DATE_FORMAT(DATE_ADD(".USER.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, fullname, about, image, mail_subscription FROM ".USER." WHERE login='$user' LIMIT 1";
		}
		else
		{
			return;
		}
		$aUserInfoSelected = dbSelect($sUserQuery, $dbLink);

		$web = trim($aUserInfoSelected[0]["web"]);
		if(strlen($web)>0)
		{
			$web = str_replace('"', "'", $web);
			if(strpos($web, "http://")!==0 && strpos($web, "https://")!==0)
			{
				$web = "http://".$web;
			}
		}
		$aUserInfoSelected[0]["web"] = $web;

		$aUserInfo["user"] = $aUserInfoSelected[0];

		if(dbGetSelectedRows($aUserInfo["user"])==0)
		{
			return;
		}

		$idUser = $aUserInfo["user"]["id_user"];

		global $idMeipi;
		if(strlen($idMeipi)>0)
		{
			$aEntriesParams = array("order by" => "date", "order desc" => "desc", "id_user" => $aUserInfo["user"]["id_user"]);
			$aUserInfo["entries"] = getEntries($aEntriesParams);

			$sCommentsQuery = "SELECT ".COMMENT.".*, DATE_FORMAT(DATE_ADD(".COMMENT.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted, ".ENTRY.".id_entry, title FROM ".COMMENT.", ".ENTRY." WHERE ".COMMENT.".id_entry=".ENTRY.".id_entry AND ".COMMENT.".id_user='$idUser' ORDER BY date DESC LIMIT 5";
			$aUserInfo["comments"] = dbSelect($sCommentsQuery, $dbLink);
		}

		return $aUserInfo;
	}

	function getUserUpdateFromRequest($request)
	{
		global $lang;

		if($request["update"]!="true")
		{
			return;
		}
		
		if(!isLogged())
		{
			addError($aUser, getString("Sorry, you're not logged in!"), "param_login");
			return $aUser;
		}
		
		$valid = TRUE;

		$idUser = getIdUser();
		$web = encode($request["web"]);
		$fullname = encode($request["fullname"]);
		$about = encode($request["about"]);
		$mail = encode($request["mail"]);
		if(!isValidEmail($mail))
		{
			addError($aUser, getString("Invalid email"), "param_mail_update");
			$valid = FALSE;
		}
		$mailSubscription = ($request["mail_subscription"]=="on" ? "1" : "0");
		
		if(strlen($request["old_pwd"])>0 && strlen($request["pwd1"])>0 && strlen($request["pwd2"])>0 && $request["pwd1"]==$request["pwd2"])
		{
			$oldPwd = md5($request["old_pwd"]);
			$result = dbSelect("SELECT id_user FROM user WHERE id_user='$idUser' AND password='$oldPwd' LIMIT 1", $dbLink);
			if(count($result)==1)
			{
				$pwd = md5($request["pwd1"]);
			}
			else
			{
				addError($aUser, getString("Invalid password"), "param_password");
				$valid = FALSE;
			}
		}
		else if(strlen($request["old_pwd"])>0 || strlen($request["pwd1"])>0 || strlen($request["pwd2"])>0)
		{
			addError($aUser, getString("Missing pwd or pwd do not match"), "param_password");
			$valid = FALSE;
		}
		
		if($valid)
		{
			$image = storeImageProfile($_FILES['picture']);
		}

		$aUser["id_user"] = $idUser;
		$aUser["web"] = $web;
		$aUser["fullname"] = $fullname;
		$aUser["about"] = $about;
		$aUser["mail"] = $mail;
		$aUser["mailSubscription"] = $mailSubscription;
		$aUser["pwd"] = $pwd;
		$aUser["image"] = $image;
		$aUser["valid"] = $valid;
		$aUser["lang"] = encode($lang);

		return $aUser;
	}
	
	function updateUser($aUser)
	{
		if(!$aUser["valid"])
		{
			return;
		}

		$idUser = $aUser["id_user"];
		$web = $aUser["web"];
		$about = $aUser["about"];
		$fullname = $aUser["fullname"];
		$mail = $aUser["mail"];
		$mailSubscription = intval($aUser["mailSubscription"]);
		$lang = $aUser["lang"];

		$pwd = $aUser["pwd"];
		if(strlen($pwd)>0)
		{
			$pwdUpdate = ", password='$pwd'";
		}

		$image = $aUser["image"];
		if(strlen($image)>0)
		{
			$imageUpdate = ", image='$image'";
		}

		$dbLink = dbConnect();
		$rc = dbUpdate("UPDATE ".USER." SET fullname='$fullname', mail='$mail', mail_subscription='$mailSubscription', web='$web', about='$about', language='$lang' $pwdUpdate $imageUpdate WHERE id_user='$idUser'", $dbLink);

		return $rc>0;
	}
	
	function htmlEncode($str)
	{
		if($str[strlen($str)-1]=='\\')
			return $str." ";
		return $str;
		/*$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&lt;", "<", $str);
		return htmlentities($str, ENT_NOQUOTES, "ISO-8859-1");*/
	}

	function htmlDecode($str)
	{
		//return html_entity_decode($str, ENT_NOQUOTES, "ISO-8859-1");
		return html_entity_decode($str, ENT_NOQUOTES, "cp1252");
	}

	function deleteContent($idContent, $file, $type)
	{
		global $imageTypes, $baseFolder;

		$dbLink = dbConnect();
		// Delete images (type=0) from file system
		if($type==0)
		{
			foreach($imageTypes as $type => $imageType)
			{
				$dir = $imageType["dir"];
				unlink($dir.$file);
			}
		}
		$rc = dbUpdate("DELETE FROM ".CONTENT." WHERE id_content='$idContent'", $dbLink);
		$rc = dbUpdate("UPDATE ".MOSAIC_ITEM." SET id_content='0' WHERE id_content='$idContent'", $dbLink);
	}

	function deleteEntry($idEntry)
	{
		if(!isLogged())
		{
			return getString("You need to log in");
		}

		$idEntry = intval($idEntry);
		$aParams["id_entry"]=$idEntry;
		$aParams["content"]="yes";
		$aEntries = getEntries($aParams);

		if(dbGetSelectedRows($aEntries)<1)
		{
			return getString("Entry not found");
		}
		
		$entryIdUser = $aEntries[0]["id_user"];
		if(!canEditEntry($idEntry))
		{
			return getString("You can only delete your entries"); // ToDo? You can't delete this entry
		}

		$dbLink = dbConnect();

		$aContents = dbSelect("SELECT * FROM ".CONTENT." WHERE id_entry='$idEntry'", $dbLink);
		for($i=0; $i<count($aContents); $i++)
		{
			deleteContent($aContents[$i]["id_content"], $aContents[$i]["file"], $aContents[$i]["type"]);
		}
		$rc = dbUpdate("DELETE FROM ".COMMENT." WHERE id_entry='$idEntry'", $dbLink);
		$rc = dbUpdate("DELETE FROM ".ENTRY_TAG." WHERE id_entry='$idEntry'", $dbLink);
		$rc = dbUpdate("DELETE FROM ".VOTE." WHERE id_entry='$idEntry'", $dbLink);
		$rc = dbUpdate("DELETE FROM ".ENTRY." WHERE id_entry='$idEntry' LIMIT 1", $dbLink);

		// Remove deleted entry from stats
		dbUpdate("UPDATE meipi_global_stats SET entries=entries-1 LIMIT 1", $dbLink);

		return "0";
	}

	function getMosaicOperation($request)
	{
		if(!isLogged())
		{
			return ;
		}

		$idUser = getIdUser();
		
		if(strlen($request["operation"])>0)
		{
			if($request["operation"]=="add")
			{
				$aMosaicOperation["operation"]="add";
				$aMosaicOperation["id_user"]=$idUser;
				$aMosaicOperation["id_content"]=intval($request["id_content"]);
				$aMosaicOperation["ok"]=TRUE;
			}
			else if($request["operation"]=="del")
			{
				$aMosaicOperation["operation"]="del";
				$aMosaicOperation["id_user"]=$idUser;
				$aMosaicOperation["id_content"]=intval($request["id_content"]);
				$aMosaicOperation["ok"]=TRUE;
			}
		}
		return $aMosaicOperation;
	}

	function doMosaicOperation($aMosaicOperation)
	{
		global $mosaicSelectedItemsLimit;
		
		if(!$aMosaicOperation["ok"])
		{
			return ;
		}

		switch($aMosaicOperation["operation"])
		{
			case "add":
				$idContent = $aMosaicOperation["id_content"];
				$idUser = $aMosaicOperation["id_user"];
				
				$dbLink = dbConnect();
				dbUpdate("INSERT INTO `".SELECTED_ITEM."`(id_user, id_content, date) VALUES('$idUser', '$idContent', now())", $dbLink);
				$aCountItems = dbSelect("SELECT count(*) AS items FROM ".SELECTED_ITEM." WHERE id_user='$idUser'", $dbLink);
				$items = intval($aCountItems[0]["items"]);
				if(strlen($mosaicSelectedItemsLimit)>0)
				{
					$mosaicSelectedItemsLimit = intval($mosaicSelectedItemsLimit);
				}
				else
				{
					$mosaicSelectedItemsLimit = 10;
				}
				$extraItems = $items-$mosaicSelectedItemsLimit;
				if($extraItems>0)
				{
					dbUpdate("DELETE FROM `".SELECTED_ITEM."` WHERE id_user='$idUser' ORDER BY date ASC LIMIT $extraItems", $dbLink);
				}
				break;
			
			case "del":
				$idContent = $aMosaicOperation["id_content"];
				$idUser = $aMosaicOperation["id_user"];
				
				$dbLink = dbConnect();
				dbUpdate("DELETE FROM `".SELECTED_ITEM."` WHERE id_user='$idUser' AND '$idContent'", $dbLink);
				break;
				
			default:
				break;
		}
	}

	function getSelectedItems()
	{
		global $dateFormat, $timeDifference, $mosaicSelectedItemsLimit;

		if(!isLogged())
		{
			return ;
		}

		if(strlen($mosaicSelectedItemsLimit)>0)
		{
			$mosaicSelectedItemsLimit = intval($mosaicSelectedItemsLimit);
		}
		else
		{
			$mosaicSelectedItemsLimit = 10;
		}

		$idUser = getIdUser();

		$dbLink = dbConnect();
		$aSelectedItems = dbSelect("SELECT ".SELECTED_ITEM.".*, ".CONTENT.".file, ".CONTENT.".type, ".ENTRY.".*, ".USER.".login, DATE_FORMAT(DATE_ADD(".ENTRY.".date, INTERVAL ".intval($timeDifference)." HOUR), '$dateFormat') AS dateFormatted FROM ".SELECTED_ITEM.", ".CONTENT.", ".ENTRY.", ".USER." WHERE ".SELECTED_ITEM.".id_user=$idUser AND ".CONTENT.".id_content=".SELECTED_ITEM.".id_content AND ".ENTRY.".id_entry=".CONTENT.".id_entry AND ".ENTRY.".id_user=".USER.".id_user ORDER BY ".SELECTED_ITEM.".date DESC LIMIT $mosaicSelectedItemsLimit", $dbLink);
		return $aSelectedItems;
	}

	function getOnLoadContent($request)
	{
		global $dirThumbnail, $dirEntry, $idMeipi;

		$bShowEntryForm = ($request["showEntryForm"]=="true" && isLogged());
		if($bShowEntryForm)
		{
			$onLoadContent .= "showNewEntryForm();\n";
		}

		$openEntry = $request["open_entry"];
		if(strlen($openEntry)>0)
		{
			$openEntry = intval($openEntry);
			if($openEntry==-1)
			{
				$onLoadContent .= "showEntryWindow('$idMeipi', $openEntry, '$dirEntry', '".getIdUser()."', ".(isLogged() ? "'yes'" : "'no'").");\n";
				$onLoadContent .= "setInterval(\"showEntryWindow('$idMeipi', $openEntry, '$dirEntry', '".getIdUser()."', ".(isLogged() ? "'yes'" : "'no'").");\", 10000);";
			}
		}

		if($request["save_mosaic"]=="true")
		{
			$onLoadContent .= "saveMosaic();\n";
		}

		$editEntry = $request["edit_entry"];
		if(strlen($editEntry)>0)
		{
			if(isLogged())
			{
				$entryId = intval($editEntry);
				$aParams["id_entry"]=$entryId;
				$aParams["content"]="yes";
				$aEntries = getEntries($aParams);
				if(dbGetSelectedRows($aEntries)>0)
				{
					$entryIdUser = $aEntries[0]["id_user"];
					if($entryIdUser==getIdUser())
					{
						$entryTitle = $aEntries[0]["title"];
						$entryDescription = $aEntries[0]["text"];
						$entryUrl = $aEntries[0]["url"];
						$entryCategory = $aEntries[0]["id_category"];
						$entryAddress = $aEntries[0]["address"];
						$entryLongitude = $aEntries[0]["longitude"];
						$entryLatitude = $aEntries[0]["latitude"];
						$entryType = $aEntries[0]["type"];
						$entryContent = $aEntries[0]["content"];
						$aTags = getTags($entryId);
						$entryTags = "";
						for($iTag=0; $iTag<count($aTags); $iTag++)
						{
							if($iTag>0)
							{
								$entryTags .= " ";
							}
							$entryTags .= $aTags[$iTag]["tag_name"];
						}

						$onLoadContent .= "showNewEntryForm($entryId,'$entryTitle','$entryDescription','$entryUrl','$entryCategory','$entryAddress','$entryLongitude','$entryLatitude','$entryTags', new Array(), '$entryType', '$entryContent');\n";
					}
				}
			}
		}

		return $onLoadContent;
	}

	function getOnLoadFunction($request)
	{
		$onLoadContent = getOnLoadContent($request);

		if(strlen($onLoadContent)>0)
		{
			return "function onLoad() {\n$onLoadContent\n}\n";
		}
		else
		{
			return ;
		}
	}
	
	function getOnLoadScript($request)
	{
		$onLoadFunction = getOnLoadFunction($request);

		if(strlen($onLoadFunction)>0)
		{
			return "<script type=\"text/javascript\">\n$onLoadFunction\n</script>\n";
		}
		else
		{
			return ;
		}
	}
	
	function getOnLoadCall($str)
	{
		if(strlen($str)>0)
			return "onLoad=\"onLoad()\"";
	}

	function getRandomIdEntry()
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aEntries, getString("Unable to connect to database"));
			return $aEntries;
		}

		$aIdEntry = dbSelect("SELECT id_entry FROM ".ENTRY." ORDER BY rand() LIMIT 1", $dbLink);
		return $aIdEntry[0]["id_entry"];
	}
	
	function canEditMeipimatic($idMeipi)
	{
		// Only logged users can edit
		if(!isLogged())
			return false;
		// Make sure meipi info is in the session (edit)
		checkMeipiId($idMeipi, null);

		// The owner can edit the meipimatic
		if($_SESSION["meipi_creator"][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]]==getIdUser())
			return true;

		// If it has ADMIN permissions for the meipi
		if(isAdmin($idMeipi))
			return true;

		return false;
	}

	function canViewMeipimatic($idMeipi)
	{
		// Check only if user can view meipi based on user permissions: user must be logged
		if(!isLogged())
			return false;

		// If user can edit the meipimatic
		if(canEditMeipimatic($idMeipi))
			return true;

		// If user has EDITOR permissions for the meipi
		if($_SESSION["permission"][getIdUser()][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]][2])
			return true;

		// If user has VIEWER permissions for the meipi
		if($_SESSION["permission"][getIdUser()][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]][3])
			return true;

		return false;
	}

	function isEditor($idMeipi)
	{
		// Check only if user can view meipi based on user permissions: user must be logged
		if(!isLogged())
			return false;

		// If user has EDITOR permissions for the meipi
		if($_SESSION["permission"][getIdUser()][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]][2])
			return true;
	}

	function isAdmin($idMeipi)
	{
		// Check only if user can view meipi based on user permissions: user must be logged
		if(!isLogged())
			return false;

		// If user has ADMIN permissions for the meipi
		if($_SESSION["permission"][getIdUser()][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]][1])
			return true;
	}

	function isCreator($idMeipi)
	{
		// Check only if user can view meipi based on user permissions: user must be logged
		if(!isLogged())
			return false;

		// If user is CREATOR of the meipi
		if($_SESSION["meipi_creator"][$_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)]]==getIdUser())
			return true;
	}

	function getPermissionParamsFromRequest($request)
	{
		global $idMeipi;
		if(!canEditMeipimatic($idMeipi))
		{
			addError($aResult, getString("You can't edit this meipimatic"));
			return $aResult;
		}

		$aUserInfoParams = getUserInfoParamsFromRequest(Array("user" => $request["user"]));
		$aUserInfo = getUserInfo($aUserInfoParams);
		
		if(strlen($aUserInfo["user"]["id_user"])==0)
		{
			addError($aResult, getString("user not found"));
			return $aResult;
		}
		
		if((getIdUser()==$aUserInfo["user"]["id_user"])&&($request["permission"]=='-1'))
		{
			addError($aResult, getString("can't remove admin permission to your user"));
			return $aResult;
		}

		$idUser = $aUserInfo["user"]["id_user"];
		$permission = intval($request["permission"]);
		if($permission==1 || $permission==2 || $permission==-1 || $permission==-2)
		{
			$aResult["id_user"] = $idUser;
			$aResult["permission"] = $permission;
			$aResult["ok"] = TRUE;
			return $aResult;
		}
	}

	function modifyPermission($aParams)
	{
		global $idMeipi;
		$id = $_SESSION["id_meipi"][getMeipiOrAliasId($idMeipi)];
		
		if(!canEditMeipimatic($idMeipi) || !$aParams["ok"])
		{
			return FALSE;
		}
		
		$permission=intval($aParams["permission"]);
		$idUser=intval($aParams["id_user"]);

		//TODO: if($_SESSION["meipi_creator"][$_SESSION["id_meipi"][$idMeipi]]==$idUser)
		if($_SESSION["meipi_creator"][$id]==$idUser)
		{
			return FALSE;
		}

		$dbLink = dbConnect();
		if($permission<0)
		{
			$type = -$permission;
			$aResult = dbUpdate("DELETE FROM `".PERMISSION."` WHERE id_user='$idUser' AND type='$type' AND id_meipi='$id'", $dbLink);
		}
		else
		{
			$type = $permission;
			$aResult = dbUpdate("INSERT INTO `".PERMISSION."`(id_meipi, id_user, type) VALUES('$id', '$idUser', '$type')", $dbLink);
		}
		$aPermissions = dbSelect("SELECT * FROM ".PERMISSION." WHERE id_user='$idUser' AND type='$type' AND id_meipi='$id'", $dbLink);
		return !(($permission>0) ^ (dbGetSelectedRows($aPermissions)>0));
	}

	function isValidLatLon($lat, $lon)
	{
		return $lat>-5000 && $lon>-5000;
	}

	function getNewEntryForm()
	{
		global $minLat, $maxLat, $minLon, $maxLon, $commonFiles;
		require("templates/newEntryForm.php");
	}

	function getEntryWindow($request=null, $script=true)
	{
		if($script)
		{
?>
<script type="text/javascript">
	var extraParams = new Array();
	var aParam = null;
</script>
<?
		}

		require("templates/entryWindow.php");
	}

	function getMosaicNameWindow($request=null)
	{
		require("templates/mosaicNameWindow.php");
	}

	function getArchiveFromRequest($request)
	{
		$id_entry = intval($request["id_entry"]);
		$archiveComment = encode($request["comment"]);
		$archiveStatus = encode($request["status"]);

		if($id_entry<=0)
		{
			return ;
		}

		$aParams["id_entry"] = $id_entry;
		$aParams["status"] = $archiveStatus;
		$aParams["date"] = ($archiveStatus=="archived" ? "now()" : "date_add(now(), interval 30 day)");
		$aParams["comment"] = $archiveComment;
		$aParams["ok"] = TRUE;
		return $aParams;
	}

	function archive($aParams)
	{
		$idEntry = $aParams["id_entry"];

		if(!isLogged())
		{
			return getString("You need to log in");
		}
		if(!canEditEntry($idEntry))
		{
			return getString("You are not allowed to edit this entry");
		}

		global $extraTable, $archiveParam;
		if($extraTable=="true" && strlen($archiveParam)>0)
		{
			$dbLink = dbConnect();

			insertExtraParams($idEntry, null);

			$archiveStatus = $aParams["status"];
			global $archiveParamConfig;
			if($archiveStatus=="active" && strlen($archiveParamConfig)>0)
			{
				$aParams["id_entry"]=$idEntry;
				$aEntries = getEntries($aParams);

				parse_str($archiveParamConfig, $aArchiveConfig);
				if(strlen($aArchiveConfig["category_".$aEntries[0]["id_category"]])>0)
				{
					$archiveStatus = encode($aArchiveConfig["category_".$aEntries[0]["id_category"]]);
				}
			}
			$archiveDate = $aParams["date"];
			$archiveComment = $aParams["comment"];
			$set = "extra_$archiveParam='$archiveStatus', extra_".$archiveParam."_date=$archiveDate, extra_".$archiveParam."_comment='$archiveComment'";

			$rcExtra = dbUpdate("UPDATE `".EXTRA."` SET $set WHERE id_entry='$idEntry'", $dbLink);
		}
	}

?>
