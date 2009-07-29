<?
	require_once("db.php");
	require_once($configsPath."config/meipiConfig.php");
	require_once($configsPath."functions/general.php");
	
	require_once($configsPath.'functions/libs/recaptchalib.php');
	
	// Cache does not work for dynamic images if session is started
	if(!$skipSession)
	{
		session_start();

		// Compress output
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			ob_start("ob_gzhandler");
		else
			ob_start();
	}

	/*
	 *	loadSessionInfo
	 *	Load information into the user session
	 */
	function loadSessionInfo($request)
	{
		if(strlen($request["debug"])>0)
		{
			$_SESSION["debug"] = $request["debug"]=="true";
		}
		
		if($_SESSION["loaded"])
		{
			return;
		}

		$_SESSION["user_agent"]=$_SERVER["HTTP_USER_AGENT"];
	}
	loadSessionInfo($_REQUEST);
	
	function isDebugEnabled()
	{
		return $_SESSION["debug"];
	}

	function browserSupportsDraggable()
	{
		if(isset($_SESSION["browserSupportsDraggable"]))
			return $_SESSION["browserSupportsDraggable"];

		// iPod doesn't support Draggable elements
		if(stristr($_SESSION["user_agent"], "iPod") !== FALSE)
			$_SESSION["browserSupportsDraggable"] = FALSE;
		// all other browsers support Draggable elements
		else
			$_SESSION["browserSupportsDraggable"] = TRUE;
		return $_SESSION["browserSupportsDraggable"];
	}

	function getSubString($str,$numChars,$maxLength=-1)
	{
		$appendStr = "";
		$strlenOri = strlen($str);
		$numCharsOri = $numChars;

		if($numChars==-1 || $numChars>strlen($str))
		{
			$numChars = strlen($str);
		}

		if($maxLength>0)
		{
			$strMod = "";
			$cont = 0;
			$state = 0;
			for($i=0; $i<$numChars; $i++)
			{
				switch($state)
				{
					default:
					case 0:
						if($str{$i}=='&')
						{
							$cont++;
							$state = 1;
						}
						else if($str{$i}=='<')
						{
							$cont = 0;
							$state = 2;
						}
						else if($str{$i}==' ')
						{
							$cont = 0;
						}
						else
						{
							$cont++;
						}
						break;

					case 1: // '&' found
						if($str{$i}==';')
						{
							$state = 0;
						}
						break;

					case 2: // '<' found
						if($str{$i}=='>')
						{
							$state = 0;
						}
						break;
				}
				if($cont>$maxLength)
				{
					$cont = 0;
					$strMod .= ' ';
					$numChars++;
				}
				$strMod .= $str{$i};
			}
			$str = $strMod;
		}

		if(strlen($str)>$numChars || $strlenOri>$numChars)
		{
			$appendStr = "...";
		}

		$endChar = $numChars;
		for($i=min($numChars, strlen($str)); $i>$numChars-7 && $i>0; $i--)
		{
			if($str{$i}=='&' || $str{$i}=='<')
			{
				$endChar = $i;
				break;
			}
			if($str{$i}==';' || $str{$i}=='>')
			{
				break;
			}
		}

		$subStr = substr($str, 0, $endChar);
		$subStr .= $appendStr;

		return $subStr;
	}

	function getMeipimaticExtraConfig($idMeipi)
	{
		$sQuery = "SELECT name, description, type, value, config FROM meipi_params WHERE id_meipi='$idMeipi'";

		$dbLink = dbConnect();
		$aExtraConfig = dbSelect($sQuery, $dbLink);

		for($i=0; $i<count($aExtraConfig); $i++)
		{
			if($aExtraConfig[$i]["type"] == "special")
			{
				switch($aExtraConfig[$i]["value"])
				{
					case "archive":
						global $archiveParam, $archiveParamConfig;
						$archiveParam = $aExtraConfig[$i]["name"];
						$archiveParamConfig = $aExtraConfig[$i]["config"];
						break;
				}
			}
		}
		
		return $aExtraConfig;
	}

	function getLongDescription($idMeipi)
	{
		$sQuery = "SELECT long_description FROM meipi_description WHERE id_meipi='$idMeipi'";

		$dbLink = dbConnect();
		$longDescs = dbSelect($sQuery, $dbLink);

		return $longDescs[0]["long_description"];
	}

	function getMeipiUsers($meipiId)
	{
		$dbLink = dbConnect();
		$id = $_SESSION["id_meipi"][$meipiId];
		$aMeipiPermissions = dbSelect("SELECT ".PERMISSION.".id_user, type, login FROM ".PERMISSION.", ".USER." WHERE id_meipi='$id' AND ".PERMISSION.".id_user=".USER.".id_user", $dbLink);
		for($i=0; $i<dbGetSelectedRows($aMeipiPermissions); $i++)
		{
			$aResult[$aMeipiPermissions[$i]["type"]][$aMeipiPermissions[$i]["id_user"]] = $aMeipiPermissions[$i]["login"];
		}
		return $aResult;
	}

	function getOverEntry($type)
	{
		global $configsPath;
		global $commonFiles;

		switch($type)
		{
			case "meipi":
				$helpString = getString("helpStringMeipi1");
				$hasOverEntry = TRUE;
				break;
			case "meipi.org":
			case "map":
				$helpString = getString("helpStringMap1");
				$hasOverEntry = TRUE;
				break;
			case "mosaic":
				$helpString = getString("helpStringMosaic1");
				$hasOverEntry = TRUE;
				break;
			case "list":
				$helpString = getString("helpStringList1");
				$hasOverEntry = FALSE;
			break;
			case "categories":
				$helpString = getString("helpStringCategories1");
				$hasOverEntry = FALSE;
			break;
		}

		require($configsPath."templates/overEntry.php");

		if(browserSupportsDraggable())
		{
?>
	<script type="text/javascript" language="javascript">
		draggableEntry = new Draggable('overEntry');
	</script>
<?
		} // browserSupportsDraggable
	}

	define("MIN_LENGTH", 4); // also in strings_*
	define("MAX_LENGTH", 20); // also in strings_*

	function checkMeipiIdFormat($meipiId)
	{
		if(strlen($meipiId)<MIN_LENGTH || strlen($meipiId)>MAX_LENGTH)
		{
			return FALSE;
		}
		return eregi('^[[:alnum:]]*$', $meipiId);
	}

	function checkMeipiId($meipiId, $dbLink)
	{
		return checkMeipiIdFormat($meipiId);
	}

	function getMeipiOrAliasId($meipiId)
	{
		return $meipiId;
	}
	
	function getLoginFromRequest($request)
	{
		global $lang;
		
		//$aUser["errors"] = 0;
		$valid = true;
		$request["login"] = trim($request["login"]);
		if(strlen($request["login"])<=0)
		{
			$valid = false;
			addError($aUser, getString("Wrong Login"), "param_login");
		}
		else
			$submited = true;
		if(strlen($request["pwd1"])<=0)
		{
			$valid = false;
			addError($aUser, getString("Wrong password"), "param_pwd1");
		}
		else
			$submited = true;

		$aUser["login"] = $request["login"];
		$aUser["pwd"] = md5($request["pwd1"]);
		$aUser["lang"] = $lang;
		
		$aUser["ok"]=$valid;
		if(!$submited)
			return ;
		return $aUser;
	}

	function getUserFromRequest($request)
	{
		global $lang;
		
		//$aUser["errors"] = 0;
		$valid = true;
		$request["login"] = trim($request["login"]);
		if(strlen($request["login"])<=0)
		{
			$valid = false;
			addError($aUser, getString("Wrong Login"), "param_login");
		}
		else if(!checkValidChars($request["login"]))
		{
			$valid = false;
			addError($aUser, getString("Invalid characters"), "param_login");
		}
		else
			$submited = true;
		//if(strlen($request["web"])<=0)
		//{
		//	$valid = false;
		//	addError($aUser, getString("Wrong web"));
		//}
		if(strlen($request["mail"])<=0 || !isValidEmail($request["mail"]))
		{
			$valid = false;
			addError($aUser, getString("Wrong mail"), "param_mail");
		}
		else
			$submited = true;
		if(strlen($request["pwd1"])<=0 || strlen($request["pwd2"])<=0)
		{
			$valid = false;
			if(strlen($request["pwd1"])<=0)
				addError($aUser, getString("Wrong password"), "param_pwd1");
			if(strlen($request["pwd2"])<=0)
				addError($aUser, getString("Wrong password"), "param_pwd2");
		}
		else
		{
			$submited = true;
			if($request["pwd1"]!==$request["pwd2"])
			{
				$valid = false;
				addError($aUser, getString("Different passwords"), "param_pwd2");
			}
		}
		
		$aUser["login"] = $request["login"];
		$aUser["pwd"] = md5($request["pwd1"]);
		$aUser["web"] = $request["web"];
		$aUser["mail"] = $request["mail"];
		$aUser["lang"] = $lang;
		
		$aUser["ok"]=$valid;
		if(!$valid && !$submited)
			return ;
		return $aUser;
	}

	function registerUser($aUser,$validationsOk = true)
	{
		// $aResult["errors"] = 0;
		
		if(!$aUser["ok"])
		{
			addError($aResult, getString("Invalid User"), "param_login");
			return $aResult;
		}
		
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aResult, getString("Unable to connect to database"));
			return $aResult;
		}

		$login = strtolower(sqlEscape($aUser["login"], $dbLink));
		$pwd = sqlEscape($aUser["pwd"], $dbLink);
		$web = sqlEscape($aUser["web"], $dbLink);
		$mail = sqlEscape($aUser["mail"], $dbLink);
		$lang = sqlEscape($aUser["lang"], $dbLink);
		
		if ($validationsOk) // TODO: Check always; register only if validations ok
		{
			$rc = dbUpdate("INSERT INTO `".USER."`(login, password, web, mail, date, language) VALUES('$login', '$pwd', '$web', '$mail', now(), '$lang')", $dbLink);
			if($rc===TRUE)
			{
				// Add new user to stats
				dbUpdate("UPDATE meipi_global_stats SET users=users+1 LIMIT 1", $dbLink);

				$rcLogin = login($aUser);
				if($rcLogin["ok"])
					$aResult["ok"] = true;
			}
			else
			{
				// TODO: check before trying to update
				addError($aResult, getString("Username already registered"));
			}
		}

		//dbDisconnect($dbLink);
		return $aResult;
	}

	function login($aUser)
	{
		// $aResult["errors"] = 0;

		if(!$aUser["ok"])
		{
			addError($aResult, getString("Invalid User"));
			return $aResult;
		}

		$dbLink = dbConnect();
		if($dbLink==null)
		{
			addError($aResult, getString("Unable to connect to database"));
			return $aResult;
		}

		$login = strtolower(sqlEscape($aUser["login"], $dbLink));
		$pwd = sqlEscape($aUser["pwd"], $dbLink);

		$result = dbSelect("SELECT id_user, web, mail, language FROM ".USER." WHERE login='$login' AND password='$pwd' LIMIT 1", $dbLink);

		if(count($result)==1)
		{
			$aLoggedUser["login"] = $aUser["login"];
			$aLoggedUser["id_user"] = $result[0]["id_user"];
			$aLoggedUser["web"] = $result[0]["web"];
			$aLoggedUser["mail"] = $result[0]["mail"];
			$aLoggedUser["lang"] = $result[0]["language"];
			
			$_SESSION["loggedUser"] = $aLoggedUser;

			$aResult["ok"] = true;
		}
		else
		{
			addError($aResult, getString("Wrong login information"));
		}

		if($aResult["ok"])
		{
			$aPermissions = dbSelect("SELECT type FROM ".PERMISSION." WHERE id_user='".getIdUser()."'", $dbLink);
			for($iPermission=0; $iPermission<count($aPermissions); $iPermission++)
			{
				$_SESSION["permission"][getIdUser()][$aPermissions[$iPermission]["id_meipi"]][$aPermissions[$iPermission]["type"]]=TRUE;
			}
			
			// Update user language if it is not set on user table
			if(strlen($aUser["lang"])>0 && strlen($aLoggedUser["lang"])==0)
			{
				$lang = sqlEscape($aUser["lang"], $dbLink);
				$id_user = $aLoggedUser["id_user"];
				$rc = dbUpdate("UPDATE `".USER."` SET language='$lang' WHERE id_user='$id_user' LIMIT 1", $dbLink);
				$_SESSION["loggedUser"]["lang"] = $lang;
			}
		}

		//dbDisconnect($dbLink);
		return $aResult;
	}

	function logout()
	{
		session_destroy();
		//$_SESSION["loggedUser"] = null;
		//$_SESSION["meipiPasswords"] = null;
	}

	function isLogged()
	{
		return isset($_SESSION["loggedUser"]) && isset($_SESSION["loggedUser"]["login"]) && strlen($_SESSION["loggedUser"]["login"])>0;
	}
	
	function getLogin()
	{
		return $_SESSION["loggedUser"]["login"];
	}
	
	function getIdUser()
	{
		return $_SESSION["loggedUser"]["id_user"];
	}
	
	function addError(&$a, $sError, $parameter="foo")
	{
		$iErrors = $a["errors"]+1;
		$a["error_".$iErrors] = $sError;
		$a["errors"] = $iErrors;
		$a["parameters"][$parameter] = TRUE;
	}
	
	function getUser($id_user)
	{
		$dbLink = dbConnect();
		if($dbLink==null)
		{
			return ;
		}
		$aUser = dbSelect("SELECT login FROM ".USER." WHERE id_user = '".sqlEscape($id_user, $dbLink)."'", $dbLink);
		return $aUser[0]["login"];
	}

	function sqlEscape($str, $dbLink)
	{
		if(get_magic_quotes_gpc()>0)
			return mysql_real_escape_string(stripslashes($str), $dbLink);
		return mysql_real_escape_string($str, $dbLink);
	}

	function showLanguageApologies($lang)
	{
		return "<p>[".getString("not translated")."]</p>";
	}

	function getResetPasswordParamsFromRequest($request)
	{
		$login = $request["reset_password_login"];
		$code = $request["code"];
		$pwd1 = $request["pwd1"];
		$pwd2 = $request["pwd2"];

		if(strlen($login)>0 && strlen($code)>0 && strlen($pwd1)>0 && $pwd1==$pwd2)
		{
			$aParams["login"] = $login;
			$aParams["code"] = $code;
			$aParams["pwd"] = md5($pwd1);
			$aParams["ok"] = TRUE;
			return $aParams;
		}
	}
	
	function resetPassword($aParams)
	{
		$dbLink = dbConnect();
		$login = strtolower(sqlEscape($aParams["login"], $dbLink));
		$code = strtolower(sqlEscape($aParams["code"], $dbLink));
		$pwd = strtolower(sqlEscape($aParams["pwd"], $dbLink));

		$aCode = dbSelect("SELECT ".USER.".id_user AS id_user FROM meipi_reset_password, ".USER." WHERE meipi_reset_password.id_user=".USER.".id_user AND code='$code' AND ".USER.".login='$login'", $dbLink);
		if(dbGetSelectedRows($aCode)>0)
		{
			$id_user = $aCode[0]["id_user"];
			$rc = dbUpdate("UPDATE `".USER."` SET password='$pwd' WHERE id_user='$id_user' LIMIT 1", $dbLink);
		}

		return $rc>0;
	}
	
	function getSendPasswordParamsFromRequest($request)
	{
		$login = $request["login"];

		if(strlen($login)>0)
		{
			$aParams["login"] = $login;
			$aParams["ok"] = TRUE;
			return $aParams;
		}
	}
	
	function sendPasswordCode($aParams)
	{
		$login = $aParams["login"];

		if(strlen($login)==0)
		{
			return;
		}
		$dbLink = dbConnect();
		$login = strtolower(sqlEscape($login, $dbLink));
		
		// get user mail
		$aUser = dbSelect("SELECT id_user, mail FROM ".USER." WHERE login='$login' LIMIT 1");

		if(dbGetSelectedRows($aUser)>0)
		{
			$mail = $aUser[0]["mail"];
			$id = $aUser[0]["id_user"];
		}

		// TODO: check too many mails?

		// delete invalid codes
		$rc = dbUpdate("DELETE FROM `meipi_reset_password` WHERE valid_until<now()", $dbLink);

		// generate code
		$code = substr(md5(rand()), 0, 16);

		// insert code
		$rc = dbUpdate("INSERT INTO `meipi_reset_password`(id_user, code, valid_until) VALUES('$id', '$code', date_add(now(), interval 1 day))", $dbLink);

		if(isValidEmail($mail))
		{
			// send mail
			$subject = html_entity_decode(getString("pwd_recovery_subject"),ENT_QUOTES,"cp1252");
			$body = html_entity_decode(getString("pwd_recovery_body_1"),ENT_QUOTES,"cp1252");
			$body .= $code."\r\n";
			$body .= html_entity_decode(getString("pwd_recovery_body_2"),ENT_QUOTES,"cp1252");
//			$body .= "http://www.meipi.org/actions/resetPassword.php\r\n";
			$body .= html_entity_decode(getString("pwd_recovery_body_3"),ENT_QUOTES,"cp1252")."\r\n";
			$headers = "From: ".$senderName." <".$senderMail.">\r\n";
			mail($mail,$subject,$body,$headers);
			$body .= "usuario: ".$id.", mail: ".$mail."\r\n";
			mail($senderMail,$subject,$body,$headers); // TODO: Send this?
			return getString("password sent");
		}
		else
		{
			return getString("invalid email");
		}
	}

	function getCancelMailSubscriptionParamsFromRequest($request)
	{
		$login = $request["login"];
		$code = $request["code"];
		$mail = $request["mail"];

		if(strlen($login)>0 && strlen($code)>0 && strlen($mail)>0)
		{
			$aParams["login"] = $login;
			$aParams["code"] = $code;
			$aParams["mail"] = $mail;
			$aParams["ok"] = TRUE;
			return $aParams;
		}
	}
	
	function cancelMailSubscription($aParams)
	{
		$dbLink = dbConnect();
		$login = strtolower(sqlEscape($aParams["login"], $dbLink));
		$code = strtolower(sqlEscape($aParams["code"], $dbLink));
		$mail = strtolower(sqlEscape($aParams["mail"], $dbLink));

		$genCode = md5($login."code".$mail);

		if($genCode==$code)
		{
			$aUser = dbSelect("SELECT id_user FROM ".USER." WHERE login='$login' AND mail='$mail'", $dbLink);
			if(dbGetSelectedRows($aUser)>0)
			{
				$id_user = $aUser[0]["id_user"];
				$rc = dbUpdate("UPDATE `".USER."` SET mail_subscription='0' WHERE id_user='$id_user' LIMIT 1", $dbLink);
				//$rc = "UPDATE `".USER."` SET mail_subscription='0' WHERE id_user='$id_user' LIMIT 1";
			}
			else return false;//"Error: Login and mail mismatch";
		}
		else return false;//"Error: Invalid code";

		return $rc;
	}
	
	function decode($str)
	{
		$str = html_entity_decode($str);
		return $str;
	}

	function safeForJavascript($str)
	{
		if($str[strlen($str)-1]=='\\')
			return $str." ";
		return $str;
	}

	function escapeQuotes($str)
	{
		return safeForJavascript(str_replace("&#039;", "\'", str_replace("'", "\'", $str)));
	}

	function removeQuotes($str)
	{
		$str = str_replace('"', "", $str);
		$str = str_replace("'", "", $str);
		return safeForJavascript($str);
	}
	
	function getPageNumbers($iRows,$actualPage,$basePage)
	{
		global $pageSize;
		global $pagesLinks;
		global $sArguments;

		$iPages = ceil($iRows / $pageSize); 

		if ($actualPage == "1")
			echo "<span class=\"disabled\">&lt;&lt; ".getString("Previous")."</span> ";
		else
		{
			$prevPage = $actualPage - 1;
			echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $prevPage, "open_entry" => ""))."\">&lt;&lt; ".getString("Previous")."</a> ";
		}

		if ($iPages<=4*$pagesLinks+3)
		{
			for ($iPage=1;$iPage<=$iPages;$iPage++)
			{
				if ($actualPage == $iPage)
					echo "<span class=\"current\">".$iPage."</span> ";
				else
				{
					$sArgumentsPage = $sArguments.$iPage;
					echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage, "open_entry" => ""))."\">".$iPage."</a> ";
				}
			}
		}
		else
		{
			if ($actualPage<=2+$pagesLinks*2)
			{
				for ($iPage=1;$iPage<=$actualPage+$pagesLinks;$iPage++)
				{
					if ($actualPage == $iPage)
						echo "<span class=\"current\">".$iPage."</span> ";
					else
					{
						echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
					}
				}
				echo " ... ";
				for ($iPage=$iPages-$pagesLinks;$iPage<=$iPages;$iPage++)
				{
					echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
				}
			}
			elseif ($actualPage>=$iPages-1-$pagesLinks*2)
			{
				for ($iPage=1;$iPage<=$pagesLinks+1;$iPage++)
				{
					echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
				}
				echo " ... ";
				for ($iPage=$actualPage-$pagesLinks;$iPage<=$iPages;$iPage++)
				{
					if ($actualPage == $iPage)
						echo "<span class=\"current\">".$iPage."</span> ";
					else
					{
						echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
					}
				}
			}
			else
			{
				for ($iPage=1;$iPage<=$pagesLinks+1;$iPage++)
				{
					echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
				}
				echo " ... ";
				for ($iPage=$actualPage-$pagesLinks;$iPage<=$actualPage+$pagesLinks;$iPage++)
				{
					if ($actualPage == $iPage)
						echo "<span class=\"current\">".$iPage."</span> ";
					else
					{
						echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
					}
				}
				echo " ... ";
				for ($iPage=$iPages-$pagesLinks;$iPage<=$iPages;$iPage++)
				{
					echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $iPage))."\">".$iPage."</a> ";
				}
			}
		}

		if ($actualPage >= $iPages)
			echo "<span class=\"disabled\">".getString("Next")." &gt;&gt;</span> ";
		else
		{
			$nextPage = $actualPage + 1;
			echo "<a href=\"".setParams($basePage."?".$_SERVER[QUERY_STRING], Array("page" => $nextPage, "open_entry" => ""))."\">".getString("Next")." &gt;&gt;</a> ";
		}

	}

	function setParam($url, $name, $value)
	{
		if(strpos($url, "?")===FALSE)
		{
			$url .= "?";
		}

		$url = ereg_replace("(&)?".$name."=[[:alnum:]_%25 <>/@\.;]*", "", $url);
		if(strlen($value)>0)
		{
			$value = str_replace("&", "%26", $value);
			$url .= "&".$name."=".$value;
		}
		$url = str_replace("?&", "?", $url);
		$url = str_replace("(\r)?\n", "", $url);
		if($url[strlen($url)-1]=="?")
			$url = substr($url, 0, strlen($url)-1);
		return $url;
	}

	function setParams($url, $params)
	{
		global $idMeipi;

		// Remove message to avoid repeating it
		$url = setParam($url, "msg", "");
		$url = setParam($url, "id_meipi", "");
		$url = setParam($url, "identifier_meipi", "");

		if($params!=null && count($params)>0)
		{
			foreach($params as $key => $value)
			{
				$url = setParam($url, $key, $value);
			}
		}
		if(strlen($idMeipi)>0)
		{
			//$url = $idMeipi.".".$url;
		}
		return $url;
	}

	function getCommonHeader($commonHeaderTitle)
	{
		require("templates/commonHeader.php");
	}
	function getCommonFooter()
	{
		require("templates/commonFooter.php");
	}

	function getLoginForm($request)
	{
		global $configsPath;
		require($configsPath."templates/loginForm.php");
	}
	function getHead()
	{
		global $configsPath;
		require($configsPath."templates/head.php");
	}

	function getMessageWindow()
	{
		global $configsPath;
		require($configsPath."templates/messageWindow.php");
	}

	function getStatisticsScript()
	{
		global $statistics;
		return $statistics;
	}

	function getMessages($read=TRUE)
	{
		global $dateFormat;

		if(!isLogged())
		{
			return;
		}
		
		$idUser = getIdUser();

		$dbLink = dbConnect();
		$aMessages = dbSelect("SELECT *, DATE_FORMAT(messages.date, '$dateFormat') AS dateFormatted FROM ".MESSAGES." messages WHERE messages.to='$idUser' ".($read ? "" : "AND messages.read=0")." ORDER BY messages.read, messages.date DESC", $dbLink);

		return $aMessages;
	}
	
	function markMessagesAsRead()
	{
		$idUser = getIdUser();

		$dbLink = dbConnect();
	 	dbUpdate("UPDATE ".MESSAGES." messages SET messages.read=1 WHERE messages.to='$idUser'", $dbLink);
	}

	function getMessageFromRequest($request, $to)
	{
		if($request["send"]!="true")
		{
			return;
		}
		
		if(!isLogged())
		{
			addError($aMessage, getString("Sorry, you're not logged in!"), "param_login");
			return $aMessage;
		}

		$valid = TRUE;

		if(strlen($to)<=0 || $to!=intval($to))
		{
			addError($aMessage, getString("Invalid recipient"), "param_to");
			$valid = FALSE;
		}
		
		$messageText = encode($request["message"]);
		if(strlen($messageText)==0)
		{
			addError($aMessage, getString("Empty message"), "param_message");
			$valid = FALSE;
		}
		//else if(strlen($messageText)>255)
		//{
			//addError($aMessage, getString("Message too long"), "param_message");
			//$valid = FALSE;
		//}

		$aMessage["message"] = $messageText;
		$aMessage["to"] = $to;
		$aMessage["from"] = getIdUser();
		$aMessage["valid"] = $valid;

		return $aMessage;
	}

	function sendMessage($aMessage)
	{
		$from = $aMessage["from"];
		$to = $aMessage["to"];
		$messageText = $aMessage["message"];

		$dbLink = dbConnect();
	 	dbUpdate("INSERT INTO ".MESSAGES."(meipi_messages.from, meipi_messages.to, meipi_messages.date, meipi_messages.read, message) VALUES('$from', '$to', now(), 0, '$messageText')", $dbLink);

		$mailAddress = getMailAddress($to);
		if(strlen($mailAddress)>0)
		{
			$fromUser = getUser($from);

			$body = getString("The following user sent you a message at meipi.org:");
			$body .= " <a href=\"http://www.meipi.org".getProfilePage($from, $fromUser)."\">$fromUser</a>";
			$body .= "\r\n\r\n";
			$body .= getString("Here is the message:");
			$body .= "<hr/>";
			$body .= "\r\n\r\n";
			$body .= $messageText;
			$body .= "\r\n\r\n";
			$body .= "<hr/>";

			sendEmail($mailAddress, getString("Message from Meipi.org user").": ".$fromUser, $body, "no-reply@meipi.org", "Meipi user", "", "");
		}
	}

	function getDeleteMessageFromRequest($request)
	{
		if(!isLogged())
		{
			return;
		}

		if($request["delete"]!="true")
		{
			return;
		}

		$idMsg = intval($_REQUEST["msg_id"]);
		$idUser = getIdUser();

		$aDelete["msg_id"] = $idMsg;
		$aDelete["user_id"] = $idUser;
		$aDelete["valid"] = TRUE;
		return $aDelete;
	}

	function deleteMessage($aDelete)
	{
		$idMsg = intval($aDelete["msg_id"]);
		$idUser = intval($aDelete["user_id"]);
		
		$dbLink = dbConnect();
	 	dbUpdate("DELETE FROM ".MESSAGES." WHERE ".MESSAGES.".to=$idUser AND id_message=$idMsg LIMIT 1", $dbLink);
	}

	/**
	 * Return the mail address of the user if it's valid AND user wants to receive mails, "" otherwise
	 */
	function getMailAddress($idUser)
	{
		$aUserInfoParams["id_user"] = intval($idUser);
		$aUserInfo = getUserInfo($aUserInfoParams);
		if($aUserInfo["user"]["mail_subscription"] == 1)
		{
			if(isValidEmail($aUserInfo["user"]["mail"]))
			{
				return $aUserInfo["user"]["mail"];
			}
		}
		return "";
	}
?>
